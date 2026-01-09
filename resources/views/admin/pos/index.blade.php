@extends('layouts.admin')

@section('title', 'Quick Reservation')
@section('header', 'Create New Booking')
@section('subheader', 'Point of Sale / Quick Reservation for walk-ins or phone bookings.')

@section('content')

<style>
    #pos-slots-container::-webkit-scrollbar {
        width: 6px;
    }
    #pos-slots-container::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 10px;
    }
    #pos-slots-container::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    #pos-slots-container::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Form -->
    <div class="lg:col-span-2">
        <form action="{{ route('admin.pos.store') }}" method="POST" id="posForm">
            @csrf
            
            <!-- 1. Customer Selection -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">1. Customer Details</h3>
                
                <div class="flex gap-4 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="customer_type" value="existing" checked class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                        <span class="text-sm font-medium text-slate-700">Existing Customer</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="customer_type" value="new" class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                        <span class="text-sm font-medium text-slate-700">New Customer</span>
                    </label>
                </div>
                
                <!-- Existing Customer Select -->
                <div id="existing_customer_section">
                    <label for="customer_id" class="block mb-2 text-sm font-medium text-slate-900">Select Customer</label>
                    <select id="customer_id" name="customer_id" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">-- Choose Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? $customer->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- New Customer Inputs -->
                <div id="new_customer_section" class="hidden space-y-4">
                    <div>
                        <label for="new_customer_name" class="block mb-2 text-sm font-medium text-slate-900">Full Name</label>
                        <input type="text" id="new_customer_name" name="new_customer_name" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="John Doe">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_customer_phone" class="block mb-2 text-sm font-medium text-slate-900">Phone Number</label>
                            <input type="tel" id="new_customer_phone" name="new_customer_phone" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="+1 234 567 890">
                        </div>
                         <div>
                            <label for="new_customer_email" class="block mb-2 text-sm font-medium text-slate-900">Email Address (Optional)</label>
                            <input type="email" id="new_customer_email" name="new_customer_email" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="john@example.com">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 2. Services Selection -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">2. Select Services</h3>
                
                <!-- Search Bar -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="serviceSearch" value="{{ $search ?? '' }}" placeholder="Search services..." class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5" onkeyup="filterServices()">
                    </div>
                </div>
                
                <div class="space-y-3" id="servicesList">
                    @forelse($services as $service)
                        <label class="service-item flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors select-none" data-name="{{ strtolower($service->name) }}" data-desc="{{ strtolower($service->description ?? '') }}">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" value="{{ $service->id }}" 
                                    data-price="{{ $service->price }}" 
                                    data-name="{{ $service->name }}" 
                                    data-duration="{{ $service->duration_minutes }}"
                                    class="service-checkbox w-5 h-5 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <div>
                                    <div class="font-medium text-slate-900">{{ $service->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $service->duration_minutes }} mins</div>
                                </div>
                            </div>
                            <div class="font-bold text-slate-700">
                                {{ auth()->user()->shop->currency ?? '$' }} {{ number_format($service->price, 2) }}
                            </div>
                        </label>
                    @empty
                        <p class="text-slate-400 text-sm text-center py-4">No services found</p>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($services->hasPages())
                    <div class="mt-4 flex justify-center">
                        {{ $services->links() }}
                    </div>
                @endif
                <div id="hidden-services-container"></div>
            </div>
                        <!-- 3. Date, Time & Stylist -->
             <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">3. Assignment & Timing</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        @php
                            $tz = auth()->user()->shop->timezone ?? config('app.timezone');
                            $today = \Carbon\Carbon::now($tz)->toDateString();
                        @endphp
                        <label for="date" class="block mb-2 text-sm font-medium text-slate-900">Date</label>
                        <input type="date" id="date" name="date" value="{{ $today }}" min="{{ $today }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="stylist_id" class="block mb-2 text-sm font-medium text-slate-900">Stylist (Optional)</label>
                        <select id="stylist_id" name="stylist_id" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                            <option value="">-- No Preference --</option>
                            @foreach($stylists as $stylist)
                                <option value="{{ $stylist->id }}">{{ $stylist->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-slate-900">Select Time Slot</label>
                        <div id="slot-loader" class="hidden">
                            <svg class="animate-spin h-4 w-4 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Quick Filters -->
                    <div class="flex gap-2 mb-4 overflow-x-auto pb-1 no-scrollbar">
                        <button type="button" onclick="filterTimeGroups('all')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-primary-500 text-white shadow-sm border border-primary-500" data-group="all">All Day</button>
                        <button type="button" onclick="filterTimeGroups('morning')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-white text-slate-500 border border-slate-200 hover:border-primary-400" data-group="morning">Morning</button>
                        <button type="button" onclick="filterTimeGroups('afternoon')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-white text-slate-500 border border-slate-200 hover:border-primary-400" data-group="afternoon">Afternoon</button>
                        <button type="button" onclick="filterTimeGroups('evening')" class="time-filter-btn whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider transition-all bg-white text-slate-500 border border-slate-200 hover:border-primary-400" data-group="evening">Evening</button>
                    </div>
                    
                    <div id="pos-slots-container" class="space-y-6 max-h-96 overflow-y-auto p-4 border border-gray-100 rounded-xl bg-slate-50/50 custom-scrollbar">
                        <!-- Morning Section -->
                        <div id="group-morning" class="hidden">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 17a5 5 0 100-10 5 5 0 000 10z"/></svg>
                                Morning
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 slot-grid"></div>
                        </div>

                        <!-- Afternoon Section -->
                        <div id="group-afternoon" class="hidden">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 17a5 5 0 100-10 5 5 0 000 10z"/></svg>
                                Afternoon
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 slot-grid"></div>
                        </div>

                        <!-- Evening Section -->
                        <div id="group-evening" class="hidden">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                Evening
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 slot-grid"></div>
                        </div>

                        <div id="no-slots-msg" class="text-center py-8 text-slate-400 text-sm italic">
                            Select a client, at least one service, and a valid date to see available times.
                        </div>
                    </div>
                    <input type="hidden" name="time" id="time" required>
                    @error('time')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
        </form>
    </div>
    
    <!-- Right Column: Summary Card (Sticky) -->
    <div class="lg:col-span-1">
        <div class="sticky top-24 bg-white border border-gray-200 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2">Reservation Summary</h3>
            
            <div id="selected-services-list" class="space-y-2 mb-4 text-sm text-slate-600 min-h-[50px]">
                <p class="text-slate-400 italic">No services selected</p>
            </div>
            
            <div class="border-t border-gray-200 pt-4 flex justify-between items-center mb-6">
                <span class="text-base font-bold text-slate-700">Total</span>
                <span class="text-2xl font-bold text-primary-600" id="total-price">{{ auth()->user()->shop->currency ?? '$' }} 0.00</span>
            </div>
            
            <button type="submit" form="posForm" class="w-full text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-bold rounded-lg text-sm px-5 py-3.5 text-center transition-transform hover:-translate-y-0.5 shadow-md">
                Confirm Booking
            </button>
        </div>
    </div>
</div>

<script>
    const POS_STORAGE_KEY = 'pos_booking_services';
    let selectedServices = new Map(); // id -> {name, price}

    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Customer Type
        const radioBtns = document.querySelectorAll('input[name="customer_type"]');
        radioBtns.forEach(btn => {
            btn.addEventListener('change', function() {
                toggleCustomerType(this.value);
                fetchPosSlots();
            });
        });

        // Date/Stylist changes
        document.getElementById('date').addEventListener('change', fetchPosSlots);
        document.getElementById('stylist_id').addEventListener('change', fetchPosSlots);
        document.getElementById('customer_id').addEventListener('change', fetchPosSlots);
        document.getElementById('new_customer_name').addEventListener('input', fetchPosSlots);

        // Load Persistent State
        const stored = sessionStorage.getItem(POS_STORAGE_KEY);
        if (stored) {
            const arr = JSON.parse(stored);
            arr.forEach(s => selectedServices.set(s.id, s));
        }

        // Toggle Services
        const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
        serviceCheckboxes.forEach(cb => {
            const id = parseInt(cb.value);
            const isChecked = selectedServices.has(id);
            cb.checked = isChecked;
            
            // Highlight row if checked
            if (isChecked) {
                cb.closest('.service-item').classList.add('bg-primary-50', 'border-primary-300');
            }

            cb.addEventListener('change', function() {
                const row = this.closest('.service-item');
                if (this.checked) {
                    selectedServices.set(id, {
                        id: id,
                        name: this.dataset.name,
                        price: parseFloat(this.dataset.price),
                        duration: parseInt(this.dataset.duration || 30)
                    });
                    row.classList.add('bg-primary-50', 'border-primary-300');
                } else {
                    selectedServices.delete(id);
                    row.classList.remove('bg-primary-50', 'border-primary-300');
                }
                saveAndRefresh();
            });
        });

        updateSummary();
        fetchPosSlots(); // Initial fetch if everything set

        // Clear storage on form submit
        document.getElementById('posForm').addEventListener('submit', () => {
            sessionStorage.removeItem(POS_STORAGE_KEY);
        });
    });

    function saveAndRefresh() {
        sessionStorage.setItem(POS_STORAGE_KEY, JSON.stringify(Array.from(selectedServices.values())));
        updateSummary();
    }

    function toggleCustomerType(type) {
        const existingSection = document.getElementById('existing_customer_section');
        const newSection = document.getElementById('new_customer_section');
        
        if (type === 'existing') {
            existingSection.classList.remove('hidden');
            newSection.classList.add('hidden');
        } else {
            existingSection.classList.add('hidden');
            newSection.classList.remove('hidden');
        }
    }
    
    let activeTimeFilter = 'all';

    function filterTimeGroups(group) {
        activeTimeFilter = group;
        const sections = ['morning', 'afternoon', 'evening'];
        const buttons = document.querySelectorAll('.time-filter-btn');
        
        // Update button styles
        buttons.forEach(btn => {
            if (btn.dataset.group === group) {
                btn.classList.remove('bg-white', 'text-slate-500', 'border-slate-200');
                btn.classList.add('bg-primary-500', 'text-white', 'shadow-sm', 'border-primary-500');
            } else {
                btn.classList.add('bg-white', 'text-slate-500', 'border-slate-200');
                btn.classList.remove('bg-primary-500', 'text-white', 'shadow-sm', 'border-primary-500');
            }
        });

        // Toggle visibility based on active filter and presence of slots
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

    function filterServices() {
        const searchTerm = document.getElementById('serviceSearch').value.toLowerCase();
        const serviceItems = document.querySelectorAll('.service-item');
        
        serviceItems.forEach(item => {
            const name = item.dataset.name || '';
            const desc = item.dataset.desc || '';
            
            if (name.includes(searchTerm) || desc.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    async function fetchPosSlots() {
        const date = document.getElementById('date').value;
        const stylistId = document.getElementById('stylist_id').value;
        const loader = document.getElementById('slot-loader');
        const container = document.getElementById('pos-slots-container');
        const noSlotsMsg = document.getElementById('no-slots-msg');
        const timeInput = document.getElementById('time');
        
        // Reset state
        timeInput.value = '';
        
        const customerType = document.querySelector('input[name="customer_type"]:checked').value;
        const customerSelected = customerType === 'existing' 
            ? document.getElementById('customer_id').value 
            : document.getElementById('new_customer_name').value.trim();
        
        if (!date || selectedServices.size === 0 || !customerSelected) {
            document.getElementById('group-morning').classList.add('hidden');
            document.getElementById('group-afternoon').classList.add('hidden');
            document.getElementById('group-evening').classList.add('hidden');
            noSlotsMsg.classList.remove('hidden');
            noSlotsMsg.textContent = 'Select a client, at least one service, and a valid date to see available times.';
            return;
        }

        loader.classList.remove('hidden');
        noSlotsMsg.classList.remove('hidden'); 
        noSlotsMsg.textContent = 'Fetching available times...'; 
        
        // Hide existing grid while loading
        document.getElementById('group-morning').classList.add('hidden');
        document.getElementById('group-afternoon').classList.add('hidden');
        document.getElementById('group-evening').classList.add('hidden');
        
        let totalDuration = 0;
        selectedServices.forEach(s => {
            const d = parseInt(s.duration);
            totalDuration += isNaN(d) ? 30 : d;
        });

        // Use the dedicated admin POS slots route
        const url = `{{ route('admin.pos.slots') }}?date=${date}&duration=${totalDuration}&stylist_id=${stylistId}`;

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Failed to fetch slots');
            
            const data = await res.json();
            
            // Clear existing slots in grids
            document.querySelectorAll('.slot-grid').forEach(g => g.innerHTML = '');
            
            if (!data.slots || data.slots.length === 0) {
                document.getElementById('group-morning').classList.add('hidden');
                document.getElementById('group-afternoon').classList.add('hidden');
                document.getElementById('group-evening').classList.add('hidden');
                noSlotsMsg.classList.remove('hidden');
                noSlotsMsg.textContent = data.message || 'No available slots for this selection.';
            } else {
                noSlotsMsg.classList.add('hidden');
                
                let hasMorning = false;
                let hasAfternoon = false;
                let hasEvening = false;

                data.slots.forEach(slotData => {
                    let timeStr, isOccupied = false;
                    if (typeof slotData === 'string') {
                        timeStr = slotData;
                    } else {
                        timeStr = slotData.time;
                        isOccupied = slotData.occupied;
                    }

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
                    const slotBtn = createSlotButton(timeStr, isOccupied);
                    grid.appendChild(slotBtn);
                });

                // Apply both "Has Data" and "Filter" logic
                document.getElementById('group-morning').classList.toggle('hidden', !hasMorning || (activeTimeFilter !== 'all' && activeTimeFilter !== 'morning'));
                document.getElementById('group-afternoon').classList.toggle('hidden', !hasAfternoon || (activeTimeFilter !== 'all' && activeTimeFilter !== 'afternoon'));
                document.getElementById('group-evening').classList.toggle('hidden', !hasEvening || (activeTimeFilter !== 'all' && activeTimeFilter !== 'evening'));
                
                if (!hasMorning && !hasAfternoon && !hasEvening) {
                    noSlotsMsg.classList.remove('hidden');
                    noSlotsMsg.textContent = 'No available slots for this selection.';
                }
            }
        } catch (err) {
            console.error('POS Slot Fetch Error:', err);
            noSlotsMsg.classList.remove('hidden');
            noSlotsMsg.textContent = 'Failed to load available slots. Please check your connection or try again.';
        } finally {
            loader.classList.add('hidden');
        }
    }

    function formatTime12h(timeStr) {
        let [hours, minutes] = timeStr.split(':');
        hours = parseInt(hours);
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        return `${displayHours}:${minutes} ${ampm}`;
    }

    function createSlotButton(timeStr, isOccupied = false) {
        const div = document.createElement('div');
        let classes = 'time-slot-btn py-3 px-2 text-center border rounded-xl cursor-pointer transition-all shadow-sm flex flex-col items-center justify-center gap-0.5 ';
        
        if (isOccupied) {
            classes += 'bg-red-50 border-red-200 hover:border-red-400 hover:bg-red-100';
        } else {
            classes += 'bg-white border-gray-200 hover:border-primary-400 hover:bg-primary-50';
        }

        div.className = classes;
        div.dataset.occupied = isOccupied ? 'true' : 'false';
        
        const [hours, minutes] = timeStr.split(':');
        const h = parseInt(hours);
        const displayH = h % 12 || 12;
        const ampm = h >= 12 ? 'PM' : 'AM';

        div.innerHTML = `
            <span class="text-sm font-bold ${isOccupied ? 'text-red-900' : 'text-slate-900'}">${displayH}:${minutes}</span>
            <span class="text-[10px] font-bold ${isOccupied ? 'text-red-400' : 'text-slate-400'} uppercase tracking-tighter">${ampm}</span>
        `;

        div.onclick = function() { selectPosTime(this, timeStr); };
        return div;
    }
    
    function selectPosTime(el, time) {
        // Remove previous selection styles
        const allSlots = document.querySelectorAll('.time-slot-btn');
        allSlots.forEach(d => {
            const isOccupied = d.dataset.occupied === 'true';
            d.classList.remove('border-primary-500', 'bg-primary-600', 'text-white', 'ring-2', 'ring-primary-500/20');
            
            if (isOccupied) {
                d.classList.add('bg-red-50', 'border-red-200');
                d.classList.remove('bg-white', 'border-gray-200');
            } else {
                d.classList.add('bg-white', 'border-gray-200');
                d.classList.remove('bg-red-50', 'border-red-200');
            }
            
            // Fix nested spans color
            const spans = d.querySelectorAll('span');
            spans[0].classList.remove('text-white');
            spans[1].classList.remove('text-primary-100');

            if (isOccupied) {
                spans[0].classList.add('text-red-900');
                spans[1].classList.add('text-red-400');
                spans[0].classList.remove('text-slate-900');
                spans[1].classList.remove('text-slate-400');
            } else {
                spans[0].classList.add('text-slate-900');
                spans[1].classList.add('text-slate-400');
                spans[0].classList.remove('text-red-900');
                spans[1].classList.remove('text-red-400');
            }
        });

        // Add new selection styles
        el.classList.remove('bg-white', 'border-gray-200', 'hover:bg-primary-50', 'bg-red-50', 'border-red-200', 'hover:bg-red-100');
        el.classList.add('border-primary-500', 'bg-primary-600', 'text-white', 'ring-2', 'ring-primary-500/20');
        
        const selectedSpans = el.querySelectorAll('span');
        selectedSpans[0].classList.remove('text-slate-900', 'text-red-900');
        selectedSpans[0].classList.add('text-white');
        selectedSpans[1].classList.remove('text-slate-400', 'text-red-400');
        selectedSpans[1].classList.add('text-primary-100');
        
        document.getElementById('time').value = time;
    }
    
    function updateSummary() {
        const listContainer = document.getElementById('selected-services-list');
        const totalEl = document.getElementById('total-price');
        let total = 0;
        const currency = @json(auth()->user()->shop->currency ?? '$');
        
        listContainer.innerHTML = '';
        
        if (selectedServices.size === 0) {
            listContainer.innerHTML = '<p class="text-slate-400 italic">No services selected</p>';
        } else {
            selectedServices.forEach(s => {
                total += s.price;
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center text-sm';
                item.innerHTML = `<span>${s.name}</span> <span class="font-medium">${currency} ${s.price.toFixed(2)}</span>`;
                listContainer.appendChild(item);
            });
        }
        
        totalEl.textContent = `${currency} ${total.toFixed(2)}`;

        // Sync hidden inputs for form submission
        const container = document.getElementById('hidden-services-container');
        container.innerHTML = '';
        selectedServices.forEach(s => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'service_ids[]';
            input.value = s.id;
            container.appendChild(input);
        });

        // Trigger slot fetch when total duration changes
        fetchPosSlots();
    }
</script>

@endsection
