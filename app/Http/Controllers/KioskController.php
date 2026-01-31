<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Carbon\Carbon;

class KioskController extends Controller
{
    private function getShop(Request $request, $slug = null)
    {
        if ($request->attributes->has('shop')) {
            return $request->attributes->get('shop');
        }
        return Shop::where('slug', $slug)->firstOrFail();
    }

    public function show(Request $request, $slug = null)
    {
        $shop = $this->getShop($request, $slug);
        $stylists = $shop->stylists()->where('is_active', true)->get();
        $services = $shop->services()->take(8)->get();

        $tz = $shop->timezone ?? config('app.timezone');

        $now = Carbon::now($tz);
        
        // Use boundaries for the shop's current day to be timezone-safe
        $startOfDay = $now->copy()->startOfDay()->setTimezone('UTC');
        $endOfDay = $now->copy()->endOfDay()->setTimezone('UTC');

        // All Today's Bookings (already filtered by day)
        $allToday = $shop->bookings()
            ->whereBetween('start_time', [$startOfDay, $endOfDay])
            ->whereIn('status', ['pending', 'confirmed', 'in_progress', 'completed'])
            ->with(['customer', 'stylist'])
            ->orderBy('start_time', 'asc')
            ->get();

        // 1. Now Serving: Explicitly 'in_progress' OR active right now
        $nowServing = $allToday->filter(function($b) use ($now) {
            return $b->status === 'in_progress' || ($b->start_time->lte($now) && $b->end_time->gte($now) && $b->status !== 'completed' && $b->status !== 'cancelled');
        });

        $nowServingIds = $nowServing->pluck('id')->toArray();

        // 2. Next Up: Strictly future appointments
        $nextUp = $allToday->filter(function($b) use ($now, $nowServingIds) {
            return !in_array($b->id, $nowServingIds) && $b->start_time->gt($now) && $b->status !== 'completed' && $b->status !== 'cancelled';
        });

        // 3. Completed Today: Explicitly marked as completed
        $completedToday = $allToday->filter(function($b) {
            return $b->status === 'completed';
        });

        // 4. Past Due: Started and passed their end time but NOT completed or in_progress
        $pastDue = $allToday->filter(function($b) use ($now, $nowServingIds) {
            return !in_array($b->id, $nowServingIds) && 
                   $b->status !== 'completed' && 
                   $b->status !== 'cancelled' && 
                   $b->end_time->lt($now);
        });

        return view('kiosk.show', compact('shop', 'stylists', 'services', 'nowServing', 'nextUp', 'completedToday', 'pastDue'));
    }
}
