<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $shop->name }} | My Appointments</title>
    @include('partials.pwa')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 min-h-screen flex flex-col items-center py-10 px-4">

    <div class="w-full max-w-3xl">
        <header class="text-center mb-10 animate-fade-in-down">
            <div class="flex items-center justify-center mb-4">
                <a href="{{ request()->attributes->has('shop') ? route('shop.index') : $shop->booking_url }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Booking</span>
                </a>
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-slate-800 to-primary-600">
                My Appointments
            </h1>
            <p class="text-lg text-slate-500">{{ $shop->name }}</p>
        </header>
        
        @if(request()->query('booked'))
            <div class="mb-8 bg-sky-50 border border-sky-200 text-sky-800 rounded-2xl p-6 flex items-center gap-4 animate-fade-in">
                <div class="flex-shrink-0 w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center text-sky-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Booking Received!</h3>
                    <p class="text-sky-700/80">We've received your appointment request! Our team will review and confirm it shortly.</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Find Your Appointments
            </h2>
            
            <form action="{{ request()->attributes->has('shop') ? route('shop.search_appointments') : route('booking.search_appointments', ['slug' => $shop->slug]) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-slate-900">Enter your phone number</label>
                    <div class="flex gap-2">
                        <input type="tel" id="phone" name="phone" value="{{ $phone }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="(555) 123-4567" required>
                        <button type="submit" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-medium rounded-lg text-sm px-6 py-2.5 transition-colors">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($phone)
            <div class="space-y-4">
                <h2 class="text-xl font-bold px-2 flex items-center justify-between">
                    <span>Results for {{ $phone }}</span>
                    <span class="text-sm font-normal text-slate-500">{{ $bookings->count() }} appointment(s) found</span>
                </h2>

                @forelse($bookings as $booking)
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 transition-all hover:shadow-lg">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-lg font-bold text-slate-900">
                                        {{ $booking->start_time->setTimezone($shop->timezone ?? config('app.timezone'))->format('M d, Y') }}
                                    </span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span class="text-lg font-bold text-primary-600">
                                        {{ $booking->start_time->setTimezone($shop->timezone ?? config('app.timezone'))->format('h:i A') }}
                                        <span class="text-xs font-normal text-slate-400 ml-1">({{ $shop->timezone ?? config('app.timezone') }})</span>
                                    </span>
                                </div>
                                <div class="text-sm text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $booking->stylist ? $booking->stylist->name : 'Any Stylist' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        {{ $booking->start_time->diffInMinutes($booking->end_time) }} min
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right mr-2">
                                    <div class="text-xl font-bold text-slate-900">{{ $shop->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}</div>
                                    @php
                                        $statusMap = [
                                            'pending' => [
                                                'bg' => 'bg-sky-50',
                                                'text' => 'text-sky-700',
                                                'dot' => 'bg-sky-500',
                                                'label' => 'Awaiting Confirmation'
                                            ],
                                            'confirmed' => [
                                                'bg' => 'bg-emerald-50',
                                                'text' => 'text-emerald-700',
                                                'dot' => 'bg-emerald-500',
                                                'label' => 'Confirmed'
                                            ],
                                            'cancelled' => [
                                                'bg' => 'bg-rose-50',
                                                'text' => 'text-rose-700',
                                                'dot' => 'bg-rose-500',
                                                'label' => 'Cancelled'
                                            ],
                                            'completed' => [
                                                'bg' => 'bg-slate-50',
                                                'text' => 'text-slate-700',
                                                'dot' => 'bg-slate-400',
                                                'label' => 'Completed'
                                            ],
                                        ];
                                        $currentStatus = $statusMap[$booking->status] ?? [
                                            'bg' => 'bg-gray-50',
                                            'text' => 'text-gray-700',
                                            'dot' => 'bg-gray-400',
                                            'label' => $booking->status
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} border border-current/5 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus['dot'] }} animate-pulse"></span>
                                        {{ $currentStatus['label'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-50">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Services</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking->items as $item)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-gray-50 text-slate-700 text-sm border border-gray-100">
                                        {{ $item->service->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900">No appointments found</h3>
                        <p class="text-slate-500 mt-1">We couldn't find any appointments for this phone number.</p>
                        <a href="{{ request()->attributes->has('shop') ? route('shop.index') : $shop->booking_url }}" class="mt-6 inline-flex items-center text-primary-600 font-semibold hover:text-primary-700">
                            Book your first appointment
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    <!-- Spacer to prevent content from being hidden behind nav -->
    <div class="h-24 sm:hidden"></div>

    <!-- Mobile App Bottom Nav -->
    <div class="sm:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] w-[90%] max-w-sm h-16 bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl flex items-center justify-around px-4 shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
        <a href="{{ request()->attributes->has('shop') ? route('shop.index') : $shop->booking_url }}" class="flex flex-col items-center gap-1 group transition-all">
            <div class="p-2 rounded-xl {{ request()->routeIs('shop.index') || request()->routeIs('booking.index') ? 'text-primary-400' : 'text-slate-400' }} group-active:scale-90 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('shop.index') || request()->routeIs('booking.index') ? 'text-primary-400' : 'text-slate-500' }}">Home</span>
        </a>
        <a href="{{ request()->attributes->has('shop') ? route('shop.my_appointments') : route('booking.my_appointments', ['slug' => $shop->slug]) }}" class="flex flex-col items-center gap-1 group transition-all">
            <div class="p-2 rounded-xl {{ request()->routeIs('shop.my_appointments') || request()->routeIs('booking.my_appointments') ? 'text-primary-400' : 'text-slate-400' }} group-active:scale-90 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('shop.my_appointments') || request()->routeIs('booking.my_appointments') ? 'text-primary-400' : 'text-slate-500' }}">My Bookings</span>
        </a>
        <button type="button" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="flex flex-col items-center gap-1 group transition-all">
            <div class="p-2 rounded-xl text-slate-400 group-active:scale-90 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Top</span>
        </button>
    </div>

</body>

</html>
