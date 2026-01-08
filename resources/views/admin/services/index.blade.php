@extends('layouts.admin')

@section('title', 'Services')
@section('header', 'Services Config')

@section('content')

<div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">Add New Service</a>
</div>

<div class="card">
    @if($services->isEmpty())
        <div style="text-align: center; padding: 30px; color: #64748b;">
            No services added yet. Add one to start accepting bookings.
        </div>
    @else
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 40%;">Name</th>
                    <th style="width: 20%;">Duration</th>
                    <th style="width: 20%;">Price</th>
                    <th style="width: 20%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #0f172a;">{{ $service->name }}</div>
                        <div style="font-size: 0.85rem; color: #64748b;">{{ $service->description }}</div>
                    </td>
                    <td>{{ $service->duration_minutes }} min</td>
                    <td style="font-weight: 600;">{{ auth()->user()->shop->currency ?? 'INR' }} {{ number_format($service->price, 2) }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-sm" style="background: #e2e8f0; color: #334155; margin-right: 5px;">Edit</a>
                        
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #ef4444; border: none; cursor: pointer;">Del</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
