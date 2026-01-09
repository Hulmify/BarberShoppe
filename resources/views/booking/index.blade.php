<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $shop->name }} | Book Appointment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-color: {{ $shop->primary_color ?? '#f59e0b' }};
            --brand-light: {{ ($shop->primary_color ?? '#f59e0b') . '1a' }}; /* 10% opacity */
            --brand-medium: {{ ($shop->primary_color ?? '#f59e0b') . '33' }}; /* 20% opacity */
        }
        body { font-family: 'Outfit', sans-serif; }
        
        .bg-brand { background-color: var(--brand-color) !important; }
        .text-brand { color: var(--brand-color) !important; }
        .border-brand { border-color: var(--brand-color) !important; }
        .bg-brand-light { background-color: var(--brand-light) !important; }
        .bg-brand-gradient { background: linear-gradient(to right, #1e293b, var(--brand-color)) !important; -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; }
        
        /* Override specific amber classes to use brand color */
        .text-amber-600, .text-amber-700 { color: var(--brand-color) !important; }
        .bg-amber-100, .bg-amber-50 { background-color: var(--brand-light) !important; }
        .bg-amber-500, .bg-amber-600 { background-color: var(--brand-color) !important; }
        .border-amber-400, .border-amber-500 { border-color: var(--brand-color) !important; }
        .from-amber-100 { --tw-gradient-from: var(--brand-light) !important; }
        .to-amber-200 { --tw-gradient-to: var(--brand-medium) !important; }
        .fill-amber-500 { fill: var(--brand-color) !important; }
        .ring-amber-200, .ring-amber-500\/20 { --tw-ring-color: var(--brand-medium) !important; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 min-h-screen flex flex-col items-center py-10 px-4">

    <div class="w-full max-w-3xl">
        <header class="text-center mb-10 animate-fade-in-down">
            <div class="flex justify-end mb-4">
                <a href="{{ request()->attributes->has('shop') ? route('shop.my_appointments') : route('booking.my_appointments', ['slug' => $shop->slug]) }}" class="text-sm font-medium text-slate-500 hover:text-amber-600 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    My Appointments
                </a>
            </div>
            @if($shop->logo)
                <div class="mb-6 flex justify-center">
                    <img src="{{ $shop->logo }}" alt="{{ $shop->name }}" class="h-20 w-auto object-contain">
                </div>
            @endif
            <h1 class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-slate-800 to-amber-600">
                {{ $shop->name }}
            </h1>
            <p class="text-lg text-slate-500">{{ $shop->description ?? 'Premium Barber Services' }}</p>
        </header>

        <form id="bookingForm" onsubmit="submitBooking(event)" class="space-y-6">
            
            <!-- Step 1: Services -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 transition-transform hover:scale-[1.01] duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">1</span>
                        Select Services
                    </h2>
                    <button type="button" onclick="clearServiceSelection()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors flex items-center gap-1.5 p-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Clear
                    </button>
                </div>
                
                <!-- Search Bar -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="serviceSearchInput" value="{{ $search ?? '' }}" placeholder="Search services..." class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 p-2.5" onkeyup="filterBookingServices()">
                    </div>
                </div>
                
                <div class="grid gap-4" id="servicesGrid">
                    @forelse($services as $service)
                        <div class="service-item group relative flex items-center justify-between p-4 rounded-xl border-2 border-gray-100 hover:border-amber-400 cursor-pointer transition-all duration-200"
                             data-id="{{ $service->id }}"
                             data-name="{{ strtolower($service->name) }}" 
                             data-desc="{{ strtolower($service->description ?? '') }}"
                             onclick="toggleService(this, {{ $service->id }}, {{ $service->price }}, {{ $service->duration_minutes }})">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-slate-900">{{ $service->name }}</h3>
                                <div class="text-sm text-slate-500 flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    {{ $service->duration_minutes }} min
                                    <span class="mx-1">•</span>
                                    <span>{{ $service->description }}</span>
                                </div>
                            </div>
                            <div class="text-xl font-bold text-slate-900 group-hover:text-amber-600 transition-colors">
                                {{ $shop->currency ?? '$' }} {{ $service->price }}
                            </div>
                            <!-- Selected Checkmark Indicator (Hidden by default) -->
                            <div class="absolute top-0 right-0 -mt-2 -mr-2 bg-amber-500 text-white rounded-full p-1 shadow-md opacity-0 scale-50 transition-all duration-200 checkmark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm text-center py-4">No services found</p>
                    @endforelse
                </div>
                <div id="serviceIdsContainer"></div>
                
                <!-- Pagination -->
                @if($services->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $services->links() }}
                    </div>
                @endif
            </div>

            <!-- Step 2: Select Stylist (Optional) -->
            @if($stylists->count() > 0)
            <div id="step2" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <h2 class="text-xl font-bold mb-2 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">2</span>
                    Choose Your Stylist
                </h2>
                <p class="text-sm text-slate-500 mb-6">Optional - Select a preferred stylist or skip to continue</p>
                
                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    @foreach($stylists as $stylist)
                        <div class="stylist-card group relative flex flex-col items-center p-4 rounded-xl border-2 border-gray-100 hover:border-amber-400 cursor-pointer transition-all duration-200"
                             onclick="selectStylist(this, {{ $stylist->id }})">
                            <div class="w-20 h-20 rounded-full overflow-hidden mb-3 bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center">
                                @if($stylist->image_base64)
                                    <img src="{{ $stylist->image_base64 }}" alt="{{ $stylist->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-10 h-10 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <h3 class="font-semibold text-base text-slate-900 text-center">{{ $stylist->name }}</h3>
                            @if($stylist->bio)
                                <p class="text-xs text-slate-500 text-center mt-1 line-clamp-2">{{ $stylist->bio }}</p>
                            @endif
                            <!-- Selected Checkmark -->
                            <div class="absolute top-0 right-0 -mt-2 -mr-2 bg-amber-500 text-white rounded-full p-1 shadow-md opacity-0 scale-50 transition-all duration-200 stylist-checkmark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- No Preference Option -->
                    <div class="stylist-card group relative flex flex-col items-center p-4 rounded-xl border-2 border-gray-100 hover:border-amber-400 cursor-pointer transition-all duration-200 border-amber-500 bg-amber-50"
                         onclick="selectStylist(this, null)">
                        <div class="w-20 h-20 rounded-full overflow-hidden mb-3 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-base text-slate-900 text-center">No Preference</h3>
                        <p class="text-xs text-slate-500 text-center mt-1">Any available stylist</p>
                        <!-- Selected Checkmark -->
                        <div class="absolute top-0 right-0 -mt-2 -mr-2 bg-amber-500 text-white rounded-full p-1 shadow-md opacity-100 scale-100 transition-all duration-200 stylist-checkmark">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="stylist_id" id="stylistInput" value="">
            </div>
            @endif

            <!-- Step 3: Date & Time -->
            <div id="dateStep" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">{{ $stylists->count() > 0 ? '3' : '2' }}</span>
                    Choose Date & Time
                </h2>
                
                <div class="mb-6">
                    <label for="dateInput" class="block mb-2 text-sm font-medium text-slate-900">Select Date</label>
                    <input type="date" id="dateInput" name="date" min="{{ date('Y-m-d') }}" onchange="fetchSlots()" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                </div>

                <label class="block mb-2 text-sm font-medium text-slate-900">Available Time Slots</label>
                
                <!-- Quick Filters -->
                <div class="flex gap-2 mb-4 overflow-x-auto pb-1 no-scrollbar">
                    <button type="button" onclick="filterBookingTimeGroups('all')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-amber-500 text-white shadow-sm border border-amber-500" data-group="all">All Day</button>
                    <button type="button" onclick="filterBookingTimeGroups('morning')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-white text-slate-500 border border-slate-200 hover:border-amber-400" data-group="morning">Morning</button>
                    <button type="button" onclick="filterBookingTimeGroups('afternoon')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-white text-slate-500 border border-slate-200 hover:border-amber-400" data-group="afternoon">Afternoon</button>
                    <button type="button" onclick="filterBookingTimeGroups('evening')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-white text-slate-500 border border-slate-200 hover:border-amber-400" data-group="evening">Evening</button>
                </div>

                <div id="slotsContainer" class="space-y-6 max-h-96 overflow-y-auto p-4 border border-gray-100 rounded-xl bg-slate-50/50 custom-scrollbar">
                    <!-- Morning Section -->
                    <div id="group-morning" class="hidden">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 17a5 5 0 100-10 5 5 0 000 10z"/></svg>
                            Morning
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 slot-grid"></div>
                    </div>

                    <!-- Afternoon Section -->
                    <div id="group-afternoon" class="hidden">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 17a5 5 0 100-10 5 5 0 000 10z"/></svg>
                            Afternoon
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 slot-grid"></div>
                    </div>

                    <!-- Evening Section -->
                    <div id="group-evening" class="hidden">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            Evening
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 slot-grid"></div>
                    </div>

                    <div id="no-slots-msg" class="text-center py-4 text-slate-400 text-sm">Select a date to view slots</div>
                </div>
                <input type="hidden" name="time" id="timeInput">
            </div>

            <!-- Step 4: Details -->
            <div id="detailsStep" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">{{ $stylists->count() > 0 ? '4' : '3' }}</span>
                    Your Details
                </h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="customer_phone" class="block mb-2 text-sm font-medium text-slate-900">Phone Number</label>
                        <input type="tel" id="customer_phone" name="customer_phone" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="(555) 123-4567" required>
                    </div>
                    <div>
                        <label for="customer_email" class="block mb-2 text-sm font-medium text-slate-900">Email Address (Optional)</label>
                        <input type="email" id="customer_email" name="customer_email" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="name@example.com">
                    </div>
                    <div class="md:col-span-2">
                        <label for="customer_name" class="block mb-2 text-sm font-medium text-slate-900">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="John Doe" required>
                    </div>
                </div>
            </div>

            <div class="h-24"></div>

            <div id="footer" class="fixed bottom-0 left-0 z-50 w-full h-20 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] transform translate-y-full transition-transform duration-300 flex items-center justify-center">
                <div class="w-full max-w-3xl px-4 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-sm text-slate-500">Total (<span id="totalDuration">0</span> min)</span>
                        <span class="text-2xl font-bold text-slate-900">{{ $shop->currency ?? '$' }} <span id="totalPrice">0.00</span></span>
                    </div>
                    <button type="submit" id="bookBtn" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-medium rounded-full text-lg px-8 py-2.5 focus:outline-none shadow-lg hover:-translate-y-0.5 transition-transform flex items-center gap-2">
                        <span>Confirm Booking</span>
                        <svg id="btnLoader" class="hidden w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <script>
        const STORAGE_KEY = 'booking_state_' + {{ $shop->id }};
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
                        el.classList.remove('border-gray-100');
                        el.classList.add('border-amber-500', 'bg-amber-50');
                        const checkmark = el.querySelector('.checkmark');
                        checkmark.classList.remove('opacity-0', 'scale-50');
                        checkmark.classList.add('opacity-100', 'scale-100');
                    }
                });
                updateSummary();
            }
        });

        function saveState() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                ids: Array.from(selectedServices),
                totalP: totalP,
                totalD: totalD
            }));
        }

        function toggleService(el, id, price, duration) {
            const checkmark = el.querySelector('.checkmark');
            
            if (selectedServices.has(id)) {
                selectedServices.delete(id);
                el.classList.remove('border-amber-500', 'bg-amber-50');
                el.classList.add('border-gray-100');
                checkmark.classList.remove('opacity-100', 'scale-100');
                checkmark.classList.add('opacity-0', 'scale-50');
                
                totalP -= price;
                totalD -= duration;
            } else {
                selectedServices.add(id);
                el.classList.remove('border-gray-100');
                el.classList.add('border-amber-500', 'bg-amber-50');
                checkmark.classList.remove('opacity-0', 'scale-50');
                checkmark.classList.add('opacity-100', 'scale-100');
                
                totalP += price;
                totalD += duration;
            }

            saveState();
            updateSummary();
        }

        function clearServiceSelection() {
            if (selectedServices.size === 0) return;
            
            selectedServices.clear();
            totalP = 0;
            totalD = 0;
            
            document.querySelectorAll('.service-item').forEach(el => {
                el.classList.remove('border-amber-500', 'bg-amber-50');
                el.classList.add('border-gray-100');
                const checkmark = el.querySelector('.checkmark');
                checkmark.classList.remove('opacity-100', 'scale-100');
                checkmark.classList.add('opacity-0', 'scale-50');
            });

            saveState();
            updateSummary();
        }
        
        function filterBookingServices() {
            const searchTerm = document.getElementById('serviceSearchInput').value.toLowerCase();
            const serviceItems = document.querySelectorAll('.service-item');
            
            serviceItems.forEach(item => {
                const name = item.dataset.name || '';
                const desc = item.dataset.desc || '';
                
                if (name.includes(searchTerm) || desc.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function selectStylist(el, id) {
            // Remove previous selection from all cards
            document.querySelectorAll('.stylist-card').forEach(card => {
                card.classList.remove('border-amber-500', 'bg-amber-50');
                card.classList.add('border-gray-100');
                const checkmark = card.querySelector('.stylist-checkmark');
                if (checkmark) {
                    checkmark.classList.remove('opacity-100', 'scale-100');
                    checkmark.classList.add('opacity-0', 'scale-50');
                }
            });

            // Add selection to clicked card
            el.classList.remove('border-gray-100');
            el.classList.add('border-amber-500', 'bg-amber-50');
            const checkmark = el.querySelector('.stylist-checkmark');
            if (checkmark) {
                checkmark.classList.remove('opacity-0', 'scale-50');
                checkmark.classList.add('opacity-100', 'scale-100');
            }

            document.getElementById('stylistInput').value = id || '';
            
            // Show next step (Date & Time)
            const dateStep = document.getElementById('dateStep');
            dateStep.classList.remove('hidden');
            setTimeout(() => {
                dateStep.classList.remove('opacity-0', 'translate-y-4');
                dateStep.scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 10);

            if (document.getElementById('dateInput').value) {
                fetchSlots();
            }
        }

        function updateSummary() {
            document.getElementById('totalPrice').textContent = totalP.toFixed(2);
            document.getElementById('totalDuration').textContent = totalD;
            
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

            const footer = document.getElementById('footer');
            const stylistStep = document.getElementById('step2');
            const dateStep = document.getElementById('dateStep');
            const detailsStep = document.getElementById('detailsStep');

            if (selectedServices.size > 0) {
                footer.classList.remove('translate-y-full');
                if (stylistStep) {
                    stylistStep.classList.remove('hidden');
                    setTimeout(() => {
                        stylistStep.classList.remove('opacity-0', 'translate-y-4');
                    }, 10);
                } else {
                    // If no stylists, show date step directly
                    dateStep.classList.remove('hidden');
                    setTimeout(() => {
                        dateStep.classList.remove('opacity-0', 'translate-y-4');
                    }, 10);
                }
            } else {
                footer.classList.add('translate-y-full');
                if (stylistStep) stylistStep.classList.add('opacity-0', 'translate-y-4');
                dateStep.classList.add('opacity-0', 'translate-y-4');
                detailsStep.classList.add('opacity-0', 'translate-y-4');
                setTimeout(() => {
                    if (stylistStep) stylistStep.classList.add('hidden');
                    dateStep.classList.add('hidden');
                    detailsStep.classList.add('hidden');
                }, 500);
            }
            
            if (document.getElementById('dateInput').value) {
                fetchSlots();
            }
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
                    btn.classList.remove('bg-white', 'text-slate-500', 'border-slate-200');
                    btn.classList.add('bg-amber-500', 'text-white', 'shadow-sm', 'border-amber-500');
                } else {
                    btn.classList.add('bg-white', 'text-slate-500', 'border-slate-200');
                    btn.classList.remove('bg-amber-500', 'text-white', 'shadow-sm', 'border-amber-500');
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
            noSlotsMsg.innerHTML = '<svg class="inline w-8 h-8 text-gray-200 animate-spin fill-amber-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>';
            
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
            div.className = 'py-3 px-2 text-center bg-white border border-gray-200 hover:border-amber-400 hover:bg-amber-50 rounded-xl cursor-pointer transition-all shadow-sm flex flex-col items-center justify-center gap-0.5';
            
            const [hours, minutes] = timeStr.split(':');
            const h = parseInt(hours);
            const displayH = h % 12 || 12;
            const ampm = h >= 12 ? 'PM' : 'AM';

            div.innerHTML = `
                <span class="text-sm font-bold text-slate-900">${displayH}:${minutes}</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">${ampm}</span>
            `;

            div.onclick = function() { selectTime(this, timeStr); };
            return div;
        }

        function selectTime(el, time) {
            // Remove previous selection styles
            document.querySelectorAll('.slot-grid div').forEach(d => {
                d.classList.remove('bg-amber-600', 'text-white', 'border-amber-700', 'ring-2', 'ring-amber-200');
                d.classList.add('bg-white', 'border-gray-200');
            });

            // Add new selection styles
            el.classList.remove('bg-white', 'border-gray-200');
            el.classList.add('bg-amber-600', 'text-white', 'border-amber-700', 'ring-2', 'ring-amber-200');
            
            document.getElementById('timeInput').value = time;
            
            const detailsStep = document.getElementById('detailsStep');
            detailsStep.classList.remove('hidden');
            setTimeout(() => {
                detailsStep.classList.remove('opacity-0', 'translate-y-4');
                detailsStep.scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 10);
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
                    alert('Booking Confirmed! ID: ' + data.booking_id);
                    location.reload();
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
