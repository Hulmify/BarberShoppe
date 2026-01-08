@extends('layouts.admin')

@section('title', 'Quick Reservation')
@section('header', 'Create New Booking')
@section('subheader', 'Point of Sale / Quick Reservation for walk-ins or phone bookings.')

@section('content')

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
                        <input type="radio" name="customer_type" value="existing" checked class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                        <span class="text-sm font-medium text-slate-700">Existing Customer</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="customer_type" value="new" class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                        <span class="text-sm font-medium text-slate-700">New Customer</span>
                    </label>
                </div>
                
                <!-- Existing Customer Select -->
                <div id="existing_customer_section">
                    <label for="customer_id" class="block mb-2 text-sm font-medium text-slate-900">Select Customer</label>
                    <select id="customer_id" name="customer_id" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
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
                        <input type="text" id="new_customer_name" name="new_customer_name" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="John Doe">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                            <label for="new_customer_email" class="block mb-2 text-sm font-medium text-slate-900">Email Address</label>
                            <input type="email" id="new_customer_email" name="new_customer_email" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="john@example.com">
                        </div>
                        <div>
                            <label for="new_customer_phone" class="block mb-2 text-sm font-medium text-slate-900">Phone Number</label>
                            <input type="tel" id="new_customer_phone" name="new_customer_phone" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="+1 234 567 890">
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
                        <input type="text" id="serviceSearch" value="{{ $search ?? '' }}" placeholder="Search services..." class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 p-2.5" onkeyup="filterServices()">
                    </div>
                </div>
                
                <div class="space-y-3" id="servicesList">
                    @forelse($services as $service)
                        <label class="service-item flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors select-none" data-name="{{ strtolower($service->name) }}" data-desc="{{ strtolower($service->description ?? '') }}">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" value="{{ $service->id }}" 
                                    data-price="{{ $service->price }}" 
                                    data-name="{{ $service->name }}" 
                                    class="service-checkbox w-5 h-5 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="date" class="block mb-2 text-sm font-medium text-slate-900">Date</label>
                        <input type="date" id="date" name="date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                    </div>
                     <div>
                        <label for="time" class="block mb-2 text-sm font-medium text-slate-900">Time Slot</label>
                        <select id="time" name="time" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                            <option value="">-- Select Time --</option>
                            @php
                                $start = \Carbon\Carbon::createFromTime(9, 0);
                                $end = \Carbon\Carbon::createFromTime(19, 0);
                            @endphp
                            @while($start->lte($end))
                                <option value="{{ $start->format('H:i') }}">{{ $start->format('h:i A') }}</option>
                                @php $start->addMinutes(15); @endphp
                            @endwhile
                        </select>
                    </div>
                    <div>
                        <label for="stylist_id" class="block mb-2 text-sm font-medium text-slate-900">Stylist (Optional)</label>
                        <select id="stylist_id" name="stylist_id" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                            <option value="">-- No Preference --</option>
                            @foreach($stylists as $stylist)
                                <option value="{{ $stylist->id }}">{{ $stylist->name }}</option>
                            @endforeach
                        </select>
                    </div>
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
                <span class="text-2xl font-bold text-amber-600" id="total-price">{{ auth()->user()->shop->currency ?? '$' }} 0.00</span>
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
            });
        });

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
            if (selectedServices.has(id)) {
                cb.checked = true;
            }
            cb.addEventListener('change', function() {
                if (this.checked) {
                    selectedServices.set(id, {
                        id: id,
                        name: this.dataset.name,
                        price: parseFloat(this.dataset.price)
                    });
                } else {
                    selectedServices.delete(id);
                }
                saveAndRefresh();
            });
        });

        updateSummary();

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
    
    function filterServices() {
        const searchTerm = document.getElementById('serviceSearch').value.toLowerCase();
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
    }
</script>

@endsection
