<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $shop->name }} | Book Appointment</title>
    @if($shop->logo)
        <link rel="icon" type="image/png" href="{{ $shop->logo }}">
    @else
        <link rel="icon" type="image/png" href="/app_favicon.png">
    @endif
    @include('partials.pwa')
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-color: {{ $shop->primary_color ?? '#4896bf' }};
            --brand-light: {{ ($shop->primary_color ?? '#4896bf') . '1a' }};
            --brand-medium: {{ ($shop->primary_color ?? '#4896bf') . '33' }};
        }
        body { font-family: 'Outfit', sans-serif; -webkit-tap-highlight-color: transparent; }
        
        .bg-brand { background-color: var(--brand-color) !important; }
        .text-brand { color: var(--brand-color) !important; }
        .border-brand { border-color: var(--brand-color) !important; }
        .bg-brand-light { background-color: var(--brand-light) !important; }
        .bg-brand-gradient { background: linear-gradient(135deg, #1e293b 0%, var(--brand-color) 100%) !important; -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; }
        
        /* Custom Scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Tactile Selections */
        .service-item.selected { border-color: var(--brand-color) !important; background-color: var(--brand-light) !important; scale: 1.02; box-shadow: 0 10px 25px -5px var(--brand-medium); }
        
        .stylist-card.selected { border-color: var(--brand-color) !important; background-color: white !important; scale: 1.05; box-shadow: 0 8px 20px -5px var(--brand-medium); }
        .stylist-card.selected .active-stylist-checkmark { opacity: 1; transform: scale(1); }

        .date-card.selected { background-color: var(--brand-color) !important; color: white !important; transform: scale(1.1); box-shadow: 0 10px 20px -5px var(--brand-medium); }
        
        /* Step Transitions */
        .step-active { opacity: 1 !important; transform: translateY(0) !important; pointer-events: auto !important; }
        
        /* Footer Visibility Fix */
        #footer.inactive { transform: translateY(110%); opacity: 0; pointer-events: none; }
        #footer.active { transform: translateY(0); opacity: 1; pointer-events: auto; }

        /* Service Item Icon States */
        .service-item .icon-box { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .service-item.selected .icon-box { background-color: var(--brand-color) !important; color: white !important; transform: rotate(90deg); }
        .service-item .plus-icon { display: block; }
        .service-item .check-icon { display: none; }
        .service-item.selected .plus-icon { display: none; }
        .service-item.selected .check-icon { display: block; transform: rotate(-90deg); }
    </style>

</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen">
    <!-- Progress Indicator -->
    <div class="fixed top-0 left-0 w-full z-[100] bg-white/80 backdrop-blur-md border-b border-gray-100 py-3 px-4 sm:hidden">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div id="step-indicator-1" class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-brand text-white flex items-center justify-center text-[10px] font-black">1</div>
                <span class="text-[10px] font-black uppercase tracking-widest text-brand">Service</span>
            </div>
            <div class="w-8 h-px bg-gray-200"></div>
            <div id="step-indicator-2" class="flex items-center gap-2 opacity-30">
                <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black">2</div>
                <span class="text-[10px] font-black uppercase tracking-widest">Time</span>
            </div>
            <div class="w-8 h-px bg-gray-200"></div>
            <div id="step-indicator-3" class="flex items-center gap-2 opacity-30">
                <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-black">3</div>
                <span class="text-[10px] font-black uppercase tracking-widest">Done</span>
            </div>
        </div>
    </div>

    <div class="w-full max-w-3xl mx-auto px-4 pt-16 sm:pt-10">
        <header class="text-center mb-8 animate-fade-in-down">
            <div class="flex justify-between items-center mb-6">
                <div class="w-10"></div> <!-- Spacer -->
                @if($shop->logo)
                    <img src="{{ $shop->logo }}" alt="{{ $shop->name }}" class="h-16 w-auto object-contain">
                @else
                    <div class="h-12 w-12 bg-brand rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-brand/20">
                        {{ substr($shop->name, 0, 1) }}
                    </div>
                @endif
                <a href="{{ request()->attributes->has('shop') ? route('shop.my_appointments') : route('booking.my_appointments', ['slug' => $shop->slug]) }}" class="text-slate-400 hover:text-brand transition-all p-2 bg-white rounded-xl shadow-sm border border-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </a>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-2 bg-brand-gradient">
                {{ $shop->name }}
            </h1>
            <p class="text-slate-500 font-medium text-sm sm:text-base px-4">{{ $shop->description ?? 'Secure Your Slot' }}</p>
        </header>

        <form id="bookingForm" onsubmit="submitBooking(event)" class="space-y-6">
            
            <!-- Step 1: Services -->
            <div id="section-services" class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-5 sm:p-8 transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Select Services</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">What are we doing today?</p>
                    </div>
                    @if($services->count() > 3)
                    <button type="button" onclick="clearServiceSelection()" class="w-10 h-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center transition-all active:scale-90 hover:bg-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    @endif
                </div>
                
                <!-- Search & Filters -->
                <div class="mb-6">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none transition-colors group-focus-within:text-brand">
                            <svg class="w-4 h-4 text-slate-400 group-focus-within:text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" id="serviceSearchInput" value="{{ $search ?? '' }}" placeholder="Search services..." class="bg-slate-50/50 border-none ring-1 ring-gray-100 text-slate-900 text-sm rounded-2xl focus:ring-2 focus:ring-brand block w-full pl-12 p-4 transition-all" onkeyup="filterBookingServices()">
                    </div>
                </div>
                
                <div class="grid gap-3" id="servicesGrid">
                    @forelse($services as $service)
                        <div class="service-item group relative flex items-center gap-4 p-5 rounded-3xl bg-white border border-gray-100 active:scale-[0.98] cursor-pointer transition-all duration-300"
                             data-id="{{ $service->id }}"
                             data-name="{{ strtolower($service->name) }}" 
                             data-desc="{{ strtolower($service->description ?? '') }}"
                             onclick="toggleService(this, {{ $service->id }}, {{ $service->price }}, {{ $service->duration_minutes }})">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 icon-box">
                                <svg class="w-6 h-6 plus-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12m6-6H6"/></svg>
                                <svg class="w-6 h-6 check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-brand transition-colors">{{ $service->name }}</h3>
                                <div class="text-xs font-bold text-slate-400 flex items-center gap-2 mt-1">
                                    <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>{{ $service->duration_minutes }} min</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-black text-slate-900">{{ $shop->currency ?? 'INR' }} {{ $service->price }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm text-center py-4">No services found</p>
                    @endforelse
                </div>
                <div id="serviceIdsContainer"></div>
                
                @if($services->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $services->links() }}
                    </div>
                @endif
            </div>

            <!-- Step 2: Select Stylist -->
            @if($stylists->count() > 0)
            <div id="step2" class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-5 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <div class="mb-6">
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">Choose Your Stylist</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Optional selection</p>
                </div>
                
                <div class="flex gap-4 overflow-x-auto pt-4 pb-6 no-scrollbar -mx-2 px-2">
                    <!-- No Preference Option -->
                    <div class="stylist-card group shrink-0 w-32 relative flex flex-col items-center p-4 rounded-3xl bg-slate-50 border border-transparent cursor-pointer transition-all duration-300"
                         onclick="selectStylist(this, null)">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden mb-3 bg-white flex items-center justify-center shadow-sm border border-gray-100 group-hover:border-brand/30 transition-all">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="font-black text-xs text-slate-900 text-center uppercase tracking-tight">Any Stylist</h3>
                        <!-- Selected Indicator -->
                        <div class="absolute top-1 right-1 w-6 h-6 bg-brand text-white rounded-full flex items-center justify-center opacity-0 scale-50 transition-all active-stylist-checkmark shadow-lg">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>

                    @foreach($stylists as $stylist)
                        <div class="stylist-card group shrink-0 w-32 relative flex flex-col items-center p-4 rounded-3xl bg-slate-50 border border-transparent cursor-pointer transition-all duration-300"
                             onclick="selectStylist(this, {{ $stylist->id }})">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden mb-3 bg-white flex items-center justify-center shadow-sm border border-gray-100 group-hover:border-brand/30 transition-all">
                                @if($stylist->image_base64)
                                    <img src="{{ $stylist->image_base64 }}" alt="{{ $stylist->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-brand/5 flex items-center justify-center text-brand font-black text-2xl">
                                        {{ substr($stylist->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <h3 class="font-black text-xs text-slate-900 text-center truncate w-full uppercase tracking-tight">{{ explode(' ', $stylist->name)[0] }}</h3>
                            <!-- Selected Indicator -->
                            <div class="absolute top-1 right-1 w-6 h-6 bg-brand text-white rounded-full flex items-center justify-center opacity-0 scale-50 transition-all active-stylist-checkmark shadow-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="stylist_id" id="stylistInput" value="">
            </div>
            @endif

            <!-- Step 3: Date & Time -->
            <div id="dateStep" class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-5 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <div class="mb-6">
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">Choose Date & Time</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Select your slot</p>
                </div>
                
                <!-- Modern Date Strip -->
                <div class="mb-6 bg-slate-50 rounded-3xl p-2">
                    <div class="flex gap-3 overflow-x-auto no-scrollbar py-1" id="datePickerStrip">
                        @for($i = 0; $i < 14; $i++)
                            @php 
                                $date = now()->addDays($i); 
                                $isToday = $i === 0;
                            @endphp
                            <div class="date-card group shrink-0 w-16 h-20 rounded-2xl flex flex-col items-center justify-center cursor-pointer transition-all duration-300 {{ $isToday ? 'bg-brand text-white shadow-lg shadow-brand/20' : 'bg-white text-slate-400 border border-gray-100 hover:border-brand/30' }}"
                                 data-date="{{ $date->format('Y-m-d') }}"
                                 onclick="selectDateCard(this, '{{ $date->format('Y-m-d') }}')">
                                <span class="text-[10px] font-black uppercase tracking-widest mb-1">{{ $date->format('D') }}</span>
                                <span class="text-xl font-black">{{ $date->format('d') }}</span>
                            </div>
                        @endfor
                    </div>
                    <input type="hidden" id="dateInput" name="date" value="{{ now()->format('Y-m-d') }}">
                </div>

                <!-- Time Filter Tabs -->
                <div class="flex p-1 bg-slate-100 rounded-2xl mb-6 gap-1">
                    <button type="button" onclick="filterBookingTimeGroups('all')" class="flex-1 py-3 px-2 rounded-xl text-[9px] font-black uppercase tracking-wider transition-all time-filter-btn bg-brand text-white shadow-sm" data-group="all">All</button>
                    <button type="button" onclick="filterBookingTimeGroups('morning')" class="flex-1 py-3 px-2 rounded-xl text-[9px] font-black uppercase tracking-wider transition-all time-filter-btn text-slate-500" data-group="morning">Morning</button>
                    <button type="button" onclick="filterBookingTimeGroups('afternoon')" class="flex-1 py-3 px-2 rounded-xl text-[9px] font-black uppercase tracking-wider transition-all time-filter-btn text-slate-500" data-group="afternoon">Noon</button>
                    <button type="button" onclick="filterBookingTimeGroups('evening')" class="flex-1 py-3 px-2 rounded-xl text-[9px] font-black uppercase tracking-wider transition-all time-filter-btn text-slate-500" data-group="evening">Night</button>
                </div>

                <div id="slotsContainer" class="p-4 rounded-3xl bg-slate-50/50 min-h-[200px]">
                    <!-- Morning Section -->
                    <div id="group-morning" class="hidden mb-6">
                        <h4 class="text-[9px] font-black uppercase tracking-widest text-slate-300 mb-4 px-2">Morning Slots</h4>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 slot-grid"></div>
                    </div>

                    <!-- Afternoon Section -->
                    <div id="group-afternoon" class="hidden mb-6">
                        <h4 class="text-[9px] font-black uppercase tracking-widest text-slate-300 mb-4 px-2">Afternoon Slots</h4>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 slot-grid"></div>
                    </div>

                    <!-- Evening Section -->
                    <div id="group-evening" class="hidden">
                        <h4 class="text-[9px] font-black uppercase tracking-widest text-slate-300 mb-4 px-2">Evening Slots</h4>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 slot-grid"></div>
                    </div>

                    <div id="no-slots-msg" class="flex flex-col items-center justify-center py-12 text-slate-300">
                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <p class="text-sm font-bold uppercase tracking-widest opacity-50">Checking for slots...</p>
                    </div>
                </div>
                <input type="hidden" name="time" id="timeInput">
            </div>

            <!-- Step 4: Details -->
            <div id="detailsStep" class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-6 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <div class="mb-8">
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">Final Details</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Almost there</p>
                </div>
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-secondary-100 text-secondary-700 text-sm font-bold">{{ $stylists->count() > 0 ? '4' : '3' }}</span>
                    Your Details
                </h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="customer_phone" class="block mb-2 text-sm font-medium text-slate-900">Phone Number</label>
                        <input type="tel" id="customer_phone" name="customer_phone" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="(555) 123-4567" required>
                    </div>
                    <div>
                        <label for="customer_email" class="block mb-2 text-sm font-medium text-slate-900">Email Address (Optional)</label>
                        <input type="email" id="customer_email" name="customer_email" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="name@example.com">
                    </div>
                    <div class="md:col-span-2">
                        <label for="customer_name" class="block mb-2 text-sm font-medium text-slate-900">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="John Doe" required>
                    </div>
                </div>
            </div>

            <!-- Page Bottom Spacer -->
            <div class="h-40 sm:h-32"></div>

            <!-- Enhanced Action Footer -->
            <div id="footer" class="fixed bottom-0 left-0 z-50 w-full bg-white border-t border-gray-100 shadow-[0_-12px_45px_rgba(0,0,0,0.12)] transition-all duration-300 inactive">
                <div class="max-w-3xl mx-auto px-6 py-5 pb-[calc(1.25rem+env(safe-area-inset-bottom,0px))] flex items-center justify-between gap-4">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Selection</span>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-black text-slate-900 leading-none">{{ $shop->currency ?? 'INR' }} <span id="totalPrice">0.00</span></span>
                            <span class="text-[10px] font-bold text-slate-400 tracking-tighter">/ <span id="totalDuration">0</span>m</span>
                        </div>
                    </div>
                    <button type="submit" id="bookBtn" class="flex-1 sm:flex-none text-white bg-slate-900 hover:bg-slate-800 active:scale-95 font-black uppercase tracking-widest text-[11px] px-8 py-4 rounded-2xl transition-all shadow-xl flex items-center justify-center gap-3">
                        <span>Confirm Booking</span>
                        <svg id="btnLoader" class="hidden w-4 h-4 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile App Bottom Nav -->
            <div id="mobile-nav" class="sm:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] w-[90%] max-w-sm h-16 bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl flex items-center justify-around px-4 shadow-[0_20px_50px_rgba(0,0,0,0.3)] transition-all duration-300">
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


        </form>
    </div>

    <script>
        const STORAGE_KEY = 'booking_state_' + {{ $shop->id }};
        const MY_APPOINTMENTS_URL = "{{ request()->attributes->has('shop') ? route('shop.my_appointments') : route('booking.my_appointments', ['slug' => $shop->slug]) }}";
        let selectedServices = new Set();
        let totalP = 0;
        let totalD = 0;

        document.addEventListener('DOMContentLoaded', () => {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                const state = JSON.parse(stored);
                selectedServices = new Set(state.ids);
                totalP = state.totalP;
                totalD = state.totalD;
                
                // Highlight already selected items on this page
                document.querySelectorAll('.service-item').forEach(el => {
                    const id = parseInt(el.dataset.id);
                    if (selectedServices.has(id)) {
                        el.classList.add('selected');
                    }
                });
            }
            updateSummary();
        });

        function saveState() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                ids: Array.from(selectedServices),
                totalP: totalP,
                totalD: totalD
            }));
        }

        function toggleService(el, id, price, duration) {
            if (selectedServices.has(id)) {
                selectedServices.delete(id);
                totalP -= price;
                totalD -= duration;
                el.classList.remove('selected');
            } else {
                selectedServices.add(id);
                totalP += price;
                totalD += duration;
                el.classList.add('selected');
                
                // Tactile feedback animation
                el.style.transform = 'scale(0.95)';
                setTimeout(() => el.style.transform = '', 100);
            }
            updateSummary();
            saveState();
        }

        function clearServiceSelection() {
            selectedServices.clear();
            totalP = 0;
            totalD = 0;
            document.querySelectorAll('.service-item').forEach(el => el.classList.remove('selected'));
            updateSummary();
            saveState();
        }

        function filterBookingServices() {
            const searchTerm = document.getElementById('serviceSearchInput').value.toLowerCase();
            document.querySelectorAll('.service-item').forEach(item => {
                const name = item.dataset.name || '';
                const desc = item.dataset.desc || '';
                item.style.display = (name.includes(searchTerm) || desc.includes(searchTerm)) ? '' : 'none';
            });
        }

        function selectStylist(el, id) {
            document.querySelectorAll('.stylist-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            document.getElementById('stylistInput').value = id || '';
            
            const dateStep = document.getElementById('dateStep');
            dateStep.classList.remove('hidden');
            setTimeout(() => {
                dateStep.classList.add('step-active');
                dateStep.scrollIntoView({behavior: 'smooth', block: 'start'});
                fetchSlots();
            }, 50);
        }

        function selectDateCard(el, date) {
            document.querySelectorAll('.date-card').forEach(c => {
                c.classList.remove('selected', 'bg-brand', 'text-white', 'shadow-lg', 'shadow-brand/20');
                c.classList.add('bg-white', 'text-slate-400', 'border-gray-100');
            });
            
            el.classList.remove('bg-white', 'text-slate-400', 'border-gray-100');
            el.classList.add('selected');
            
            document.getElementById('dateInput').value = date;
            fetchSlots();
        }

        function updateSummary() {
            document.getElementById('totalPrice').textContent = totalP.toFixed(2);
            document.getElementById('totalDuration').textContent = totalD;
            
            const ind2 = document.getElementById('step-indicator-2');
            const ind3 = document.getElementById('step-indicator-3');
            const footer = document.getElementById('footer');
            const mobileNav = document.getElementById('mobile-nav');
            const stylistStep = document.getElementById('step2');
            const dateStep = document.getElementById('dateStep');

            if (selectedServices.size > 0) {
                footer.classList.remove('inactive');
                footer.classList.add('active');
                if (mobileNav) mobileNav.classList.add('opacity-0', 'pointer-events-none', 'translate-y-10');
                if (ind2) ind2.classList.remove('opacity-30');
                
                if (stylistStep) {
                    stylistStep.classList.remove('hidden');
                    setTimeout(() => stylistStep.classList.add('step-active'), 10);
                } else {
                    dateStep.classList.remove('hidden');
                    setTimeout(() => {
                        dateStep.classList.add('step-active');
                        fetchSlots();
                    }, 10);
                }
            } else {
                footer.classList.remove('active');
                footer.classList.add('inactive');
                if (mobileNav) mobileNav.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-10');
                if (ind2) ind2.classList.add('opacity-30');
                if (ind3) ind3.classList.add('opacity-30');

                [stylistStep, dateStep, document.getElementById('detailsStep')].forEach(s => {
                    if (s) {
                        s.classList.remove('step-active');
                        setTimeout(() => s.classList.add('hidden'), 500);
                    }
                });
            }
            
            // Sync Hidden Inputs
            const idsContainer = document.getElementById('serviceIdsContainer');
            idsContainer.innerHTML = '';
            selectedServices.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'service_ids[]';
                input.value = id;
                idsContainer.appendChild(input);
            });
        }

        function formatTime12h(timeStr) {
            const [hours, minutes] = timeStr.split(':');
            let h = parseInt(hours);
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12;
            return h + ':' + minutes + ' ' + ampm;
        }

        let activeBookingTimeFilter = 'all';

        function filterBookingTimeGroups(group) {
            activeBookingTimeFilter = group;
            const sections = ['morning', 'afternoon', 'evening'];
            const buttons = document.querySelectorAll('.time-filter-btn');
            
            buttons.forEach(btn => {
                if (btn.dataset.group === group) {
                    btn.classList.add('bg-brand', 'text-white', 'shadow-sm');
                    btn.classList.remove('text-slate-500');
                } else {
                    btn.classList.remove('bg-brand', 'text-white', 'shadow-sm');
                    btn.classList.add('text-slate-500');
                }
            });

            sections.forEach(s => {
                const el = document.getElementById(`group-${s}`);
                const hasData = el.querySelector('.slot-grid').children.length > 0;
                
                if (hasData && (group === 'all' || group === s)) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }

        async function fetchSlots() {
            const date = document.getElementById('dateInput').value;
            if (!date) return;

            const stylistId = document.getElementById('stylistInput') ? document.getElementById('stylistInput').value : '';
            const container = document.getElementById('slotsContainer');
            const noSlotsMsg = document.getElementById('no-slots-msg');
            
            // Show Loader
            noSlotsMsg.innerHTML = '<svg class="inline w-8 h-8 text-gray-200 animate-spin fill-primary-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>';
            
            // Hide sections
            document.querySelectorAll('.slot-grid').forEach(g => g.innerHTML = '');
            document.getElementById('group-morning').classList.add('hidden');
            document.getElementById('group-afternoon').classList.add('hidden');
            document.getElementById('group-evening').classList.add('hidden');

            let baseUrl = window.location.href.split('?')[0];
            baseUrl = baseUrl.replace(/\/$/, '');
            const url = baseUrl + '/slots?date=' + date + '&duration=' + totalD + '&stylist_id=' + stylistId;
            
            try {
                const res = await fetch(url);
                const data = await res.json();
                
                if (data.slots.length === 0) {
                    noSlotsMsg.innerHTML = data.message || 'No slots available for this period.';
                    noSlotsMsg.classList.remove('hidden');
                } else {
                    noSlotsMsg.classList.add('hidden');
                    
                    let hasMorning = false;
                    let hasAfternoon = false;
                    let hasEvening = false;

                    data.slots.forEach(timeStr => {
                        const hour = parseInt(timeStr.split(':')[0]);
                        let group = 'evening';
                        if (hour < 12) {
                            group = 'morning';
                            hasMorning = true;
                        } else if (hour < 17) {
                            group = 'afternoon';
                            hasAfternoon = true;
                        } else {
                            hasEvening = true;
                        }

                        const grid = document.querySelector(`#group-${group} .slot-grid`);
                        const slotBtn = createBookingSlotButton(timeStr);
                        grid.appendChild(slotBtn);
                    });

                    // Initial Visibility based on filter
                    document.getElementById('group-morning').classList.toggle('hidden', !hasMorning || (activeBookingTimeFilter !== 'all' && activeBookingTimeFilter !== 'morning'));
                    document.getElementById('group-afternoon').classList.toggle('hidden', !hasAfternoon || (activeBookingTimeFilter !== 'all' && activeBookingTimeFilter !== 'afternoon'));
                    document.getElementById('group-evening').classList.toggle('hidden', !hasEvening || (activeBookingTimeFilter !== 'all' && activeBookingTimeFilter !== 'evening'));
                }
            } catch (e) {
                console.error(e);
                noSlotsMsg.innerHTML = 'Error loading slots.';
                noSlotsMsg.classList.remove('hidden');
            }
        }

        function createBookingSlotButton(timeStr) {
            const div = document.createElement('div');
            div.className = 'py-3 px-2 text-center bg-white border border-gray-200 hover:border-brand hover:bg-brand-light rounded-xl cursor-pointer transition-all shadow-sm flex flex-col items-center justify-center gap-0 text-slate-900';
            
            const [hours, minutes] = timeStr.split(':');
            const h = parseInt(hours);
            const displayH = h % 12 || 12;
            const ampm = h >= 12 ? 'PM' : 'AM';

            div.innerHTML = `
                <span class="text-base font-bold leading-tight">${displayH}:${minutes}</span>
                <span class="text-[9px] font-black uppercase tracking-[0.15em] opacity-50">${ampm}</span>
            `;

            div.onclick = function() { selectTime(this, timeStr); };
            return div;
        }

        function selectTime(el, time) {
            document.querySelectorAll('.slot-grid div').forEach(d => {
                d.classList.remove('bg-brand', 'text-white', 'border-brand', 'scale-105', 'shadow-md');
                d.classList.add('bg-white', 'text-slate-900', 'border-gray-200');
            });

            el.classList.remove('bg-white', 'text-slate-900', 'border-gray-200');
            el.classList.add('bg-brand', 'text-white', 'border-brand', 'scale-105', 'shadow-md');
            
            document.getElementById('timeInput').value = time;
            
            const detailsStep = document.getElementById('detailsStep');
            const ind3 = document.getElementById('step-indicator-3');
            if (ind3) ind3.classList.remove('opacity-30');
            
            detailsStep.classList.remove('hidden');
            setTimeout(() => {
                detailsStep.classList.add('step-active');
                detailsStep.scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 50);
        }

        async function submitBooking(e) {
            e.preventDefault();
            const btn = document.getElementById('bookBtn');
            const loader = document.getElementById('btnLoader');
            
            btn.disabled = true;
            loader.classList.remove('hidden');
            
            const formData = new FormData(e.target);
            
            try {
                const res = await fetch(window.location.href.split('?')[0], { 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await res.json();
                
                if (data.success) {
                    localStorage.removeItem(STORAGE_KEY);
                    const phone = document.getElementById('customer_phone').value;
                    window.location.href = MY_APPOINTMENTS_URL + '?phone=' + encodeURIComponent(phone) + '&booked=1';
                } else {
                    alert(data.message || 'Error: ' + JSON.stringify(data.errors || 'Unknown error'));
                }
            } catch (err) {
                alert('Request failed');
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
