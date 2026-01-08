@extends('layouts.admin')

@section('title', 'Add Stylist')
@section('header', 'Add New Stylist')
@section('subheader', 'Create a new stylist profile for your shop.')

@section('content')
<div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <form action="{{ route('admin.stylists.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="space-y-4">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Stylist Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="e.g. John Doe" required>
            </div>

            <div>
                <label for="bio" class="block mb-2 text-sm font-medium text-gray-900">Bio</label>
                <textarea name="bio" id="bio" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Short description of the stylist...">{{ old('bio') }}</textarea>
            </div>

            <div>
                <label for="image" class="block mb-2 text-sm font-medium text-gray-900">Profile Photo</label>
                <input type="file" name="image" id="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-amber-500 focus:border-amber-500">
                <p class="mt-1 text-xs text-gray-500">Recommended size: 400x400 (Max 2MB)</p>
            </div>

            <div class="flex items-center space-x-6 pt-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                    <label for="is_active" class="ms-2 text-sm font-medium text-gray-900">Active (Visible to customers)</label>
                </div>
            </div>

            <div>
                <label for="display_order" class="block mb-2 text-sm font-medium text-gray-900">Display Order</label>
                <input type="number" name="display_order" id="display_order" value="{{ old('display_order', '0') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                <p class="mt-1 text-xs text-gray-500">Lower numbers appear first.</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 pt-6 border-t border-gray-100">
            <button type="submit" class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                Save Stylist
            </button>
            <a href="{{ route('admin.stylists.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
