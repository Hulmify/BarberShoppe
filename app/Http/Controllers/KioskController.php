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

        // Now Serving: Explicitly in_progress ONLY
        $nowServing = $bookings->filter(function($b) {
            return $b->status === 'in_progress';
        });

        // Next Up: All active appointments that are NOT in Now Serving and have not ended yet
        $nowServingIds = $nowServing->pluck('id')->toArray();
        $nextUp = $bookings->filter(function($b) use ($now, $nowServingIds) {
            return !in_array($b->id, $nowServingIds) && $b->end_time->gt($now);
        })->take(5);

        return view('kiosk.show', compact('shop', 'stylists', 'services', 'nowServing', 'nextUp'));
    }
}
