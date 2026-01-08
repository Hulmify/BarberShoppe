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
                ['name' => $request->new_customer_name, 'email' => $request->new_customer_email, 'password' => bcrypt('password')] 
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

        // Check if shop is closed for the requested date (Temporary Toggle)
        if ($shop->off_date && Carbon::parse($shop->off_date)->isSameDay($startDateTime)) {
            return back()->withErrors(['date' => 'The shop is marked as OFF for today. Toggle it ON in the dashboard to allow bookings.'])->withInput();
        }

        // Check if shop is active for this day of the week (Regular Schedule)
        $dayOff = !$shop->availabilities()->where('day_of_week', $startDateTime->dayOfWeek)->where('is_active', true)->exists();
        if ($dayOff) {
            return back()->withErrors(['date' => 'The shop is closed on this day of the week according to your schedule.'])->withInput();
        }

        $endDateTime = $startDateTime->copy()->addMinutes($totalDuration);

        $selectedStylistId = $request->stylist_id;
        if (!$selectedStylistId) {
            // Auto-assign first free stylist if no preference
            $overlappingBookings = $shop->bookings()
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->whereBetween('start_time', [$startDateTime, $endDateTime])
                      ->orWhereBetween('end_time', [$startDateTime, $endDateTime]);
                })->pluck('stylist_id')->toArray();
            
            $freeStylist = $shop->stylists()->where('is_active', true)->whereNotIn('id', $overlappingBookings)->first();
            $selectedStylistId = $freeStylist ? $freeStylist->id : $shop->stylists()->where('is_active', true)->first()?->id;
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
