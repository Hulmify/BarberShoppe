@extends('layouts.admin')

@section('title', 'Shop Settings')
@section('header', 'Shop Settings')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">General Information</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Update your shop's basic details and configuration.</p>
    </div>

    <form action="{{ route('admin.shop.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-xl">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mb-4">Brand Logo</h3>
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="relative group">
                    <div class="w-32 h-32 rounded-xl border-2 border-dashed border-gray-300 bg-white overflow-hidden flex items-center justify-center">
                        @if($shop->logo)
                            <img src="{{ $shop->logo }}" id="logo-preview" class="w-full h-full object-contain">
                        @else
                            <div id="logo-placeholder" class="text-center p-4">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">No Logo</span>
                            </div>
                            <img src="" id="logo-preview" class="hidden w-full h-full object-contain">
                        @endif
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <p class="text-xs text-slate-500 max-w-sm">Upload your brand logo. This will be visible on your booking page and kiosk view. Recommended size: 512x512px (PNG, JPG).</p>
                    <div class="flex flex-wrap gap-2">
                        <label for="logo" class="cursor-pointer bg-white border border-gray-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition-colors">
                            Change Logo
                        </label>
                        <input type="file" id="logo" name="logo" class="hidden" accept="image/*" onchange="previewLogo(this)">
                        
                        @if($shop->logo)
                            <button type="button" onclick="removeLogo()" class="bg-red-50 text-red-600 border border-red-100 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition-colors">
                                Remove
                            </button>
                        @endif
                        <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                    </div>
                </div>
            </div>
        </div>

        <script>
            function previewLogo(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('logo-preview');
                        const placeholder = document.getElementById('logo-placeholder');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        if (placeholder) placeholder.classList.add('hidden');
                        document.getElementById('remove_logo').value = "0";
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function removeLogo() {
                const preview = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');
                const input = document.getElementById('logo');
                
                input.value = '';
                preview.src = '';
                preview.classList.add('hidden');
                
                if (!placeholder) {
                    const container = preview.parentElement;
                    const newPlaceholder = document.createElement('div');
                    newPlaceholder.id = 'logo-placeholder';
                    newPlaceholder.className = 'text-center p-4';
                    newPlaceholder.innerHTML = `
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">No Logo</span>
                    `;
                    container.appendChild(newPlaceholder);
                } else {
                    placeholder.classList.remove('hidden');
                }
                
                document.getElementById('remove_logo').value = "1";
            }
        </script>
        
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

            <div>
                <label for="timezone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Timezone</label>
                <select id="timezone" name="timezone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    @foreach(DateTimeZone::listIdentifiers() as $tz)
                        <option value="{{ $tz }}" {{ old('timezone', $shop->timezone) == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
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
