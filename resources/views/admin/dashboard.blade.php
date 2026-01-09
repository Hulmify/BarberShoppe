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
            <div class="p-3 bg-primary-50 rounded-full">
                <svg class="w-8 h-8 text-primary-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                 <p class="text-xs text-slate-500 mt-1 font-medium">Earned: {{ $shop->currency ?? '$' }} {{ number_format($weekRevenue) }}</p>
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
        
        <form action="{{ route('admin.shop.toggle_off') }}" method="POST">
            @csrf
            @php 
                $tz = $shop->timezone ?? config('app.timezone');
                $isOff = $shop->off_date && $shop->off_date == \Carbon\Carbon::now($tz)->toDateString(); 
            @endphp
            <button type="submit" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg transition-all {{ $isOff ? 'bg-red-100 text-red-700 border border-red-200 hover:bg-red-200' : 'bg-green-100 text-green-700 border border-green-200 hover:bg-green-200' }}">
                <div class="w-2.5 h-2.5 rounded-full {{ $isOff ? 'bg-red-500 animate-pulse' : 'bg-green-500' }}"></div>
                {{ $isOff ? 'Today Is Off (Click to Enable)' : 'Today Is On (Click to Disable)' }}
            </button>
        </form>
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
        <div class="p-6 space-y-8">
            <!-- Ongoing Bookings Section -->
            @if($ongoingBookings->isNotEmpty())
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <h6 class="text-sm font-black uppercase tracking-[0.2em] text-red-600">Live Now</h6>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($ongoingBookings as $booking)
                        <div class="bg-primary-50 border-2 border-primary-200 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6 hover:shadow-md transition-all">
                            <div class="flex items-center gap-5 w-full md:w-auto">
                                <div class="bg-primary-400 text-white font-black px-4 py-2 rounded-xl text-center shadow-inner">
                                    <div class="text-xs uppercase tracking-tighter opacity-80">Started</div>
                                    <div class="text-lg">{{ $booking->start_time->format('h:i A') }}</div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-xs font-bold text-primary-700 uppercase mb-0.5">{{ $booking->start_time->copy()->setTimezone('UTC')->diffForHumans($now->copy()->setTimezone('UTC')) }}</div>
                                    <h4 class="text-xl font-black text-slate-900 leading-tight">{{ $booking->customer->name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        @foreach($booking->items as $item)
                                            <span class="text-[10px] font-bold bg-white/60 text-primary-800 px-2 py-0.5 rounded border border-primary-300 uppercase tracking-tighter">{{ $item->service->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 w-full md:w-auto border-t md:border-t-0 pt-4 md:pt-0">
                                <div class="text-right mr-4 hidden md:block">
                                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pricing</div>
                                    <div class="text-xl font-black text-slate-900">{{ $shop->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}</div>
                                </div>
                                <div class="flex items-center gap-2 flex-1 md:flex-initial">
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" class="flex-1 md:flex-initial">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="w-full bg-slate-900 text-white font-bold py-2.5 px-6 rounded-xl hover:bg-slate-800 transition-all shadow-sm flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Complete Visit
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.appointments.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Permanently delete this appointment? This action cannot be undone.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2.5 text-red-600 bg-red-50 rounded-xl hover:bg-red-100 border border-red-200 transition-colors" title="Delete Permanently">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Upcoming Bookings Section -->
            @if($upcomingBookings->isNotEmpty())
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                    <h6 class="text-sm font-black uppercase tracking-[0.2em] text-primary-600">Coming Up Next</h6>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Time Slot</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Service & Duration</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($upcomingBookings as $booking)
                            <tr class="hover:bg-primary-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-base font-black text-slate-900">{{ $booking->start_time->format('h:i A') }}</div>
                                    <div class="text-[10px] font-bold text-primary-500 uppercase">{{ $booking->start_time->copy()->setTimezone('UTC')->diffForHumans($now->copy()->setTimezone('UTC')) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $booking->customer->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">
                                        {{ $booking->customer->phone }} 
                                        <span class="mx-1 text-slate-300">•</span> 
                                        Booked: {{ $booking->created_at->setTimezone($tz)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->items as $item)
                                            <span class="text-[10px] font-bold text-slate-500 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200">
                                                {{ $item->service->name }} ({{ $item->service->duration_minutes }}m)
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $sStyle = match($booking->status) {
                                            'pending' => 'bg-primary-100 text-primary-700',
                                            'confirmed' => 'bg-green-100 text-green-700',
                                            'in_progress' => 'bg-primary-600 text-white',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-[10px] font-bold {{ $sStyle }}">{{ ucwords(str_replace('_', ' ', $booking->status)) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($booking->status == 'pending' || $booking->status == 'confirmed')
                                            <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="in_progress">
                                                <button title="Start Visit" class="p-2 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 border border-primary-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($booking->status == 'pending')
                                            <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button title="Confirm Visit" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 border border-green-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?');">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button title="Cancel Visit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.appointments.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Permanently delete this appointment? This action cannot be undone.');">
                                            @csrf @method('DELETE')
                                            <button title="Delete Permanently" class="p-2 text-red-600 rounded-lg hover:bg-red-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Past Bookings Section -->
            @if($pastBookings->isNotEmpty())
            <div class="opacity-75 grayscale-[0.5] hover:opacity-100 hover:grayscale-0 transition-all">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                    <h6 class="text-sm font-black uppercase tracking-[0.2em] text-gray-500">Completed or Past Today</h6>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pastBookings as $booking)
                            <tr>
                                <td class="px-6 py-3 w-32 font-bold text-gray-500">{{ $booking->start_time->format('h:i A') }}</td>
                                <td class="px-6 py-3 font-semibold text-gray-600">{{ $booking->customer->name }}</td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold {{ $booking->status == 'completed' ? 'bg-primary-100 text-primary-700' : ($booking->status == 'in_progress' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-600') }}">{{ ucwords(str_replace('_', ' ', $booking->status)) }}</span>
                                        <form action="{{ route('admin.appointments.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Permanently delete this appointment? This action cannot be undone.');">
                                            @csrf @method('DELETE')
                                            <button title="Delete Permanently" class="text-red-400 hover:text-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    @endif
</div>

<!-- Quick Access Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <a href="{{ route('booking.kiosk', $shop->slug) }}" target="_blank" class="group relative overflow-hidden bg-slate-900 p-6 rounded-2xl shadow-lg border border-slate-800 transition-all hover:scale-[1.02] hover:shadow-2xl">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-125 transition-transform">
             <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        <div class="relative z-10">
            <h3 class="text-xl font-black text-white mb-2 flex items-center gap-2">
                Open Kiosk View
                <span class="bg-primary-500 text-slate-900 text-[10px] uppercase font-black px-2 py-0.5 rounded">Full Screen</span>
            </h3>
            <p class="text-slate-400 text-sm max-w-xs">Ideal for shop front tablets. Displays stylists, now serving, and booking QR code.</p>
        </div>
    </a>

    <a href="{{ route('admin.appointments.index', ['date' => \Carbon\Carbon::now($tz)->toDateString()]) }}" class="group relative overflow-hidden bg-white p-6 rounded-2xl shadow-lg border border-gray-200 transition-all hover:scale-[1.02] hover:shadow-2xl">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-125 transition-transform text-slate-900">
             <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div class="relative z-10">
            <h3 class="text-xl font-black text-slate-900 mb-2 flex items-center gap-2">
                Today's Schedule
                <span class="bg-primary-100 text-primary-700 text-[10px] uppercase font-black px-2 py-0.5 rounded">Quick Link</span>
            </h3>
            <p class="text-slate-500 text-sm max-w-xs">Instantly view and manage all of today's appointments in a focused list view.</p>
        </div>
    </a>
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
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg p-3 text-sm font-mono text-primary-500 break-all">
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
