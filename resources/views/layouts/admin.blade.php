<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - BarberShoppe</title>
    <link rel="icon" type="image/png" href="/app_favicon.png">
    @include('partials.pwa')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-slate-800">

    <nav class="bg-slate-900 border-b border-slate-800 sticky w-full z-50 top-0 start-0 shadow-lg shadow-slate-900/50 backdrop-blur-sm">
      <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto px-6 py-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 rtl:space-x-reverse transition-all duration-300 hover:scale-105 active:scale-95">
            <span class="self-center text-2xl font-bold whitespace-nowrap text-white drop-shadow-sm">Barber<span class="text-primary-500">Shoppe</span></span>
        </a>
        <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center md:mr-0 transition-all duration-200 hover:shadow-lg hover:shadow-red-600/30 active:scale-95">Sign Out</button>
            </form>
            <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-400 rounded-lg md:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 transition-all duration-200 active:scale-95" aria-controls="navbar-sticky" aria-expanded="false">
              <span class="sr-only">Open main menu</span>
              <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
              </svg>
          </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
          <ul class="flex flex-col p-4 md:p-0 js-nav-menu mt-4 font-medium border border-gray-700 rounded-xl bg-slate-800 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-slate-900 items-center shadow-lg md:shadow-none gap-1">
            <li>
              <a href="{{ route('admin.dashboard') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">Dashboard</a>
            </li>
            <li>
              <a href="{{ route('admin.appointments.index') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.appointments*') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">Appointments</a>
            </li>
            <li>
                <a href="{{ route('admin.pos.index') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.pos*') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">POS</a>
            </li>
            <li>
              <a href="{{ route('admin.customers.index') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.customers*') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">Customers</a>
            </li>
            <li>
              <a href="{{ route('admin.analytics.index') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.analytics*') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">Analytics</a>
            </li>
            <li>
               <a href="{{ route('admin.services.index') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.services*') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">Services</a>
            </li>
            <li>
               <a href="{{ route('admin.stylists.index') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.stylists*') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">Stylists</a>
            </li>
             <li>
               <a href="{{ route('admin.shop.edit') }}" class="block py-2 px-3 rounded-lg {{ request()->routeIs('admin.shop*') ? 'text-primary-500 md:p-0 bg-primary-500/10 md:bg-transparent' : 'text-white hover:text-primary-500 md:p-0 transition-all duration-200 hover:bg-slate-700/50 md:hover:bg-transparent' }}">Settings</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="max-w-screen-xl mx-auto px-6 py-6 mt-20">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 pb-6 border-b border-gray-200/80 gap-6">
             <div class="flex-1">
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight mb-2">@yield('header')</h1>
                <p class="text-gray-500 text-sm">@yield('subheader', 'Manage your barber shop.')</p>
             </div>
             <div class="flex flex-col items-end gap-3 w-full md:w-auto">
                @if(auth()->user()->trial_ends_at)
                    @php
                        $daysLeft = now()->diffInDays(auth()->user()->trial_ends_at, false);
                        $daysLeft = round($daysLeft);
                    @endphp
                    @if($daysLeft >= 0 && $daysLeft <= 7)
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 border border-primary-200 shadow-sm">
                            {{ $daysLeft == 1 ? 'Last day of subscription' : $daysLeft . ' days left in subscription' }}
                        </div>
                    @endif
                @endif
                <div class="bg-white rounded-xl border border-gray-200/80 px-4 py-3 shadow-sm hover:shadow-md transition-all duration-300">
                    <div id="live-clock" class="text-2xl font-bold text-slate-700 tracking-wider mb-1">00:00:00</div>
                    <div class="flex items-center justify-end gap-2">
                        <span id="live-timezone" class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-1 rounded border border-primary-100 uppercase shadow-sm">{{ auth()->user()->shop->timezone ?? config('app.timezone') }}</span>
                        <div id="live-date" class="text-xs font-semibold text-slate-400 uppercase tracking-widest">
                            {{ \Carbon\Carbon::now(auth()->user()->shop->timezone ?? config('app.timezone'))->format('l, F j') }}
                        </div>
                    </div>
                </div>
             </div>
        </div>

        <script>
            function updateClock() {
                const now = new Date();
                const options = { 
                    hour12: true, 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit',
                    timeZone: "{{ auth()->user()->shop->timezone ?? config('app.timezone') }}"
                };
                const timeStr = now.toLocaleTimeString('en-US', options);
                document.getElementById('live-clock').textContent = timeStr;
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>

        @if(session('success'))
            <div id="alert-3" class="flex items-center p-4 mb-6 text-green-800 rounded-xl bg-green-50 border border-green-200 shadow-md hover:shadow-lg transition-all duration-300" role="alert">
              <svg class="flex-shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
              </svg>
              <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
              </div>
              <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 transition-all duration-200 active:scale-90" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
              </button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-5 mb-6 text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-md hover:shadow-lg transition-all duration-300" role="alert">
                <span class="font-medium">Please fix the following errors:</span>
                <ul class="mt-3 text-sm list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main class="min-h-[50vh]">
            @yield('content')
        </main>
        
        <footer class="mt-20 text-center text-sm text-gray-400 py-6 border-t border-gray-200">
            &copy; {{ date('Y') }} Hulmify. Designed with ❤️ in India.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
