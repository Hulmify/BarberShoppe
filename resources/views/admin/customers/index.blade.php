@extends('layouts.admin')

@section('title', 'Customers')
@section('header', 'My Customers')
@section('subheader', 'Manage your client base and view their history.')

@section('content')

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($customers->isEmpty())
        <div class="flex flex-col items-center justify-center p-12 text-center">
            <div class="p-4 bg-gray-50 rounded-full mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No customers yet</h3>
            <p class="text-slate-500 text-sm mt-1">Customers will appear here once they make a booking.</p>
        </div>
    @else
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-slate-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold">Name</th>
                        <th scope="col" class="px-6 py-4 font-bold">Email</th>
                        <th scope="col" class="px-6 py-4 font-bold">Phone</th>
                        <th scope="col" class="px-6 py-4 font-bold">Total Bookings</th>
                        <th scope="col" class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($customers as $customer)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                            {{ $customer->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $customer->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            {{ $customer->phone ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $customer->bookings_count }} Bookings
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <a href="{{ route('admin.customers.show', $customer->id) }}" class="inline-flex items-center font-medium text-amber-600 hover:text-amber-500 hover:underline transition-colors text-sm">
                                View History
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
    @endif
</div>

@endsection
