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
    /**
     * Display the analytics dashboard with revenue, bookings, and performance data.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        if (!$shop) {
            return redirect()->route('admin.dashboard');
        }

        $tz = $shop->timezone ?? config('app.timezone');
        $now = Carbon::now($tz);

        // Parse date range from request or default to the last 30 days
        $startDateStr = $request->input('start_date', $now->copy()->subDays(29)->toDateString());
        $endDateStr = $request->input('end_date', $now->toDateString());

        $startDate = Carbon::parse($startDateStr, $tz)->startOfDay();
        $endDate = Carbon::parse($endDateStr, $tz)->endOfDay();

        // Limit range to a maximum of 1 year to prevent performance issues
        $diffInDays = $startDate->diffInDays($endDate);
        if ($diffInDays > 365) {
            $startDate = $endDate->copy()->subYear();
            $diffInDays = 365;
        }

        // Fetch non-cancelled bookings within the selected range
        $bookings = $shop->bookings()
            ->whereBetween('start_time', [$startDate->copy()->setTimezone('UTC'), $endDate->copy()->setTimezone('UTC')])
            ->where('status', '!=', 'cancelled')
            ->get();

        $chartData = [
            'labels' => [],
            'bookings' => [],
            'revenue' => []
        ];

        // Prepare data for the timeline chart
        for ($i = 0; $i <= $diffInDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            
            $dayBookings = $bookings->filter(function($b) use ($currentDate, $tz) {
                return $b->start_time->copy()->setTimezone($tz)->toDateString() === $currentDate->toDateString();
            });

            $chartData['labels'][] = $currentDate->format('M d');
            $chartData['bookings'][] = $dayBookings->count();
            $chartData['revenue'][] = (float) $dayBookings->sum('total_price');
        }

        // 2. Identification of Top Services (most booked)
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

        // 3. Busy Hours Pattern Analysis
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

        // 4. Stylist Performance Analysis (Revenue generation)
        $stylistPerformance = DB::table('bookings')
            ->join('stylists', 'bookings.stylist_id', '=', 'stylists.id')
            ->where('bookings.shop_id', $shop->id)
            ->where('bookings.status', 'completed')
            ->whereBetween('bookings.start_time', [$startDate->copy()->setTimezone('UTC'), $endDate->copy()->setTimezone('UTC')])
            ->select(
                'stylists.name', 
                DB::raw('COUNT(*) as booking_count'), 
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->groupBy('stylists.id', 'stylists.name')
            ->orderByDesc('total_revenue')
            ->get();

        // 5. Aggregate Summary Statistics
        $totalRevenue = (float)$bookings->where('status', 'completed')->sum('total_price');
        $totalBookings = $bookings->count();
        $avgBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        return view('admin.analytics.index', compact(
            'shop',
            'chartData',
            'topServices',
            'busyHours',
            'stylistPerformance',
            'totalRevenue',
            'totalBookings',
            'avgBookingValue',
            'startDate',
            'endDate'
        ));
    }
}

