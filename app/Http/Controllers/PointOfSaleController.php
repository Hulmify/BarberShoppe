<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BookingItem; // Assuming this model exists based on context
use Illuminate\Http\Request;
use Carbon\Carbon;

class PointOfSaleController extends Controller
{
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
        
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'service_ids' => 'required|array|min:1',
            'customer_type' => 'required|in:existing,new',
            'customer_id' => 'required_if:customer_type,existing|nullable|exists:customers,id',
            'new_customer_name' => 'required_if:customer_type,new|nullable|string|max:255',
            'new_customer_email' => 'nullable|email|max:255', 
            'new_customer_phone' => 'required_if:customer_type,new|nullable|string|max:20',
            'stylist_id' => 'nullable|exists:stylists,id',
        ]);

        // 1. Resolve Customer
        if ($request->customer_type === 'new') {
            $customer = Customer::updateOrCreate(
                ['phone' => $request->new_customer_phone],
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
        $startDateTime = Carbon::parse($request->date . ' ' . $request->time, $tz);

        if ($startDateTime->isPast()) {
            return back()->withErrors(['date' => 'Cannot create reservations in the past.'])->withInput();
        }

        // Check if shop is closed for the requested date (Temporary Toggle)
        if ($shop->off_date && Carbon::parse($shop->off_date)->isSameDay($startDateTime)) {
            return back()->withErrors(['date' => 'The shop is marked as OFF for today. Toggle it ON in the dashboard to allow bookings.'])->withInput();
        }

        $endDateTime = $startDateTime->copy()->addMinutes($totalDuration);

        // Fetch active stylists with their availability for this day
        $dayOfWeek = $startDateTime->dayOfWeek;
        $activeStylists = $shop->stylists()
            ->where('is_active', true)
            ->with(['availabilities' => function($q) use ($dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek)->where('is_active', true);
            }])
            ->get();

        // Check for overlapping bookings
        $overlappingBookings = $shop->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('start_time', [$startDateTime, $endDateTime])
                  ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                  ->orWhere(function ($q2) use ($startDateTime, $endDateTime) {
                      $q2->where('start_time', '<=', $startDateTime)
                         ->where('end_time', '>=', $endDateTime);
                  });
            })->get();

        // Filter stylists who are working AND not busy
        $availableStylists = $activeStylists->filter(function($s) use ($startDateTime, $endDateTime, $overlappingBookings, $tz) {
            // Check if stylist is busy
            if ($overlappingBookings->contains('stylist_id', $s->id)) return false;

            // Check if stylist is working at this time
            $sAvail = $s->availabilities->first();
            if (!$sAvail) return false;

            $sStart = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $sAvail->start_time, $tz);
            $sEnd = Carbon::parse($startDateTime->format('Y-m-d') . ' ' . $sAvail->end_time, $tz);

            return $startDateTime->gte($sStart) && $endDateTime->lte($sEnd);
        });

        $selectedStylistId = $request->stylist_id;
        if ($selectedStylistId) {
            if (!$availableStylists->contains('id', $selectedStylistId)) {
                return back()->withErrors(['stylist_id' => 'Requested stylist is busy or not working at this time.'])->withInput();
            }
        } else {
            if ($availableStylists->isEmpty()) {
                return back()->withErrors(['time' => 'No stylists available at this time.'])->withInput();
            }
            // Assign a random free stylist
            $selectedStylistId = $availableStylists->random()->id;
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
}
