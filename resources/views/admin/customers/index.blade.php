@extends('layouts.admin')

@section('title', 'Customers')
@section('header', 'My Customers')

@section('content')

<div class="card">
    @if($customers->isEmpty())
        <div style="text-align: center; padding: 30px; color: #64748b;">
            No customers yet.
        </div>
    @else
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Total Bookings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td style="font-weight: 600;">{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem;">{{ $customer->bookings_count }}</span></td>
                    <td>
                        <!-- Future: View History -->
                        <span style="color: #cbd5e1; cursor: not-allowed;">View History</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $customers->links() }}
        </div>
    @endif
</div>

@endsection
