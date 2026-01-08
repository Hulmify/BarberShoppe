<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;
        
        // Ensure all days exist for the shop (stylist_id is null)
        for ($i = 0; $i <= 6; $i++) {
            $shop->availabilities()->firstOrCreate(
                ['day_of_week' => $i, 'stylist_id' => null],
                ['start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_active' => $i > 0 && $i < 6] // Default Mo-Fri
            );
        }
        
        $availabilities = $shop->availabilities()->whereNull('stylist_id')->orderBy('day_of_week')->get();
        
        return view('admin.availability.index', compact('availabilities'));
    }

    public function update(Request $request)
    {
        $shop = auth()->user()->shop;
        
        $data = $request->validate([
            'schedule' => 'required|array',
            'schedule.*.start_time' => 'required|date_format:H:i',
            'schedule.*.end_time' => 'required|date_format:H:i',
            'schedule.*.is_active' => 'nullable',
        ]);
        
        foreach ($data['schedule'] as $day => $times) {
            $shop->availabilities()
                ->where('day_of_week', $day)
                ->whereNull('stylist_id')
                ->update([
                    'start_time' => $times['start_time'],
                    'end_time' => $times['end_time'],
                    'is_active' => isset($times['is_active'])
                ]);
        }
        
        return back()->with('success', 'Schedule updated successfully.');
    }
}
