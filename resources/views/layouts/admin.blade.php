<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - BarberShoppe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-slate-800">

    <nav class="bg-slate-900 border-b border-slate-800 fixed w-full z-50 top-0 start-0">
      <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 rtl:space-x-reverse transition-transform hover:scale-105">
            <span class="self-center text-2xl font-bold whitespace-nowrap text-white">Barber<span class="text-amber-500">Shoppe.</span></span>
        </a>
        <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center md:mr-0 transition-colors">Sign Out</button>
            </form>
            <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-400 rounded-lg md:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" aria-controls="navbar-sticky" aria-expanded="false">
              <span class="sr-only">Open main menu</span>
              <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
              </svg>
          </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
          <ul class="flex flex-col p-4 md:p-0 js-nav-menu mt-4 font-medium border border-gray-700 rounded-lg bg-slate-800 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-slate-900">
            <li>
              <a href="{{ route('admin.dashboard') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.dashboard') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Dashboard</a>
            </li>
            <li>
              <a href="{{ route('admin.appointments.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.appointments*') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Appointments</a>
            </li>
            <li>
                <a href="{{ route('admin.pos.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.pos*') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Quick Reserve</a>
            </li>
             <li>
              <a href="{{ route('admin.customers.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.customers*') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Customers</a>
            </li>
            <li>
               <a href="{{ route('admin.services.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.services*') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Services</a>
            </li>
            <li>
               <a href="{{ route('admin.availability.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.availability*') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Schedule</a>
            </li>
            <li>
               <a href="{{ route('admin.stylists.index') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.stylists*') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Stylists</a>
            </li>
             <li>
               <a href="{{ route('admin.shop.edit') }}" class="block py-2 px-3 rounded {{ request()->routeIs('admin.shop*') ? 'text-amber-500 md:p-0' : 'text-white hover:text-amber-500 md:p-0 transition-colors' }}">Settings</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="max-w-screen-xl mx-auto p-4 mt-20">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b border-gray-200 pb-4">
             <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">@yield('header')</h1>
                <p class="text-gray-500 text-sm mt-1">@yield('subheader', 'Manage your barber shop.')</p>
             </div>
        </div>

        @if(session('success'))
            <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 border border-green-200 shadow-sm" role="alert">
              <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
              </svg>
              <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
              </div>
              <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
              </button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-red-800 rounded-lg bg-red-50 border border-red-200 shadow-sm" role="alert">
                <span class="font-medium">Please fix the following errors:</span>
                <ul class="mt-2 text-sm list-inside list-disc">
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
            &copy; {{ date('Y') }} BarberShoppe. Designed with ❤️ in India.
        </footer>
    </div>

</body>
</html>
