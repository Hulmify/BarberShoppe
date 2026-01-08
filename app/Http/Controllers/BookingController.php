<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use Carbon\Carbon;

class BookingController extends Controller
{
    private function getShop(Request $request, $slug = null)
    {
        if ($request->attributes->has('shop')) {
            return $request->attributes->get('shop');
        }
        return Shop::where('slug', $slug)->firstOrFail();
    }

    public function index(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        
        // Get services with search and pagination
        $search = $request->input('search');
        $servicesQuery = $shop->services();
        
        if ($search) {
            $servicesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        $services = $servicesQuery->paginate(10)->withQueryString();
        
        // Get active stylists
        $stylists = $shop->stylists()->where('is_active', true)->get();
        
        return view('booking.index', compact('shop', 'services', 'search', 'stylists'));
    }

    public function slots(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        
        // Validate date
        $request->validate(['date' => 'required|date']);
        $tz = $shop->timezone ?? config('app.timezone');
        $date = Carbon::parse($request->date, $tz)->startOfDay();
        $today = Carbon::now($tz)->startOfDay();

        if ($date->lt($today)) {
            return response()->json(['slots' => []]);
        }

        // Check if shop is closed for the requested date
        if ($shop->off_date && Carbon::parse($shop->off_date)->startOfDay()->equalTo($date)) {
            return response()->json(['slots' => [], 'message' => 'Shop is closed today.']);
        }
        
        // Get Availability for Day of Week
        $dayOfWeek = $date->dayOfWeek; // 0=Sun
        
        $avail = $shop->availabilities()->where('day_of_week', $dayOfWeek)->first();
        
        if (!$avail || !$avail->is_active) {
            return response()->json(['slots' => []]);
        }

        // Generate Slots
        $duration = (int) $request->input('duration', 30); // minutes
        $stylistId = $request->input('stylist_id'); // Optional stylist filter
        
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $avail->start_time, $tz);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $avail->end_time, $tz);
        
        // Get existing bookings
        $allBookings = $shop->bookings()
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get();
            
        $activeStylistsCount = $shop->stylists()->where('is_active', true)->count();
        if ($activeStylistsCount === 0) $activeStylistsCount = 1; // Fallback to 1 if no stylists set up yet

        $slots = [];
        
        $now = Carbon::now($tz);
        
        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $start->copy()->addMinutes($duration);
            
            // Skip if slot start time has already passed
            if ($start->lt($now)) {
                $start->addMinutes(15);
                continue;
            }

            // Filter bookings that overlap with this slot
            $overlappingBookings = $allBookings->filter(function ($b) use ($start, $slotEnd) {
                return $start->lt($b->end_time) && $slotEnd->gt($b->start_time);
            });

            if ($stylistId) {
                // Specific stylist requested: available if they specifically aren't busy
                $isAvailable = !$overlappingBookings->contains('stylist_id', $stylistId);
            } else {
                // No preference: available if at least one stylist is free
                // Note: This logic assumes each booking occupies exactly 1 stylist
                $isAvailable = $overlappingBookings->count() < $activeStylistsCount;
            }
            
            if ($isAvailable) {
                $slots[] = $start->format('H:i');
            }
            
            $start->addMinutes(15); 
        }
        
        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        
        $validated = $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'customer_name' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string',
            'stylist_id' => 'nullable|exists:stylists,id',
        ]);

        // Check if customer already has an active booking in this shop
        $existingCustomer = Customer::where('phone', $validated['customer_phone'])->first();
        if ($existingCustomer) {
            $activeBooking = Booking::where('shop_id', $shop->id)
                ->where('customer_id', $existingCustomer->id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->first();

            if ($activeBooking) {
                return response()->json([
                    'success' => false, 
                    'message' => 'You already have an active appointment. You can only book another one after your current appointment is completed or cancelled.'
                ], 422);
            }
        }

        // Calculate Totals
        $services = $shop->services()->whereIn('id', $validated['service_ids'])->get();
        $totalPrice = $services->sum('price');
        $totalDuration = $services->sum('duration_minutes');
        
        // Time
        $tz = $shop->timezone ?? config('app.timezone');
        $startTime = Carbon::parse($validated['date'] . ' ' . $validated['time'], $tz);
        
        if ($startTime->isPast()) {
            return response()->json(['success' => false, 'message' => 'Cannot book appointments in the past'], 422);
        }

        // Check if shop is closed for the requested date (Temporary Toggle)
        if ($shop->off_date && Carbon::parse($shop->off_date)->isSameDay($startTime)) {
            return response()->json(['success' => false, 'message' => 'Shop is closed today.'], 422);
        }

        // Check if shop is active for this day of the week (Regular Schedule)
        $dayOff = !$shop->availabilities()->where('day_of_week', $startTime->dayOfWeek)->where('is_active', true)->exists();
        if ($dayOff) {
            return response()->json(['success' => false, 'message' => 'Shop is not accepting bookings for this day.'], 422);
        }

        $endTime = $startTime->copy()->addMinutes($totalDuration);
        
        // Double Check Availability (Concurrency)
        $overlappingBookings = $shop->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })->get();

        $selectedStylistId = $validated['stylist_id'] ?? null;
        $activeStylists = $shop->stylists()->where('is_active', true)->get();
        
        if ($selectedStylistId) {
            // Check if specific stylist is busy
            if ($overlappingBookings->contains('stylist_id', $selectedStylistId)) {
                return response()->json(['success' => false, 'message' => 'Requested stylist is busy at this time'], 422);
            }
        } else {
            // No preference: auto-assign a free stylist
            $busyStylistIds = $overlappingBookings->pluck('stylist_id')->filter()->toArray();
            $freeStylists = $activeStylists->whereNotIn('id', $busyStylistIds);

            if ($freeStylists->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No stylists available at this time'], 422);
            }
            // Assign a random free stylist
            $selectedStylistId = $freeStylists->random()->id;
        }
        
        // Create Customer
        $customer = Customer::updateOrCreate(
            ['phone' => $validated['customer_phone']],
            ['name' => $validated['customer_name'], 'email' => $validated['customer_email']]
        );

        // Create Booking
        $booking = Booking::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'stylist_id' => $selectedStylistId,
        ]);

        // Items
        foreach ($services as $svc) {
            BookingItem::create([
                'booking_id' => $booking->id,
                'service_id' => $svc->id,
                'price' => $svc->price
            ]);
        }
        
        return response()->json(['success' => true, 'booking_id' => $booking->id]);
    }

    public function myAppointments(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        $phone = $request->query('phone');
        $bookings = collect();

        if ($phone) {
            $customer = Customer::where('phone', $phone)->first();
            if ($customer) {
                $bookings = Booking::where('shop_id', $shop->id)
                    ->where('customer_id', $customer->id)
                    ->with(['items.service', 'stylist'])
                    ->orderBy('start_time', 'desc')
                    ->get();
            }
        }

        return view('booking.my_appointments', compact('shop', 'bookings', 'phone'));
    }

    public function searchAppointments(Request $request, $slug = null)
    {
        $request->validate(['phone' => 'required|string']);
        
        // Handle both domain-based and slug-based routing
        if ($request->attributes->has('shop')) {
            return redirect()->route('shop.my_appointments', ['phone' => $request->phone]);
        }
        
        return redirect()->route('booking.my_appointments', ['slug' => $slug, 'phone' => $request->phone]);
    }
}
