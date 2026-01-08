@extends('layouts.admin')

@section('title', 'Customer History')
@section('header', 'Customer Profile')
@section('subheader', 'Detailed view of customer activity and history.')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-amber-500 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Customers
    </a>
</div>

<!-- Customer Overview Card -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-8 mb-8">
    <div class="flex flex-col md:flex-row items-center gap-8">
        <div class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center text-3xl font-bold text-slate-400">
            {{ strtoupper(substr($customer->name, 0, 1)) }}
        </div>
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-2xl font-bold text-slate-900">{{ $customer->name }}</h2>
            <div class="flex flex-col md:flex-row gap-4 mt-2 text-slate-500 justify-center md:justify-start">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ $customer->email }}
                </span>
                @if($customer->phone)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    {{ $customer->phone }}
                </span>
                @endif
            </div>
        </div>
        
        <!-- Stats -->
        <div class="flex gap-6 border-t md:border-t-0 md:border-l border-gray-100 pt-6 md:pt-0 pl-0 md:pl-8">
            <div class="text-center">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Visits</div>
                <div class="text-xl font-bold text-slate-900">{{ $customer->bookings->count() }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Spent</div>
                <div class="text-xl font-bold text-amber-600">{{ auth()->user()->shop->currency ?? '$' }} {{ number_format($totalSpent, 2) }}</div>
            </div>
             <div class="text-center">
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Last Visit</div>
                <div class="text-xl font-bold text-slate-900">{{ $lastVisit ? $lastVisit->format('M d') : '-' }}</div>
            </div>
        </div>
    </div>
</div>

<h3 class="text-lg font-bold text-slate-800 mb-4">Booking History</h3>

<!-- History Table -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($customer->bookings->isEmpty())
        <div class="p-8 text-center text-slate-500">No booking history found for this customer.</div>
    @else
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-slate-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Date & Time</th>
                        <th scope="col" class="px-6 py-4 font-bold">Services</th>
                        <th scope="col" class="px-6 py-4 font-bold">Total</th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($customer->bookings as $booking)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-800">{{ $booking->start_time->format('M d, Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $booking->start_time->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                             <div class="flex flex-wrap gap-2">
                                @foreach($booking->items as $item)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $item->service->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800">
                            {{ auth()->user()->shop->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap">
                             @php
                                $statusStyles = match($booking->status) {
                                    'confirmed' => 'bg-green-50 text-green-700 border-green-200 ring-green-600/20',
                                    'completed' => 'bg-blue-50 text-blue-700 border-blue-200 ring-blue-600/20',
                                    'cancelled' => 'bg-red-50 text-red-700 border-red-200 ring-red-600/20',
                                    default => 'bg-gray-50 text-gray-600 border-gray-200 ring-gray-500/10'
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-md border px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles }} capitalize">
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

@endsection
