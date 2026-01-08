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
        
        // Generate Slots
        $duration = (int) $request->input('duration', 30); // minutes
        $stylistId = $request->input('stylist_id'); // Optional stylist filter
        
        // Get active stylists with their availability for this day
        $dayOfWeek = $date->dayOfWeek;
        $stylists = $shop->stylists()
            ->where('is_active', true)
            ->with(['availabilities' => function($q) use ($dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek)->where('is_active', true);
            }])
            ->get();

        if ($stylists->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        // Determine the overall working window for the day based on stylists
        $earliestStartTime = null;
        $latestEndTime = null;

        foreach ($stylists as $s) {
            $sAvail = $s->availabilities->first();
            if ($sAvail) {
                if ($earliestStartTime === null || $sAvail->start_time < $earliestStartTime) {
                    $earliestStartTime = $sAvail->start_time;
                }
                if ($latestEndTime === null || $sAvail->end_time > $latestEndTime) {
                    $latestEndTime = $sAvail->end_time;
                }
            }
        }

        if ($earliestStartTime === null) {
            return response()->json(['slots' => []]);
        }

        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $earliestStartTime, $tz);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $latestEndTime, $tz);
        
        // Get existing bookings
        $allBookings = $shop->bookings()
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

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

            // Check which stylists are available (working and not busy)
            $availableStylists = $stylists->filter(function($s) use ($start, $slotEnd, $overlappingBookings, $stylistId, $tz) {
                // If specific stylist requested, skip others
                if ($stylistId && $s->id != $stylistId) return false;

                // Check if stylist is busy
                if ($overlappingBookings->contains('stylist_id', $s->id)) return false;

                // Check if stylist is working at this time
                $sAvail = $s->availabilities->first();
                if (!$sAvail) return false;

                $sStart = Carbon::parse($start->format('Y-m-d') . ' ' . $sAvail->start_time, $tz);
                $sEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $sAvail->end_time, $tz);

                return $start->gte($sStart) && $slotEnd->lte($sEnd);
            });

            if ($availableStylists->isNotEmpty()) {
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
        $dayOfWeek = $startTime->dayOfWeek;
        
        $activeStylists = $shop->stylists()
            ->where('is_active', true)
            ->with(['availabilities' => function($q) use ($dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek)->where('is_active', true);
            }])
            ->get();

        // Filter stylists who are working AND not busy
        $availableStylists = $activeStylists->filter(function($s) use ($startTime, $endTime, $overlappingBookings, $tz) {
            // Check if stylist is busy
            if ($overlappingBookings->contains('stylist_id', $s->id)) return false;

            // Check if stylist is working at this time
            $sAvail = $s->availabilities->first();
            if (!$sAvail) return false;

            $sStart = Carbon::parse($startTime->format('Y-m-d') . ' ' . $sAvail->start_time, $tz);
            $sEnd = Carbon::parse($startTime->format('Y-m-d') . ' ' . $sAvail->end_time, $tz);

            return $startTime->gte($sStart) && $endTime->lte($sEnd);
        });

        if ($selectedStylistId) {
            if (!$availableStylists->contains('id', $selectedStylistId)) {
                return response()->json(['success' => false, 'message' => 'Requested stylist is busy or not working at this time'], 422);
            }
        } else {
            if ($availableStylists->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No stylists available at this time'], 422);
            }
            // Assign a random free stylist
            $selectedStylistId = $availableStylists->random()->id;
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
