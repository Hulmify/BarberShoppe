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
    public function index()
    {
        $shop = auth()->user()->shop;
        
        // Get existing customers
        $customers = Customer::whereHas('bookings', function($q) use ($shop) {
            $q->where('shop_id', $shop->id);
        })->orderBy('name')->get();
        
        // Get all shop services (is_active column does not exist)
        $services = $shop->services()->get();
        
        return view('admin.pos.index', compact('customers', 'services'));
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
            'new_customer_email' => 'required_if:customer_type,new|nullable|email|max:255', 
            'new_customer_phone' => 'nullable|string|max:20',
        ]);

        // 1. Resolve Customer
        if ($request->customer_type === 'new') {
            $customer = Customer::firstOrCreate(
                ['email' => $request->new_customer_email],
                ['name' => $request->new_customer_name, 'phone' => $request->new_customer_phone, 'password' => bcrypt('password')] 
            );
        } else {
            $customer = Customer::findOrFail($request->customer_id);
        }

        // 2. Create Booking
        // Calculate Total
        $services = Service::whereIn('id', $request->service_ids)->get();
        $totalPrice = $services->sum('price');
        $totalDuration = $services->sum('duration_minutes');
        
        $startDateTime = Carbon::parse($request->date . ' ' . $request->time);
        $endDateTime = $startDateTime->copy()->addMinutes($totalDuration);

        $booking = Booking::create([
            'shop_id' => $shop->id,
            'customer_id' => $customer->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'total_price' => $totalPrice,
            'status' => 'confirmed', 
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
