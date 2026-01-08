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
        $services = $shop->services; 
        
        return view('booking.index', compact('shop', 'services'));
    }

    public function slots(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        
        // Validate date
        $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($request->date);
        
        // Get Availability for Day of Week
        $dayOfWeek = $date->dayOfWeek; // 0=Sun
        
        $avail = $shop->availabilities()->where('day_of_week', $dayOfWeek)->first();
        
        if (!$avail || !$avail->is_active) {
            return response()->json(['slots' => []]);
        }

        // Generate Slots
        
        $duration = (int) $request->input('duration', 30); // minutes
        
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $avail->start_time);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $avail->end_time);
        
        // Get existing bookings
        $bookings = $shop->bookings()
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->get();
            
        $slots = [];
        
        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $start->copy()->addMinutes($duration);
            
            // Check collision
            $collision = $bookings->contains(function ($b) use ($start, $slotEnd) {
                // Overlap: (StartA < EndB) and (EndA > StartB)
                return $start->lt($b->end_time) && $slotEnd->gt($b->start_time);
            });
            
            if (!$collision) {
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
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string',
        ]);

        // Calculate Totals
        $services = $shop->services()->whereIn('id', $validated['service_ids'])->get();
        $totalPrice = $services->sum('price');
        $totalDuration = $services->sum('duration_minutes');
        
        // Time
        $startTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $endTime = $startTime->copy()->addMinutes($totalDuration);
        
        // Double Check Availability (Concurrency)
        $exists = $shop->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })->exists();
        
        // Create Customer
        $customer = Customer::firstOrCreate(
            ['email' => $validated['customer_email']],
            ['name' => $validated['customer_name'], 'phone' => $validated['customer_phone']]
        );

        // Create Booking
        $booking = Booking::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $totalPrice,
            'status' => 'pending'
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
}
