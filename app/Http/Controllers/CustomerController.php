<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * List all customers who have booked with the current shop.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Identify the user's shop
        $shopId = auth()->user()->shop->id;
        $search = $request->input('search');
        
        // Find customers that have at least one booking in this shop
        $query = Customer::whereHas('bookings', function($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount(['bookings' => function($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        }])->paginate(20);
        
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display detailed profile and booking history for a specific customer.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $shopId = auth()->user()->shop->id;
        
        // Ensure customer and their displayed bookings belong to the current shop
        $customer = Customer::whereHas('bookings', function($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })->with(['bookings' => function($q) use ($shopId) {
            $q->where('shop_id', $shopId)->with('items.service')->latest();
        }])->findOrFail($id);
        
        // Aggregate customer value stats
        $totalSpent = $customer->bookings->where('status', 'completed')->sum('total_price');
        $lastVisit = $customer->bookings->first()?->start_time;

        return view('admin.customers.show', compact('customer', 'totalSpent', 'lastVisit'));
    }
}

