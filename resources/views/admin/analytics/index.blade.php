@extends('layouts.admin')

@section('title', 'Analytics')
@section('header', 'Analytics & Insights')
@section('subheader')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-2">
    <p class="text-gray-500 text-sm">Deep dive into your business performance and customer trends.</p>
    
    <form action="{{ route('admin.analytics.index') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-2 px-2">
            <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" 
                class="text-xs font-bold text-slate-700 border-none focus:ring-0 p-0 bg-transparent">
            <span class="text-slate-300">→</span>
            <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" 
                class="text-xs font-bold text-slate-700 border-none focus:ring-0 p-0 bg-transparent">
        </div>
        <button type="submit" class="bg-slate-900 text-white p-2 rounded-lg hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </button>
    </form>
</div>
@endsection

@section('content')

<!-- Quick Stats Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Revenue <span class="text-[8px] opacity-60">(in selection)</span></div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-slate-900">{{ $shop->currency ?? '$' }} {{ number_format($totalRevenue, 2) }}</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Bookings <span class="text-[8px] opacity-60">(in selection)</span></div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-slate-900">{{ number_format($totalBookings) }}</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Avg. Value <span class="text-[8px] opacity-60">(in selection)</span></div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-slate-900">{{ $shop->currency ?? '$' }} {{ number_format($avgBookingValue, 2) }}</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Active Services</div>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-slate-900">{{ $shop->services()->count() }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Main Revenue & Bookings Chart -->
    <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Business Growth</h3>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-tighter">
                    {{ $startDate->format('M d, Y') }} — {{ $endDate->format('M d, Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 bg-primary-500 rounded-full"></div>
                    <span class="text-[10px] font-bold text-slate-500 border-none outline-none">Revenue</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 bg-slate-300 rounded-full"></div>
                    <span class="text-[10px] font-bold text-slate-500">Bookings</span>
                </div>
            </div>
        </div>
        <div id="growthChart" class="w-full min-h-[350px]"></div>
    </div>

    <!-- Top Services -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Top Services</h3>
        <div class="space-y-6">
            @forelse($topServices as $service)
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-bold text-slate-800 mb-1">{{ $service->name }}</div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            @php 
                                $percentage = $totalBookings > 0 ? ($service->usage_count / $totalBookings) * 100 : 0;
                            @endphp
                            <div class="bg-primary-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="ml-4 text-right">
                        <div class="text-xs font-black text-slate-900">{{ $service->usage_count }} booked</div>
                        <div class="text-[10px] font-bold text-slate-400">{{ $shop->currency ?? '$' }} {{ number_format($service->service_revenue, 2) }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-sm text-slate-400 italic">No data available yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Busy Hours Insight -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Usage Patterns</h3>
        <p class="text-xs text-slate-500 mb-6">Daily distribution of bookings by hour.</p>
        <div id="hoursChart" class="w-full min-h-[300px]"></div>
    </div>

    <!-- Quick Insights Card -->
    <div class="bg-slate-900 p-8 rounded-3xl text-white relative overflow-hidden flex flex-col justify-center">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M11 15h2v2h-2zm0-8h2v6h-2zm1-5C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path></svg>
        </div>
        <div class="relative z-10">
            <h4 class="text-2xl font-black mb-4">Smart Insights</h4>
            <div class="space-y-4">
                @php
                    $peakHour = collect($busyHours['counts'])->keys()->sort(fn($a, $b) => $busyHours['counts'][$b] <=> $busyHours['counts'][$a])->first();
                    $peakTime = $peakHour !== null ? \Carbon\Carbon::parse($busyHours['labels'][$peakHour])->format('g:i A') : 'N/A';
                @endphp
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-primary-500 rounded-lg">
                        <svg class="w-6 h-6 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold">Peak Booking Time</div>
                        <p class="text-xs text-slate-400 mt-1">Your busiest hour is typically around <span class="text-primary-400 font-bold uppercase">{{ $peakTime }}</span>.</p>
                    </div>
                </div>
                
                @if($topServices->isNotEmpty())
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-primary-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold">Star Service</div>
                        <p class="text-xs text-slate-400 mt-1"><span class="text-primary-400 font-bold uppercase">{{ $topServices->first()->name }}</span> is your most requested service this month.</p>
                    </div>
                </div>
                @endif

                <div class="flex items-start gap-4">
                    <div class="p-2 bg-green-500 rounded-lg">
                         <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold">Performance Summary</div>
                        <p class="text-xs text-slate-400 mt-1">You've generated {{ $shop->currency ?? '$' }} {{ number_format($totalRevenue, 0) }} in revenue from {{ $totalBookings }} appointments.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stylist Performance Section -->
<div class="mt-8 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="text-xl font-black text-slate-900">Stylist Performance</h3>
            <p class="text-sm text-slate-500">Revenue and booking share by team member.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div id="stylistChart" class="min-h-[350px]"></div>
        <div class="overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Stylist Name</th>
                        <th class="px-6 py-4 text-center">Bookings</th>
                        <th class="px-6 py-4 text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($stylistPerformance as $perf)
                    <tr>
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $perf->name }}</td>
                        <td class="px-6 py-4 text-center font-bold text-slate-600">{{ $perf->booking_count }}</td>
                        <td class="px-6 py-4 text-right">
                           <span class="text-base font-black text-slate-900">{{ $shop->currency ?? '$' }} {{ number_format($perf->total_revenue, 2) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Growth Chart (Revenue & Bookings)
        var growthOptions = {
            series: [{
                name: 'Revenue',
                type: 'area',
                data: @json($chartData['revenue'])
            }, {
                name: 'Bookings',
                type: 'line',
                data: @json($chartData['bookings'])
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            colors: ['#f59e0b', '#cbd5e1'],
            stroke: { width: [4, 4], curve: 'smooth' },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            labels: @json($chartData['labels']),
            xaxis: {
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8', fontWeight: 600, fontSize: '10px' } }
            },
            yaxis: [
                {
                    title: { text: 'Revenue', style: { color: '#f59e0b', fontWeight: 700 } },
                    labels: { style: { colors: '#f59e0b', fontWeight: 600 } }
                },
                {
                    opposite: true,
                    title: { text: 'Bookings', style: { color: '#64748b', fontWeight: 700 } },
                    labels: { style: { colors: '#64748b', fontWeight: 600 } }
                }
            ],
            legend: { show: false },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: { theme: 'light' }
        };

        var growthChart = new ApexCharts(document.querySelector("#growthChart"), growthOptions);
        growthChart.render();

        // Busy Hours Chart (Heatmap or Bar)
        var hoursOptions = {
            series: [{
                name: 'Bookings',
                data: @json($busyHours['counts'])
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                    distributed: true
                }
            },
            colors: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6', '#f97316'],
            dataLabels: { enabled: false },
            legend: { show: false },
            xaxis: {
                categories: @json($busyHours['labels']),
                labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 }, rotate: -45 }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8', fontWeight: 600 } }
            },
            grid: { borderColor: '#f1f5f9' },
            tooltip: { theme: 'light' }
        };

        var hoursChart = new ApexCharts(document.querySelector("#hoursChart"), hoursOptions);
        hoursChart.render();

        // Stylist Revenue Chart
        var stylistOptions = {
            series: @json($stylistPerformance->pluck('total_revenue')),
            chart: {
                type: 'donut',
                height: 350,
            },
            labels: @json($stylistPerformance->pluck('name')),
            colors: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6'],
            legend: {
                position: 'bottom',
                fontFamily: 'Outfit',
                fontWeight: 600
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return Math.round(val) + "%"
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Revenue',
                                formatter: function (w) {
                                    return '{{ $shop->currency ?? '$' }} ' + {{ $totalRevenue }}
                                }
                            }
                        }
                    }
                }
            },
            tooltip: { theme: 'light' }
        };

        var stylistChart = new ApexCharts(document.querySelector("#stylistChart"), stylistOptions);
        stylistChart.render();
    });
</script>

@endsection
