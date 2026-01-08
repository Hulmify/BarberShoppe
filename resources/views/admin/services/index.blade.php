@extends('layouts.admin')

@section('title', 'Services')
@section('header', 'Services Config')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <!-- Search Bar -->
    <form method="GET" action="{{ route('admin.services.index') }}" class="w-full md:w-1/2">
        <label for="search" class="mb-2 text-sm font-medium text-slate-800 sr-only">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-slate-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="search" id="search" name="search" value="{{ request('search') }}" class="block w-full p-4 ps-10 text-sm text-slate-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500" placeholder="Search services..." />
            <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors">Search</button>
        </div>
    </form>

    <!-- Add Button -->
    <a href="{{ route('admin.services.create') }}" class="w-full md:w-auto text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-3 focus:outline-none flex items-center justify-center gap-2 transition-colors shadow-sm">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
        </svg>
        Add New Service
    </a>
</div>

<div class="relative overflow-x-auto shadow-sm sm:rounded-xl border border-gray-200">
    @if($services->isEmpty())
        <div class="flex flex-col items-center justify-center p-12 text-center bg-white">
            <div class="p-4 bg-gray-50 rounded-full mb-4">
                 <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No services found</h3>
            <p class="text-slate-500 max-w-sm mt-1 mb-6">Try adjusting your search or create a new service.</p>
            <a href="{{ route('admin.services.create') }}" class="text-amber-600 hover:text-amber-700 font-bold hover:underline">Add a new service &rarr;</a>
        </div>
    @else
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-slate-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-4 font-bold">Name</th>
                    <th scope="col" class="px-6 py-4 font-bold">Duration</th>
                    <th scope="col" class="px-6 py-4 font-bold">Price</th>
                    <th scope="col" class="px-6 py-4 text-right font-bold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($services as $service)
                <tr class="bg-white hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-base font-bold text-slate-800">{{ $service->name }}</div>
                        <div class="text-xs text-slate-500">{{ Str::limit($service->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                            {{ $service->duration_minutes }} min
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800">
                        {{ auth()->user()->shop->currency ?? '$' }} {{ number_format($service->price, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                             <a href="{{ route('admin.services.edit', $service->id) }}" class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors border border-transparent hover:border-amber-200" title="Edit Service">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00 2 2h11a2 2 0 00 2-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>

                            <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-200" title="Delete Service">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($services->hasPages())
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $services->links() }}
        </div>
        @endif
    @endif
</div>

@endsection
