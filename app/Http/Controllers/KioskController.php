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
        
        $now = Carbon::now();
        $bookings = $shop->bookings()
            ->whereDate('start_time', $now->toDateString())
            ->where('status', '!=', 'cancelled')
            ->with(['customer', 'stylist'])
            ->orderBy('start_time', 'asc')
            ->get();

        // Determine who is "Now Serving" and who is "Next Up"
        $nowServing = $bookings->filter(function($b) use ($now) {
            return $now->between($b->start_time, $b->end_time);
        });

        $nextUp = $bookings->filter(function($b) use ($now) {
            return $b->start_time->isAfter($now);
        })->take(5);

        return view('kiosk.show', compact('shop', 'stylists', 'services', 'nowServing', 'nextUp'));
    }
}
