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
        config(['app.timezone' => $tz]);
        date_default_timezone_set($tz);

        $now = Carbon::now();
        
        // Use boundaries for the shop's current day to be timezone-safe
        $startOfDay = $now->copy()->startOfDay()->setTimezone('UTC');
        $endOfDay = $now->copy()->endOfDay()->setTimezone('UTC');

        $bookings = $shop->bookings()
            ->whereBetween('start_time', [$startOfDay, $endOfDay])
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->with(['customer', 'stylist'])
            ->orderBy('start_time', 'asc')
            ->get();

        $bookings->each(function($b) use ($tz) {
            $b->start_time->setTimezone($tz);
            if ($b->end_time) $b->end_time->setTimezone($tz);
        });

        // Determine who is "Now Serving" and who is "Next Up"
        // Now Serving: Explicitly in_progress OR starting within 5 mins
        $nowServing = $bookings->filter(function($b) use ($now) {
            return $b->status === 'in_progress' || 
                   ($b->start_time->lte($now->copy()->addMinutes(5)) && 
                    $b->end_time->gt($now->copy()->subMinutes(10)));
        });

        // Next Up: Strictly future appointments that are NOT in Now Serving
        $nowServingIds = $nowServing->pluck('id')->toArray();
        $nextUp = $bookings->filter(function($b) use ($now, $nowServingIds) {
            return $b->start_time->gt($now) && !in_array($b->id, $nowServingIds);
        })->take(5);

        return view('kiosk.show', compact('shop', 'stylists', 'services', 'nowServing', 'nextUp'));
    }
}
