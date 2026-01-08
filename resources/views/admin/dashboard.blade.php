@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Overview of your business performance.')

@section('content')

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Card 1 -->
    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <h5 class="mb-2 text-xs font-bold tracking-wider text-gray-500 uppercase">Today's Appointments</h5>
        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-50 rounded-full">
                <svg class="w-8 h-8 text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <span class="text-4xl font-extrabold text-slate-800">{{ $todaysBookings->count() }}</span>
        </div>
    </div>
    
    <!-- Card 2 -->
    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <h5 class="mb-2 text-xs font-bold tracking-wider text-gray-500 uppercase">Weekly Revenue (Est.)</h5>
        <div class="flex items-center gap-4">
            <div class="p-3 bg-green-50 rounded-full">
                <svg class="w-8 h-8 text-green-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v13m0-13 4 4m-4-4-4 4"/>
                </svg>
            </div>
            <div>
                 <span class="text-4xl font-extrabold text-slate-800">{{ $shop->currency ?? '$' }} {{ number_format($potentialRevenue) }}</span>
                 <p class="text-xs text-slate-500 mt-1 font-medium">Completed: {{ $shop->currency ?? '$' }} {{ number_format($weekRevenue) }}</p>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
        <h5 class="mb-2 text-xs font-bold tracking-wider text-gray-500 uppercase">Total Customers</h5>
         <div class="flex items-center gap-4">
            <div class="p-3 bg-purple-50 rounded-full">
                <svg class="w-8 h-8 text-purple-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <span class="text-4xl font-extrabold text-slate-800">{{ $totalCustomers }}</span>
        </div>
    </div>
</div>

<!-- Schedule Section -->
<div class="w-full bg-white border border-gray-200 rounded-xl shadow-sm mb-8 overflow-hidden">
    <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gray-50">
        <h5 class="text-lg font-bold text-slate-800">Today's Schedule</h5>
        <span class="text-sm font-medium text-slate-500 bg-white px-3 py-1 rounded border border-gray-200 shadow-sm">{{ now()->toFormattedDateString() }}</span>
    </div>

    @if($todaysBookings->isEmpty())
        <div class="flex flex-col items-center justify-center p-12 text-center">
            <div class="p-4 bg-gray-50 rounded-full mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No appointments today</h3>
            <p class="text-slate-500 text-sm mt-1">Accept bookings to fill up your schedule.</p>
        </div>
    @else
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Time</th>
                        <th scope="col" class="px-6 py-4">Customer</th>
                        <th scope="col" class="px-6 py-4">Services</th>
                        <th scope="col" class="px-6 py-4">Price</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todaysBookings as $booking)
                    <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-800 text-base">
                            {{ $booking->start_time->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">{{ $booking->customer->name }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->customer->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking->items as $item)
                                    <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-2.5 py-0.5 rounded border border-slate-200">
                                        {{ $item->service->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800">
                            {{ $shop->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClass = match($booking->status) {
                                    'confirmed' => 'bg-green-100 text-green-700 border border-green-200',
                                    'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
                                    'pending' => 'bg-amber-100 text-amber-700 border border-amber-200',
                                    default => 'bg-gray-100 text-gray-700 border border-gray-200'
                                };
                            @endphp
                            <span class="{{ $statusClass }} text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                {{ $booking->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Link Section -->
<div class="w-full bg-slate-900 border border-slate-800 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 p-4 opacity-10">
        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
    </div>
    
    <div class="relative z-10">
        <h5 class="mb-2 text-xl font-bold">Your Booking Link</h5>
        <p class="text-slate-300 text-sm mb-6 max-w-lg">Share this link directly with your customers or add it to your social media bio to start accepting appointments.</p>
        
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg p-3 text-sm font-mono text-amber-500 break-all">
                @if($shop->custom_domain)
                    http://{{ $shop->custom_domain }}
                @else
                    {{ route('booking.via_slug', $shop->slug) }}
                @endif
            </div>
            <a href="{{ route('booking.via_slug', $shop->slug) }}" target="_blank" class="text-slate-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-white/50 font-bold rounded-lg text-sm px-6 py-3 focus:outline-none transition-colors">
                Preview Booking Page
            </a>
        </div>
    </div>
</div>

@endsection
