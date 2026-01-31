@extends('layouts.admin')

@section('title', 'Appointments')
@section('header', 'All Appointments')
@section('subheader', 'View and manage all customer bookings.')

@section('content')

<!-- View Toggle -->
<div class="flex justify-end mb-6">
    <a href="{{ route('admin.appointments.calendar') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200/80 rounded-xl text-sm font-medium text-slate-700 hover:bg-gray-50 hover:text-primary-600 hover:border-primary-200 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95 gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Calendar View
    </a>
</div>

<!-- Quick Filters -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ route('admin.appointments.index') }}" 
       class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ !request()->has('filter') && !request()->has('status') && !request()->has('statuses') && !request()->has('date') ? 'bg-slate-900 text-white shadow-md hover:shadow-lg hover:shadow-slate-900/30 active:scale-95' : 'bg-white border border-gray-200/80 text-slate-600 hover:bg-gray-50 hover:border-gray-300 shadow-sm active:scale-95' }}">
       All Appointments
    </a>
    <a href="{{ route('admin.appointments.index', ['filter' => 'today']) }}" 
       class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request('filter') === 'today' ? 'bg-slate-900 text-white shadow-md hover:shadow-lg hover:shadow-slate-900/30 active:scale-95' : 'bg-white border border-gray-200/80 text-slate-600 hover:bg-gray-50 hover:border-gray-300 shadow-sm active:scale-95' }}">
       Today
    </a>
    <a href="{{ route('admin.appointments.index', ['filter' => 'pending']) }}" 
       class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request('filter') === 'pending' ? 'bg-slate-900 text-white shadow-md hover:shadow-lg hover:shadow-slate-900/30 active:scale-95' : 'bg-white border border-gray-200/80 text-slate-600 hover:bg-gray-50 hover:border-gray-300 shadow-sm active:scale-95' }}">
       Pending Approvals
    </a>
    <a href="{{ route('admin.appointments.index', ['filter' => 'active']) }}" 
       class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request('filter') === 'active' ? 'bg-slate-900 text-white shadow-md hover:shadow-lg hover:shadow-slate-900/30 active:scale-95' : 'bg-white border border-gray-200/80 text-slate-600 hover:bg-gray-50 hover:border-gray-300 shadow-sm active:scale-95' }}">
       Active Visits
    </a>
    <a href="{{ route('admin.appointments.index', ['filter' => 'completed']) }}" 
       class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request('filter') === 'completed' ? 'bg-slate-900 text-white shadow-md hover:shadow-lg hover:shadow-slate-900/30 active:scale-95' : 'bg-white border border-gray-200/80 text-slate-600 hover:bg-gray-50 hover:border-gray-300 shadow-sm active:scale-95' }}">
       Completed
    </a>
</div>

<!-- Filter Section -->
<div class="bg-white border border-gray-200/80 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 mb-8">
    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-5">Detailed Filters</h3>
    <form method="GET" class="space-y-4">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-full md:w-64">
                <label for="date" class="block mb-2 text-sm font-medium text-slate-700">Filter Date</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
            </div>
            <div class="flex-1">
                <label class="block mb-2 text-sm font-medium text-slate-700">Filter by Statuses</label>
                <div class="flex flex-wrap gap-x-6 gap-y-2 mt-3">
                    @foreach(['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'] as $status)
                        @php
                            $checked = (is_array(request('statuses')) && in_array($status, request('statuses'))) || 
                                       request('status') == $status ||
                                       (request('filter') === 'pending' && $status === 'pending') ||
                                       (request('filter') === 'active' && in_array($status, ['confirmed', 'in_progress'])) ||
                                       (request('filter') === 'completed' && $status === 'completed');
                        @endphp
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="statuses[]" value="{{ $status }}" 
                                {{ $checked ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 transition-colors">
                            <span class="ml-2 text-sm text-slate-600 group-hover:text-slate-900 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="text-xs text-slate-500">
                @if(request()->anyFilled(['date', 'statuses', 'status', 'filter']))
                    Showing filtered results
                @else
                    Showing all appointments
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.appointments.index') }}" class="text-slate-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors">
                    Reset
                </a>
                <button type="submit" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-medium rounded-lg text-sm px-8 py-2.5 focus:outline-none transition-colors">
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Table Section -->
<div class="bg-white border border-gray-200/80 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
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
                        <th scope="col" class="px-6 py-4 font-bold">
                            <a href="{{ route('admin.appointments.index', array_merge(request()->all(), ['sort_by' => 'start_time', 'sort_dir' => request('sort_by') === 'start_time' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="group inline-flex items-center gap-1 hover:text-primary-600 transition-colors">
                                Date & Time
                                @if(request('sort_by') === 'start_time' || !request()->has('sort_by'))
                                    <svg class="w-3 h-3 {{ (request('sort_dir', 'desc') === 'asc' && request('sort_by') === 'start_time') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @else
                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-4 font-bold">
                            <a href="{{ route('admin.appointments.index', array_merge(request()->all(), ['sort_by' => 'customer_name', 'sort_dir' => request('sort_by') === 'customer_name' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="group inline-flex items-center gap-1 hover:text-primary-600 transition-colors">
                                Customer
                                @if(request('sort_by') === 'customer_name')
                                    <svg class="w-3 h-3 {{ request('sort_dir') === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @else
                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-4 font-bold">Services</th>
                        <th scope="col" class="px-6 py-4 font-bold">Stylist</th>
                        <th scope="col" class="px-6 py-4 font-bold">
                            <a href="{{ route('admin.appointments.index', array_merge(request()->all(), ['sort_by' => 'total_price', 'sort_dir' => request('sort_by') === 'total_price' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="group inline-flex items-center gap-1 hover:text-primary-600 transition-colors">
                                Price
                                @if(request('sort_by') === 'total_price')
                                    <svg class="w-3 h-3 {{ request('sort_dir') === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @else
                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-4 font-bold">
                            <a href="{{ route('admin.appointments.index', array_merge(request()->all(), ['sort_by' => 'status', 'sort_dir' => request('sort_by') === 'status' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="group inline-flex items-center gap-1 hover:text-primary-600 transition-colors">
                                Status
                                @if(request('sort_by') === 'status')
                                    <svg class="w-3 h-3 {{ request('sort_dir') === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @else
                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($bookings as $booking)
                    <tr id="booking-{{ $booking->id }}" class="bg-white hover:bg-gray-50 transition-colors {{ request('highlight') == $booking->id ? 'bg-yellow-50/50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-800 text-base">{{ $booking->start_time->setTimezone($tz)->format('M d, Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $booking->start_time->setTimezone($tz)->format('h:i A') }}</div>
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
                            @php
                                $totalDuration = $booking->items->sum(fn($i) => $i->service->duration_minutes ?? 0);
                                $totalItems = $booking->items->count();
                            @endphp
                            <div class="text-xs text-slate-400 font-medium mt-3">
                                {{ $totalItems }} services, {{ $totalDuration }} mins
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" class="m-0">
                                @csrf @method('PUT')
                                <select 
                                    name="stylist_id" 
                                    onchange="this.form.submit()" 
                                    class="text-xs rounded-lg border-gray-300 bg-gray-50 focus:ring-primary-500 focus:border-primary-500 block w-full p-1 font-medium text-slate-700"
                                >
                                    <option value="">Unassigned</option>
                                    @foreach($stylists as $stylist)
                                        <option value="{{ $stylist->id }}" {{ $booking->stylist_id == $stylist->id ? 'selected' : '' }}>
                                            {{ $stylist->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                            {{ auth()->user()->shop->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             @php
                                $statusStyles = match($booking->status) {
                                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200 ring-yellow-600/20',
                                    'confirmed' => 'bg-green-50 text-green-700 border-green-200 ring-green-600/20',
                                    'in_progress' => 'bg-primary-600 text-white border-primary-700 ring-primary-500/20',
                                    'completed' => 'bg-primary-50 text-primary-700 border-primary-200 ring-primary-600/20',
                                    'cancelled' => 'bg-red-50 text-red-700 border-red-200 ring-red-600/20',
                                    default => 'bg-gray-50 text-gray-600 border-gray-200 ring-gray-500/10'
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-md border px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles }}">
                                {{ ucwords(str_replace('_', ' ', $booking->status)) }}
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
                                    <!-- Start Visit -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="p-2 bg-primary-50 text-primary-600 hover:bg-primary-100 rounded-lg transition-colors border border-primary-200" title="Start Visit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                                @elseif($booking->status === 'in_progress')
                                    <!-- Mark Completed -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="p-2 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg transition-colors border border-green-200" title="Mark Completed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                @elseif($booking->status === 'completed')
                                    <!-- Revert to In Progress -->
                                    <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" onsubmit="return confirm('Revert status to In Progress?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="p-2 bg-gray-50 text-slate-600 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200 group" title="Undo / Revert to In Progress">
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
                                        <button type="submit" class="p-2 bg-primary-50 text-primary-600 hover:bg-primary-100 rounded-lg transition-colors border border-primary-200 group" title="Restore Booking">
                                            <div class="flex items-center gap-1 text-xs font-semibold px-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Restore
                                            </div>
                                        </button>
                                    </form>
                                @endif

                                <!-- Permanent Delete -->
                                <form action="{{ route('admin.appointments.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Permanently delete this appointment? This action cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100" title="Delete Permanently">
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
        
@if($bookings->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const highlightId = urlParams.get('highlight');
        
        if (highlightId) {
            const row = document.getElementById('booking-' + highlightId);
            if (row) {
                // Scroll into view
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Add temporary highlight effect
                row.classList.add('bg-yellow-100');
                row.classList.remove('bg-white');
                
                setTimeout(() => {
                    row.classList.remove('bg-yellow-100');
                    row.classList.add('transition-colors', 'duration-1000');
                }, 2000);
            }
        }
    });
</script>

@endsection
