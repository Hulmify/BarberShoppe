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
    /**
     * Resolve the shop instance from request or slug.
     *
     * @param Request $request
     * @param string|null $slug
     * @return Shop
     */
    private function getShop(Request $request, $slug = null)
    {
        if ($request->attributes->has('shop')) {
            return $request->attributes->get('shop');
        }
        return Shop::where('slug', $slug)->firstOrFail();
    }

    /**
     * Display the public booking page.
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        
        // Get services with search and pagination for the booking catalog
        $search = $request->input('search');
        $servicesQuery = $shop->services();
        
        if ($search) {
            $servicesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        $services = $servicesQuery->paginate(10)->withQueryString();
        
        // Get active stylists to allow customers to choose their preferred barber
        $stylists = $shop->stylists()->where('is_active', true)->get();
        
        return view('booking.index', compact('shop', 'services', 'search', 'stylists'));
    }

    /**
     * Fetch available time slots for a given date and service duration.
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function slots(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        
        // Validate the requested date
        $request->validate(['date' => 'required|date']);
        $tz = $shop->timezone ?? config('app.timezone');
        
        // Strictly parse the date part only, ensuring we use the shop's timezone
        // This fixes issues where ISO strings from frontend might trigger UTC interpretation
        $dateString = substr($request->date, 0, 10);
        $date = Carbon::parse($dateString, $tz)->startOfDay();
        $today = Carbon::now($tz)->startOfDay();

        // Don't allow bookings in the past
        if ($date->lt($today)) {
            return response()->json(['slots' => []]);
        }

        // Check if shop is explicitly closed for the requested date (manual override)
        if ($shop->off_date && Carbon::parse($shop->off_date, $tz)->startOfDay()->equalTo($date)) {
            return response()->json(['slots' => [], 'message' => 'Shop is closed today.']);
        }
        
        // Determine required duration and optional stylist preference
        $duration = (int) $request->input('duration', 30); // minutes
        $stylistId = $request->input('stylist_id');
        
        // Get active stylists with their availability for requested day of week
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

        // Calculate the overall operating hours based on the earliest and latest stylist shifts
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
        
        // Convert Shop "Day" start/end to UTC for querying DB (which stores UTC)
        $searchStartUtc = $date->copy()->setTimezone('UTC');
        $searchEndUtc = $date->copy()->endOfDay()->setTimezone('UTC');

        // Retrieve all existing bookings for this day to check for overlaps
        // We use whereBetween or overlapping logic on the UTC range
        $allBookings = $shop->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($searchStartUtc, $searchEndUtc) {
                 $q->where('start_time', '<', $searchEndUtc)
                   ->where('end_time', '>', $searchStartUtc);
            })
            ->get();

        $slots = [];
        $now = Carbon::now($tz);
        
        // Iterate through the day in 15-minute increments
        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $start->copy()->addMinutes($duration);
            
            // Skip if slot start time has already passed
            if ($start->lt($now)) {
                $start->addMinutes(15);
                continue;
            }

            // Identify any bookings that conflict with this specific time slot
            $overlappingBookings = $allBookings->filter(function ($b) use ($start, $slotEnd) {
                return $start->lt($b->end_time) && $slotEnd->gt($b->start_time);
            });

            // Find stylists who are:
            // 1. Scheduled to work during this entire window
            // 2. Not already booked for an overlapping appointment
            $availableStylists = $stylists->filter(function($s) use ($start, $slotEnd, $overlappingBookings, $stylistId, $tz) {
                // If the user requested a specific stylist, ignore others
                if ($stylistId && $s->id != $stylistId) return false;

                // Check if stylist is busy
                if ($overlappingBookings->contains(function($b) use ($s) {
                    return (string)$b->stylist_id === (string)$s->id;
                })) return false;

                // Check if stylist is on shift
                $sAvail = $s->availabilities->first();
                if (!$sAvail) return false;

                $sStart = Carbon::parse($start->format('Y-m-d') . ' ' . $sAvail->start_time, $tz);
                $sEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $sAvail->end_time, $tz);

                return $start->gte($sStart) && $slotEnd->lte($sEnd);
            });

            // If at least one stylist can take the appointment, this slot is available
            if ($availableStylists->isNotEmpty()) {
                $slots[] = $start->format('H:i');
            }
            
            $start->addMinutes(15); 
        }
        
        return response()->json(['slots' => $slots]);
    }

    /**
     * Create a new booking for a customer.
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\Http\JsonResponse
     */
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

        // Rate limiting/Business logic: prevent multiple active bookings from the same customer
        $existingCustomer = Customer::where('phone', $validated['customer_phone'])->first();
        if ($existingCustomer) {
            $activeBooking = Booking::where('shop_id', $shop->id)
                ->where('customer_id', $existingCustomer->id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->first();

            if ($activeBooking) {
                return response()->json([
                    'success' => false, 
                    'message' => 'You already have an active appointment.'
                ], 422);
            }
        }

        // Calculate totals based on selected services
        $services = $shop->services()->whereIn('id', $validated['service_ids'])->get();
        $totalPrice = $services->sum('price');
        $totalDuration = $services->sum('duration_minutes');
        
        $tz = $shop->timezone ?? config('app.timezone');
        $startTime = Carbon::parse($validated['date'] . ' ' . $validated['time'], $tz)->setTimezone('UTC');
        
        if ($startTime->isPast()) {
            return response()->json(['success' => false, 'message' => 'Cannot book appointments in the past'], 422);
        }

        // Re-check shop closure status
        if ($shop->off_date && Carbon::parse($shop->off_date)->isSameDay($startTime)) {
            return response()->json(['success' => false, 'message' => 'Shop is closed today.'], 422);
        }

        $endTime = $startTime->copy()->addMinutes($totalDuration);
        
        // Final concurrency check: verify availability just before creating the record
        $overlappingBookings = $shop->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })->get();

        $selectedStylistId = $validated['stylist_id'] ?? null;
        $dayOfWeek = $startTime->dayOfWeek;
        
        $activeStylists = $shop->stylists()
            ->where('is_active', true)
            ->with(['availabilities' => function($q) use ($dayOfWeek) {
                $q->where('day_of_week', $dayOfWeek)->where('is_active', true);
            }])
            ->get();

        $availableStylists = $activeStylists->filter(function($s) use ($startTime, $endTime, $overlappingBookings, $tz) {
            if ($overlappingBookings->contains(function($b) use ($s) {
                return (string)$b->stylist_id === (string)$s->id;
            })) return false;

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
            // Auto-assign any available stylist if none was requested
            $selectedStylistId = $availableStylists->random()->id;
        }
        
        // Register or update customer profile
        $customer = Customer::updateOrCreate(
            ['phone' => $validated['customer_phone']],
            ['name' => $validated['customer_name'], 'email' => $validated['customer_email']]
        );

        // Persist the booking
        $booking = Booking::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'stylist_id' => $selectedStylistId,
        ]);

        // Attach services to the booking
        foreach ($services as $svc) {
            BookingItem::create([
                'booking_id' => $booking->id,
                'service_id' => $svc->id,
                'price' => $svc->price
            ]);
        }
        
        return response()->json(['success' => true, 'booking_id' => $booking->id]);
    }

    /**
     * Show appointment history for a customer based on their phone number.
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\View\View
     */
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
                    ->limit(10)
                    ->get();
                

            }
        }

        return view('booking.my_appointments', compact('shop', 'bookings', 'phone'));
    }

    /**
     * Redirect to the appointment list for a customer phone number.
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\Http\RedirectResponse
     */
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

