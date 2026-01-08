@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Overview')

@section('content')
<style>
    /* Dashboard specific over-rides if needed */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 30px; }
    .stat-card { background: white; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; }
    .stat-val { font-size: 2.5rem; font-weight: 700; color: #0f172a; margin-top: 5px; }
    .stat-label { color: #64748b; font-size: 0.95rem; font-weight: 500; }

    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; }
    td { padding: 20px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }
    .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: #e2e8f0; color: #64748b; }
    .status-confirmed { background: #dcfce7; color: #166534; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Today's Appointments</div>
        <div class="stat-val">{{ $todaysBookings->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Weekly Revenue (Est.)</div>
        <div class="stat-val" style="color: #166534;">{{ $shop->currency ?? 'INR' }} {{ number_format($potentialRevenue) }}</div>
        <div style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">Confirmed: {{ $shop->currency ?? 'INR' }} {{ number_format($weekRevenue) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Customers</div>
        <div class="stat-val">{{ $totalCustomers }}</div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Today's Schedule</h2>
        <span style="color: #64748b; font-size: 0.9rem;">{{ now()->toFormattedDateString() }}</span>
    </div>

    @if($todaysBookings->isEmpty())
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <p>No clean shaves scheduled for today.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th width="15%">Time</th>
                    <th width="30%">Customer</th>
                    <th width="30%">Services</th>
                    <th width="10%">Price</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todaysBookings as $booking)
                <tr>
                    <td style="font-weight: 700; font-size: 1.1rem; color: #0f172a;">{{ $booking->start_time->format('h:i A') }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $booking->customer->name }}</div>
                        <div style="font-size: 0.85rem; color: #64748b;">{{ $booking->customer->email }}</div>
                    </td>
                    <td>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                            @foreach($booking->items as $item)
                                <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;">{{ $item->service->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td style="font-weight: 600;">{{ $shop->currency ?? 'INR' }} {{ number_format($booking->total_price, 2) }}</td>
                    <td><span class="status-badge status-{{ $booking->status }}">{{ $booking->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="card">
    <h2 style="font-size: 1.25rem; margin-bottom: 20px;">Your Booking Link</h2>
    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px dashed #cbd5e1; display: flex; flex-wrap: wrap; gap: 15px; justify-content: space-between; align-items: center;">
        <div style="flex: 1;">
            <div style="font-family: monospace; font-size: 1rem; color: #0f172a; word-break: break-all;">
                @if($shop->custom_domain)
                    http://{{ $shop->custom_domain }}
                @else
                    {{ route('booking.via_slug', $shop->slug) }}
                @endif
            </div>
            <div style="font-size: 0.85rem; color: #64748b; margin-top: 5px;">Share this link to accept bookings online.</div>
        </div>
        <a href="{{ route('booking.via_slug', $shop->slug) }}" target="_blank" class="btn btn-primary btn-sm">Preview Shop</a>
    </div>
</div>

@endsection
