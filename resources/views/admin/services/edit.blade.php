@extends('layouts.admin')

@section('title', isset($service) ? 'Edit Service' : 'Add Service')
@section('header', isset($service) ? 'Edit Service' : 'Add New Service')
@section('subheader', isset($service) ? 'Update service details and pricing.' : 'Create a new service for your customers.')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8">
        <form action="{{ isset($service) ? route('admin.services.update', $service->id) : route('admin.services.store') }}" method="POST">
            @csrf
            @if(isset($service))
                @method('PUT')
            @endif

            <div class="mb-6">
                <label for="name" class="block mb-2 text-sm font-medium text-slate-900">Service Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $service->name ?? '') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="e.g. Master Fade" required>
            </div>

            <div class="mb-6">
                <label for="description" class="block mb-2 text-sm font-medium text-slate-900">Description (Optional)</label>
                <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-slate-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-amber-500 focus:border-amber-500" placeholder="e.g. Precision haircut with hot towel finish.">{{ old('description', $service->description ?? '') }}</textarea>
            </div>

            <div class="grid gap-6 mb-8 md:grid-cols-2">
                <div>
                     <label for="price" class="block mb-2 text-sm font-medium text-slate-900">Price ({{ auth()->user()->shop->currency ?? '$' }})</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <span class="text-gray-500 font-bold text-sm">{{ auth()->user()->shop->currency ?? '$' }}</span>
                        </div>
                        <input type="number" id="price" step="0.01" name="price" value="{{ old('price', $service->price ?? '') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full ps-10 p-2.5" placeholder="0.00" required>
                    </div>
                </div>
                <div>
                    <label for="duration_minutes" class="block mb-2 text-sm font-medium text-slate-900">Duration (Minutes)</label>
                     <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
                        </div>
                        <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?? '30') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full ps-10 p-2.5" required>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm w-full sm:w-auto px-6 py-2.5 text-center transition-transform hover:-translate-y-0.5">
                    {{ isset($service) ? 'Update Service' : 'Create Service' }}
                </button>
                <a href="{{ route('admin.services.index') }}" class="text-slate-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
