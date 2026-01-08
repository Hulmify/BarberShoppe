<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        
        $query = $shop->bookings()->with(['customer', 'items.service'])->orderBy('start_time', 'desc');
        
        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }
        
        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(15);
        
        return view('admin.appointments.index', compact('bookings'));
    }

    public function update(Request $request, $id)
    {
        $booking = auth()->user()->shop->bookings()->findOrFail($id);
        
        $request->validate(['status' => 'required|in:confirmed,cancelled,completed']);
        
        $booking->update(['status' => $request->status]);
        
        return back()->with('success', 'Booking status updated.');
    }
}
