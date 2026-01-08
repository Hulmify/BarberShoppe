@extends('layouts.admin')

@section('title', 'Shop Settings')
@section('header', 'Shop Settings')

@section('content')

<div class="card" style="max-width: 800px;">
    <form action="{{ route('admin.shop.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label>Shop Name</label>
            <input type="text" name="name" value="{{ old('name', $shop->name) }}" required>
        </div>

        <div class="form-group">
            <label>Booking URL Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $shop->slug) }}" required>
            <small style="color: #64748b; display: block; margin-top: 5px;">
                Changes will update your link: {{ config('app.url') }}/book/<b>{{ $shop->slug }}</b>
            </small>
        </div>
        
        <div class="form-group">
            <label>Custom Domain (Optional)</label>
            <input type="text" name="custom_domain" value="{{ old('custom_domain', $shop->custom_domain) }}" placeholder="e.g. book.joescuts.com">
            <small style="color: #64748b;">Requires CNAME record pointing to this server.</small>
        </div>

        <div class="form-group">
            <label>Currency</label>
            <select name="currency">
                <option value="INR" {{ old('currency', $shop->currency) == 'INR' ? 'selected' : '' }}>INR (₹) - Indian Rupee</option>
                <option value="USD" {{ old('currency', $shop->currency) == 'USD' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                <option value="EUR" {{ old('currency', $shop->currency) == 'EUR' ? 'selected' : '' }}>EUR (€) - Euro</option>
                <option value="GBP" {{ old('currency', $shop->currency) == 'GBP' ? 'selected' : '' }}>GBP (£) - British Pound</option>
                <option value="CAD" {{ old('currency', $shop->currency) == 'CAD' ? 'selected' : '' }}>CAD ($) - Canadian Dollar</option>
                <option value="AUD" {{ old('currency', $shop->currency) == 'AUD' ? 'selected' : '' }}>AUD ($) - Australian Dollar</option>
            </select>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3">{{ old('description', $shop->description) }}</textarea>
        </div>

        <div class="form-group">
            <label>Brand Primary Color</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="color" name="primary_color" value="{{ old('primary_color', $shop->primary_color) }}" style="width: 60px; height: 50px; padding: 0; border: none; cursor: pointer;">
                <span style="color: #64748b;">Click to choose</span>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

@endsection
