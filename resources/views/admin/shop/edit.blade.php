@extends('layouts.admin')

@section('title', 'Shop Settings')
@section('header', 'Shop Settings')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">General Information</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Update your shop's basic details and configuration.</p>
    </div>

    <form action="{{ route('admin.shop.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Shop Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $shop->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>
            
            <div>
                <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Booking URL Slug</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                        /book/
                    </span>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $shop->slug) }}" class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Full Link: {{ config('app.url') }}/book/<span class="font-medium class='text-gray-900 dark:text-white'">{{ $shop->slug }}</span></p>
            </div>

            <div>
                 <label for="custom_domain" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Custom Domain (Optional)</label>
                 <input type="text" id="custom_domain" name="custom_domain" value="{{ old('custom_domain', $shop->custom_domain) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="e.g. book.joescuts.com">
                 <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Requires CNAME record pointing to this server.</p>
            </div>

            <div>
                <label for="currency" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Currency</label>
                <select id="currency" name="currency" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="INR" {{ old('currency', $shop->currency) == 'INR' ? 'selected' : '' }}>INR (₹) - Indian Rupee</option>
                    <option value="USD" {{ old('currency', $shop->currency) == 'USD' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                    <option value="EUR" {{ old('currency', $shop->currency) == 'EUR' ? 'selected' : '' }}>EUR (€) - Euro</option>
                    <option value="GBP" {{ old('currency', $shop->currency) == 'GBP' ? 'selected' : '' }}>GBP (£) - British Pound</option>
                    <option value="CAD" {{ old('currency', $shop->currency) == 'CAD' ? 'selected' : '' }}>CAD ($) - Canadian Dollar</option>
                    <option value="AUD" {{ old('currency', $shop->currency) == 'AUD' ? 'selected' : '' }}>AUD ($) - Australian Dollar</option>
                    <option value="AED" {{ old('currency', $shop->currency) == 'AED' ? 'selected' : '' }}>AED (د.إ) - UAE Dirham</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
            <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('description', $shop->description) }}</textarea>
        </div>

        <div class="mb-6">
             <label for="primary_color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Brand Primary Color</label>
             <div class="flex items-center gap-4">
                 <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', $shop->primary_color) }}" class="h-10 w-20 p-1 bg-white border border-gray-300 rounded cursor-pointer dark:bg-gray-700 dark:border-gray-600">
                 <span class="text-sm text-gray-500 dark:text-gray-400">Click to choose a color that matches your brand.</span>
             </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Save Changes
            </button>
        </div>
    </form>
</div>

@endsection
