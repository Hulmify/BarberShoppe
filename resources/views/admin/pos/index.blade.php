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
                <div class="space-y-3">
                    @foreach($services as $service)
                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors select-none">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" 
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
                    @endforeach
                </div>
            </div>
            
             <!-- 3. Date & Time -->
             <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">3. Date & Time</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Customer Type
        const radioBtns = document.querySelectorAll('input[name="customer_type"]');
        radioBtns.forEach(btn => {
            btn.addEventListener('change', function() {
                toggleCustomerType(this.value);
            });
        });

        // Toggle Services
        const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
        serviceCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });
    });

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
    
    function updateSummary() {
        const checkboxes = document.querySelectorAll('.service-checkbox:checked');
        const listContainer = document.getElementById('selected-services-list');
        const totalEl = document.getElementById('total-price');
        let total = 0;
        const currency = @json(auth()->user()->shop->currency ?? '$');
        
        listContainer.innerHTML = '';
        
        if (checkboxes.length === 0) {
            listContainer.innerHTML = '<p class="text-slate-400 italic">No services selected</p>';
        } else {
            checkboxes.forEach(cb => {
                const price = parseFloat(cb.dataset.price);
                const name = cb.dataset.name;
                total += price;
                
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center';
                item.innerHTML = `<span>${name}</span> <span class="font-medium">${currency} ${price.toFixed(2)}</span>`;
                listContainer.appendChild(item);
            });
        }
        
        totalEl.textContent = `${currency} ${total.toFixed(2)}`;
    }
</script>

@endsection
