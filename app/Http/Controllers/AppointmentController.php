<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * List appointments with filtering and pagination for the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        $tz = $shop->timezone ?? config('app.timezone');
        
        $query = $shop->bookings()->with(['customer', 'items.service', 'stylist'])->latest('start_time');
        
        // Filter by specific date or pre-defined filters
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        } elseif ($request->get('filter') === 'today') {
            $query->whereDate('start_time', now()->timezone($tz));
        }
        
        // Filter by Status (supports multiple via array or comma-separated string)
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
        
        // Ensure all times are shifted to the shop's local timezone for display
        $bookings->getCollection()->each(function($b) use ($tz) {
            $b->start_time->setTimezone($tz);
            if ($b->end_time) $b->end_time->setTimezone($tz);
        });

        $stylists = $shop->stylists()->where('is_active', true)->get();
        
        return view('admin.appointments.index', compact('bookings', 'stylists'));
    }

    /**
     * Update appointment status or assigned stylist.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $booking = auth()->user()->shop->bookings()->findOrFail($id);
        
        $request->validate([
            'status' => 'nullable|in:confirmed,cancelled,completed,in_progress',
            'stylist_id' => 'nullable|exists:stylists,id'
        ]);
        
        if ($request->filled('status')) {
            $booking->update(['status' => $request->status]);
        }

        if ($request->has('stylist_id')) {
            $stylistId = $request->input('stylist_id');
            if ($stylistId) {
                // Verify stylist belongs to shop
                $stylist = auth()->user()->shop->stylists()->findOrFail($stylistId);
                $booking->update(['stylist_id' => $stylist->id]);
            } else {
                $booking->update(['stylist_id' => null]);
            }
        }
        
        return back()->with('success', 'Appointment updated.');
    }

    /**
     * Permanently delete an appointment.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $booking = auth()->user()->shop->bookings()->findOrFail($id);
        
        // Clean up associated items
        $booking->items()->delete();
        $booking->delete();
        
        return back()->with('success', 'Appointment permanently deleted.');
    }

    /**
     * Display appointments in a calendar view.
     */
    public function calendar()
    {
        $shop = auth()->user()->shop;
        $stylists = $shop->stylists()->where('is_active', true)->get();
        return view('admin.appointments.calendar', compact('stylists'));
    }

    /**
     * Get appointments as JSON events for FullCalendar.
     */
    public function events(Request $request)
    {
        $shop = auth()->user()->shop;
        $tz = $shop->timezone ?? config('app.timezone');
        
        $start = $request->input('start');
        $end = $request->input('end');
        
        $query = $shop->bookings()->with(['customer', 'stylist', 'items.service']);
        
        if ($start) $query->where('start_time', '>=', $start);
        if ($end) $query->where('start_time', '<=', $end);
        
        $bookings = $query->get();
        
        $events = $bookings->map(function($b) use ($tz) {
            $services = $b->items->map(fn($i) => $i->service->name)->implode(', ');
            $stylistName = $b->stylist ? $b->stylist->name : 'Unassigned';
            
            $color = match($b->status) {
                'pending' => '#eab308', // yellow-500
                'confirmed' => '#22c55e', // green-500
                'in_progress' => '#4896bf', // primary
                'completed' => '#64748b', // slate-500
                'cancelled' => '#ef4444', // red-500
                default => '#94a3b8'
            };

            return [
                'id' => $b->id,
                'title' => $b->customer->name . " (" . $services . ")",
                'start' => $b->start_time->toIso8601String(),
                'end' => $b->end_time ? $b->end_time->toIso8601String() : $b->start_time->addMinutes(30)->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $b->status,
                    'customer' => $b->customer->name,
                    'phone' => $b->customer->phone,
                    'stylist' => $stylistName,
                    'price' => number_format($b->total_price, 2)
                ]
            ];
        });
        
        return response()->json($events);
    }
}

