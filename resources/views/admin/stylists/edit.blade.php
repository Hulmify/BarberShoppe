@extends('layouts.admin')

@section('title', 'Edit Stylist')
@section('header', 'Edit Stylist')
@section('subheader', 'Update stylist profile details.')

@section('content')
<div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <form action="{{ route('admin.stylists.update', $stylist) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-4">
            <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-gray-100">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 shrink-0">
                    @if($stylist->image_base64)
                        <img src="{{ $stylist->image_base64 }}" alt="{{ $stylist->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-amber-50">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-900 leading-tight">Current Photo</h3>
                    <p class="text-xs text-gray-500">To change, upload a new photo below.</p>
                </div>
            </div>

            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Stylist Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $stylist->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="e.g. John Doe" required>
            </div>

            <div>
                <label for="bio" class="block mb-2 text-sm font-medium text-gray-900">Bio</label>
                <textarea name="bio" id="bio" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Short description of the stylist...">{{ old('bio', $stylist->bio) }}</textarea>
            </div>

            <div>
                <label for="image" class="block mb-2 text-sm font-medium text-gray-900">New Profile Photo (Optional)</label>
                <input type="file" name="image" id="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-amber-500 focus:border-amber-500">
                <p class="mt-1 text-xs text-gray-500">Recommended size: 400x400 (Max 2MB)</p>
            </div>

            <div class="flex items-center space-x-6 pt-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $stylist->is_active) ? 'checked' : '' }} class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                    <label for="is_active" class="ms-2 text-sm font-medium text-gray-900">Active (Visible to customers)</label>
                </div>
            </div>

            <div>
                <label for="display_order" class="block mb-2 text-sm font-medium text-gray-900">Display Order</label>
                <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $stylist->display_order) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                <p class="mt-1 text-xs text-gray-500">Lower numbers appear first.</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 pt-6 border-t border-gray-100">
            <button type="submit" class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                Update Stylist
            </button>
            <a href="{{ route('admin.stylists.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
