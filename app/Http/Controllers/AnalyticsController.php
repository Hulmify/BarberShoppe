<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        if (!$shop) {
            return redirect()->route('admin.dashboard');
        }

        $tz = $shop->timezone ?? config('app.timezone');
        $now = Carbon::now($tz);

        // Date Range Handling
        $startDateStr = $request->input('start_date', $now->copy()->subDays(29)->toDateString());
        $endDateStr = $request->input('end_date', $now->toDateString());

        $startDate = Carbon::parse($startDateStr, $tz)->startOfDay();
        $endDate = Carbon::parse($endDateStr, $tz)->endOfDay();

        // Ensure reasonable limits (e.g., max 1 year) or just calculate days
        $diffInDays = $startDate->diffInDays($endDate);
        if ($diffInDays > 365) {
            $startDate = $endDate->copy()->subYear();
            $diffInDays = 365;
        }

        $bookings = $shop->bookings()
            ->whereBetween('start_time', [$startDate->copy()->setTimezone('UTC'), $endDate->copy()->setTimezone('UTC')])
            ->where('status', '!=', 'cancelled')
            ->get();

        $chartData = [
            'labels' => [],
            'bookings' => [],
            'revenue' => []
        ];

        for ($i = 0; $i <= $diffInDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            
            $dayBookings = $bookings->filter(function($b) use ($currentDate, $tz) {
                return $b->start_time->copy()->setTimezone($tz)->toDateString() === $currentDate->toDateString();
            });

            $chartData['labels'][] = $currentDate->format('M d');
            $chartData['bookings'][] = $dayBookings->count();
            $chartData['revenue'][] = (float) $dayBookings->sum('total_price');
        }

        // 2. Top Services (within range)
        $topServices = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('services', 'booking_items.service_id', '=', 'services.id')
            ->where('bookings.shop_id', $shop->id)
            ->where('bookings.status', '!=', 'cancelled')
            ->whereBetween('bookings.start_time', [$startDate->copy()->setTimezone('UTC'), $endDate->copy()->setTimezone('UTC')])
            ->select('services.name', DB::raw('COUNT(*) as usage_count'), DB::raw('SUM(booking_items.price) as service_revenue'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        // 3. Busy Hours (Usage insights - based on full history or selected range? Let's use history for pattern, but range for insights)
        $busyHoursRaw = $bookings->groupBy(function($b) use ($tz) {
            return (int)$b->start_time->copy()->setTimezone($tz)->format('H');
        });

        $busyHours = [
            'labels' => [],
            'counts' => []
        ];
        for ($h = 0; $h < 24; $h++) {
            $busyHours['labels'][] = sprintf('%02d:00', $h);
            $busyHours['counts'][] = isset($busyHoursRaw[$h]) ? $busyHoursRaw[$h]->count() : 0;
        }

        // 4. Overall Stats (within range)
        $totalRevenue = (float)$bookings->where('status', 'completed')->sum('total_price');
        $totalBookings = $bookings->count();
        $avgBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        return view('admin.analytics.index', compact(
            'shop',
            'chartData',
            'topServices',
            'busyHours',
            'totalRevenue',
            'totalBookings',
            'avgBookingValue',
            'startDate',
            'endDate'
        ));
    }
}
