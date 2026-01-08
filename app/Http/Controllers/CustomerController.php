<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // Get all customers connected to shop via bookings
        $shopId = auth()->user()->shop->id;
        
        $customers = Customer::whereHas('bookings', function($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })->withCount(['bookings' => function($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        }])->paginate(20);
        
        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        // Show customer history
    }
}
