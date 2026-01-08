@extends('layouts.admin')

@section('title', 'Appointments')
@section('header', 'All Appointments')

@section('content')

<div class="card" style="padding: 20px;">
    <form method="GET" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
        <div>
            <label style="font-size: 0.85rem; color: #64748b; margin-bottom: 5px; display: block;">Filter Date</label>
            <input type="date" name="date" value="{{ request('date') }}" style="padding: 8px;">
        </div>
        <div>
            <label style="font-size: 0.85rem; color: #64748b; margin-bottom: 5px; display: block;">Status</label>
            <select name="status" style="padding: 8px; width: 120px;">
                <option value="">All</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary btn-sm" style="height: 38px;">Filter</button>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-sm" style="background: #e2e8f0; color: #334155; height: 38px; line-height: 22px;">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    @if($bookings->isEmpty())
        <div style="text-align: center; padding: 30px; color: #64748b;">
            No appointments found.
        </div>
    @else
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Customer</th>
                    <th>Services</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $booking->start_time->format('M d, Y') }}</div>
                        <div style="color: #64748b; font-size: 0.85rem;">{{ $booking->start_time->format('h:i A') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 500;">{{ $booking->customer->name }}</div>
                        <div style="color: #64748b; font-size: 0.85rem;">{{ $booking->customer->phone }}</div>
                    </td>
                    <td>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                            @foreach($booking->items as $item)
                                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">{{ $item->service->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td style="font-weight: 600;">
                        {{ auth()->user()->shop->currency ?? 'INR' }} {{ number_format($booking->total_price, 2) }}
                    </td>
                    <td>
                        <span class="status-badge status-{{ $booking->status }}">{{ $booking->status }}</span>
                    </td>
                    <td>
                        @if($booking->status == 'confirmed')
                            <div style="display: flex; gap: 5px;">
                                <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-sm" style="background: #dcfce7; color: #166534; border: none; cursor: pointer;">✓</button>
                                </form>
                                <form action="{{ route('admin.appointments.update', $booking->id) }}" method="POST" onsubmit="return confirm('Cancel this booking?');">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #ef4444; border: none; cursor: pointer;">✕</button>
                                </form>
                            </div>
                        @else
                            <span style="color: #cbd5e1;">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

@endsection
