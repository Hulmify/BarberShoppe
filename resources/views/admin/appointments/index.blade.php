@extends('layouts.admin')

@section('title', 'Appointments')
@section('header', 'All Appointments')
@section('subheader', 'View and manage all customer bookings.')

@section('content')

<!-- Filter Section -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-8">
    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">Filter Options</h3>
    <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="w-full md:w-auto">
            <label for="date" class="block mb-2 text-sm font-medium text-slate-700">Filter Date</label>
            <input type="date" id="date" name="date" value="{{ request('date') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
        </div>
        <div class="w-full md:w-48">
             <label for="status" class="block mb-2 text-sm font-medium text-slate-700">Status</label>
             <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none transition-colors">
                Apply Filters
            </button>
            <a href="{{ route('admin.appointments.index') }}" class="text-slate-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table Section -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($bookings->isEmpty())
        <div class="flex flex-col items-center justify-center p-12 text-center">
            <div class="p-4 bg-gray-50 rounded-full mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No appointments found</h3>
            <p class="text-slate-500 text-sm mt-1">Try adjusting your filters or booking a new appointment.</p>
        </div>
    @else
        <div class="relative overflow-x-auto" style="min-height: 400px;">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-slate-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Date & Time</th>
                        <th scope="col" class="px-6 py-4 font-bold">Customer</th>
                        <th scope="col" class="px-6 py-4 font-bold">Services</th>
                        <th scope="col" class="px-6 py-4 font-bold">Price</th>
                        <th scope="col" class="px-6 py-4 font-bold">Status</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($bookings as $booking)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-800 text-base">{{ $booking->start_time->format('M d, Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $booking->start_time->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800">{{ $booking->customer->name }}</div>
                            <div class="text-xs text-slate-500">{{ $booking->customer->phone ?? $booking->customer->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking->items as $item)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $item->service->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                            {{ auth()->user()->shop->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             @php
                                $statusStyles = match($booking->status) {
                                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200 ring-yellow-600/20',
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
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                @if($booking->status === 'pending')
                                    <!-- Confirm -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="p-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg transition-colors border border-green-200" title="Confirm Booking">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <!-- Reject/Cancel -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this booking?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors border border-red-200" title="Reject Booking">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                @elseif($booking->status === 'confirmed')
                                    <!-- Mark Completed -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="p-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg transition-colors border border-green-200" title="Mark Completed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <!-- Cancel -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors border border-red-200" title="Cancel Booking">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                @elseif($booking->status === 'completed')
                                    <!-- Revert to Confirmed -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" onsubmit="return confirm('Revert status to Confirmed?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="p-2 bg-gray-50 text-slate-600 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200 group" title="Undo / Revert to Confirmed">
                                             <div class="flex items-center gap-1 text-xs font-semibold px-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                Undo
                                             </div>
                                        </button>
                                    </form>
                                @elseif($booking->status === 'cancelled')
                                    <!-- Restore to Confirmed -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" onsubmit="return confirm('Restore this booking to Confirmed?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors border border-amber-200 group" title="Restore Booking">
                                            <div class="flex items-center gap-1 text-xs font-semibold px-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Restore
                                            </div>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
