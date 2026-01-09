<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        $tz = $shop->timezone ?? config('app.timezone');
        
        $query = $shop->bookings()->with(['customer', 'items.service'])->latest('start_time');
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        } elseif ($request->get('filter') === 'today') {
            $query->whereDate('start_time', now()->timezone($tz));
        }
        
        // Filter by Status
        if ($request->filled('statuses')) {
            $statuses = is_array($request->statuses) ? $request->statuses : explode(',', $request->statuses);
            $query->whereIn('status', $statuses);
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif ($request->get('filter') === 'pending') {
            $query->where('status', 'pending');
        } elseif ($request->get('filter') === 'active') {
             $query->whereIn('status', ['confirmed', 'in_progress']);
        } elseif ($request->get('filter') === 'completed') {
             $query->where('status', 'completed');
        }

        $bookings = $query->paginate(15)->withQueryString();
        
        $bookings->getCollection()->each(function($b) use ($tz) {
            $b->start_time->setTimezone($tz);
            if ($b->end_time) $b->end_time->setTimezone($tz);
        });
        
        return view('admin.appointments.index', compact('bookings'));
    }

    public function update(Request $request, $id)
    {
        $booking = auth()->user()->shop->bookings()->findOrFail($id);
        
        $request->validate(['status' => 'required|in:confirmed,cancelled,completed,in_progress']);
        
        $booking->update(['status' => $request->status]);
        
        return back()->with('success', 'Booking status updated.');
    }

    public function destroy($id)
    {
        $booking = auth()->user()->shop->bookings()->findOrFail($id);
        
        // Optionally delete associated items if not handled by database cascade
        $booking->items()->delete();
        $booking->delete();
        
        return back()->with('success', 'Appointment permanently deleted.');
    }
}
