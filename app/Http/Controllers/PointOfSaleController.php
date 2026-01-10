<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BookingItem; // Assuming this model exists based on context
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\PhoneNumberService;
use App\Rules\Phone;

class PointOfSaleController extends Controller
{
    protected $phoneService;

    public function __construct(PhoneNumberService $phoneService)
    {
        $this->phoneService = $phoneService;
    }

    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        
        // Get existing customers
        $customers = Customer::whereHas('bookings', function($q) use ($shop) {
            $q->where('shop_id', $shop->id);
        })->orderBy('name')->get();
        
        // Get shop services with search and pagination
        $search = $request->input('search');
        $servicesQuery = $shop->services();
        
        if ($search) {
            $servicesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        $services = $servicesQuery->paginate(10)->withQueryString();
        
        $stylists = $shop->stylists()->where('is_active', true)->get();
        
        return view('admin.pos.index', compact('customers', 'services', 'search', 'stylists'));
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;
        $tz = $shop->timezone ?? config('app.timezone');
        
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'service_ids' => 'required|array|min:1',
            'customer_type' => 'required|in:existing,new',
            'customer_id' => 'required_if:customer_type,existing|nullable|exists:customers,id',
            'new_customer_name' => 'required_if:customer_type,new|nullable|string|max:255',
            'new_customer_email' => 'nullable|email|max:255', 
            'new_customer_phone' => ['required_if:customer_type,new', 'nullable', 'string', 'max:20', new Phone($tz)],
            'stylist_id' => 'nullable|exists:stylists,id',
        ]);

        // 1. Resolve Customer
        if ($request->customer_type === 'new') {
            $normalizedPhone = $this->phoneService->formatE164($request->new_customer_phone, $tz);

            $customer = Customer::updateOrCreate(
                ['phone' => $normalizedPhone],
                ['name' => $request->new_customer_name, 'email' => $request->new_customer_email] 
            );
        } else {
            $customer = Customer::findOrFail($request->customer_id);
        }

        // 2. Resolve Stylist and Timing
        $services = Service::whereIn('id', $request->service_ids)->get();
        $totalPrice = $services->sum('price');
        $totalDuration = $services->sum('duration_minutes');
        
        $tz = $shop->timezone ?? config('app.timezone');
        $startDateTime = Carbon::parse($request->date . ' ' . $request->time, $tz)->setTimezone('UTC');
        $endDateTime = $startDateTime->copy()->addMinutes($totalDuration);

        $selectedStylistId = $request->stylist_id;
        
        if (!$selectedStylistId) {
            // Assign a random active stylist if none selected, or the first one found
            $anyStylist = $shop->stylists()->where('is_active', true)->first();
            $selectedStylistId = $anyStylist ? $anyStylist->id : null;
        }

        $booking = Booking::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'total_price' => $totalPrice,
            'status' => 'confirmed', 
            'stylist_id' => $selectedStylistId,
        ]);

        // 3. Attach Items
        foreach ($services as $service) {
            BookingItem::create([
                'booking_id' => $booking->id,
                'service_id' => $service->id,
                'price' => $service->price
            ]);
        }

        return redirect()->route('admin.appointments.index')->with('success', 'Reservation created successfully.');
    }
    public function slots(Request $request)
    {
        $shop = auth()->user()->shop;
        $tz = $shop->timezone ?? config('app.timezone');
        $date = $request->input('date');
        $duration = (int) $request->input('duration', 30);
        $stylistId = $request->input('stylist_id');

        // Parse bounds in Shop Time
        $start = Carbon::parse($date . ' 00:00:00', $tz);
        $limit = Carbon::parse($date . ' 23:45:00', $tz);
        
        // Fetch Bookings for the day (coverage check)
        // Convert day bounds to UTC for DB query
        $dayStartUtc = $start->copy()->setTimezone('UTC');
        $dayEndUtc = $start->copy()->endOfDay()->setTimezone('UTC'); // Full day coverage

        $bookingsQuery = Booking::where('shop_id', $shop->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($dayStartUtc, $dayEndUtc) {
                // Overlap with the day
                $q->where('start_time', '<', $dayEndUtc)
                  ->where('end_time', '>', $dayStartUtc);
            });

        if ($stylistId) {
            $bookingsQuery->where('stylist_id', $stylistId);
        }

        $bookings = $bookingsQuery->get();

        $slots = [];
        while ($start->lte($limit)) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addMinutes($duration);
            
            // Convert slot to UTC for accurate comparison
            $slotStartUtc = $slotStart->copy()->setTimezone('UTC');
            $slotEndUtc = $slotEnd->copy()->setTimezone('UTC');

            $isOccupied = false;
            foreach ($bookings as $booking) {
                // Check Overlap: (StartA < EndB) && (EndA > StartB)
                if ($slotStartUtc->lt($booking->end_time) && $slotEndUtc->gt($booking->start_time)) {
                    $isOccupied = true;
                    break;
                }
            }

            $slots[] = [
                'time' => $start->format('H:i'),
                'occupied' => $isOccupied,
            ];
            
            $start->addMinutes(15);
        }
        
        return response()->json(['slots' => $slots]);
    }
}
