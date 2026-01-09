<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        
        $query = $shop->bookings()
            ->with(['customer', 'items.service', 'stylist'])
            ->select('bookings.*');
        
        // Sorting Logic
        $sortBy = $request->get('sort_by', 'start_time');
        $sortDir = $request->get('sort_dir', 'desc');
        $validSorts = ['start_time', 'total_price', 'status', 'customer_name'];

        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'start_time';
        }

        if ($sortBy === 'customer_name') {
            $query->join('customers', 'bookings.customer_id', '=', 'customers.id')
                  ->orderBy('customers.name', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }
        
        // Filter by specific date or pre-defined filters
        if ($request->filled('date')) {
            $startUtc = Carbon::parse($request->date, $tz)->startOfDay()->setTimezone('UTC');
            $endUtc = Carbon::parse($request->date, $tz)->endOfDay()->setTimezone('UTC');
            $query->whereBetween('start_time', [$startUtc, $endUtc]);
        } elseif ($request->get('filter') === 'today') {
            $startUtc = now($tz)->startOfDay()->setTimezone('UTC');
            $endUtc = now($tz)->endOfDay()->setTimezone('UTC');
            $query->whereBetween('start_time', [$startUtc, $endUtc]);
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
        
        $stylists = $shop->stylists()->where('is_active', true)->get();
        
        return view('admin.appointments.index', compact('bookings', 'stylists', 'tz'));
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
        
        if ($start) {
            $startUtc = Carbon::parse($start)->setTimezone('UTC');
            $query->where('start_time', '>=', $startUtc);
        }
        if ($end) {
            $endUtc = Carbon::parse($end)->setTimezone('UTC');
            $query->where('start_time', '<=', $endUtc);
        }
        
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

            // Convert to shop timezone for display
            $startTime = $b->start_time->copy()->setTimezone($tz);
            $endTime = $b->end_time ? $b->end_time->copy()->setTimezone($tz) : $startTime->copy()->addMinutes(30);

            return [
                'id' => $b->id,
                'title' => $b->customer->name . " (" . $services . ")",
                'start' => $startTime->toIso8601String(),
                'end' => $endTime->toIso8601String(),
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

