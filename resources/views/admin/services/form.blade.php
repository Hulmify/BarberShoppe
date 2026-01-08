@extends('layouts.admin')

@section('title', isset($service) ? 'Edit Service' : 'Add Service')
@section('header', isset($service) ? 'Edit Service' : 'Add New Service')

@section('content')

<div class="card" style="max-width: 600px;">
    <form action="{{ isset($service) ? route('admin.services.update', $service->id) : route('admin.services.store') }}" method="POST">
        @csrf
        @if(isset($service))
            @method('PUT')
        @endif

        <div class="form-group">
            <label>Service Name</label>
            <input type="text" name="name" value="{{ old('name', $service->name ?? '') }}" required placeholder="e.g. Buzz Cut">
        </div>

        <div class="form-group">
            <label>Description (Optional)</label>
            <textarea name="description" rows="3" placeholder="e.g. Quick trim with clippers">{{ old('description', $service->description ?? '') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $service->price ?? '') }}" required placeholder="0.00">
            </div>
            
            <div class="form-group">
                <label>Duration (Minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?? '30') }}" required>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary">{{ isset($service) ? 'Update Service' : 'Create Service' }}</button>
            <a href="{{ route('admin.services.index') }}" class="btn" style="background: #e2e8f0; color: #334155;">Cancel</a>
        </div>
    </form>
</div>

@endsection
