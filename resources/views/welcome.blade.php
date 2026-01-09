<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberShoppe - Manage Your Business</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-slate-800">

    <!-- Nav -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 fixed w-full z-50 top-0 start-0">
      <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <span class="self-center text-2xl font-bold whitespace-nowrap text-slate-900">Barber<span class="text-primary-500">Shoppe</span></span>
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-4 rtl:space-x-reverse">
             @auth
                <a href="{{ route('admin.dashboard') }}" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all">Dashboard</a>
             @else
                <a href="{{ route('login') }}" class="text-slate-700 hover:text-slate-900 font-medium text-sm px-4 py-2.5 transition-colors">Log In</a>
                <a href="{{ route('register') }}" class="text-white bg-primary-500 hover:bg-primary-600 focus:ring-4 focus:outline-none focus:ring-primary-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md shadow-primary-500/20">Get Started</a>
             @endauth
             
             <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-sticky" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
          <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent">
            <li>
              <a href="#" class="block py-2 px-3 text-white bg-primary-500 rounded md:bg-transparent md:text-primary-500 md:p-0" aria-current="page">Home</a>
            </li>
            <li>
              <a href="#features" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary-500 md:p-0 transition-colors">Features</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section class="min-h-screen flex items-center justify-center pt-20 relative overflow-hidden">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-primary-200/20 blur-3xl"></div>
            <div class="absolute top-[40%] -left-[10%] w-[40%] h-[40%] rounded-full bg-slate-200/40 blur-3xl"></div>
        </div>

        <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16">
            <h1 class="mb-4 text-5xl font-extrabold tracking-tight leading-none text-slate-900 md:text-6xl lg:text-7xl animate-fade-in-up" style="animation-delay: 0.1s;">
                The Modern Way to <br class="hidden md:block"/> Manage Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-500 to-secondary-500">Shop.</span>
            </h1>
            <p class="mb-8 text-lg font-normal text-slate-500 lg:text-xl sm:px-16 lg:px-48 animate-fade-in-up" style="animation-delay: 0.2s;">
                Effortless scheduling, custom booking domains, and powerful client management. Everything run your barber shop or salon, all in one place.
            </p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 animate-fade-in-up" style="animation-delay: 0.3s;">
                <a href="{{ route('register') }}" class="inline-flex justify-center items-center py-3 px-6 text-base font-medium text-center text-white rounded-full bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                    Start Free Trial
                    <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                </a>
                <a href="#features" class="inline-flex justify-center items-center py-3 px-6 text-base font-medium text-center text-slate-900 rounded-full border border-slate-200 hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 sm:ms-4 transition-all hover:-translate-y-1">
                    Learn More
                </a>
            </div>
            
            <div class="mt-12 text-sm text-slate-500 flex flex-col items-center gap-2 animate-fade-in-up" style="animation-delay: 0.4s;" id="demo">
                <span>Want to see it in action?</span>
                <a href="{{ url('/book/joes-cuts') }}" target="_blank" class="text-primary-600 hover:text-primary-700 font-semibold underline decoration-primary-300 decoration-2 underline-offset-4 hover:decoration-primary-500 transition-all">
                    View Demo Booking Page &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="bg-white py-24">
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <div class="max-w-screen-md mb-8 lg:mb-16 mx-auto text-center">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-slate-900">Designed for Growth</h2>
                <p class="text-slate-500 sm:text-xl">We focus on the boring stuff so you can focus on your craft.</p>
            </div>
            <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
                <div class="p-8 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-lg hover:border-primary-200 transition-all duration-300 group">
                    <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-xl bg-primary-100 group-hover:bg-primary-500 transition-colors duration-300">
                        <svg class="w-6 h-6 text-primary-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-slate-900">Smart Scheduling</h3>
                    <p class="text-slate-500">Intelligent availability engine prevents double bookings and manages your time off automatically.</p>
                </div>
                <div class="p-8 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-lg hover:border-primary-200 transition-all duration-300 group">
                    <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-xl bg-slate-100 group-hover:bg-slate-800 transition-colors duration-300">
                         <svg class="w-6 h-6 text-slate-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-slate-900">Custom Domains</h3>
                    <p class="text-slate-500">Look professional with your own branded booking link (e.g. book.yourshop.com) instead of a generic URL.</p>
                </div>
                <div class="p-8 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-lg hover:border-primary-200 transition-all duration-300 group">
                    <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-xl bg-green-100 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-6 h-6 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-slate-900">Revenue Tracking</h3>
                    <p class="text-slate-500">Track your estimated and confirmed revenue over time. Know exactly how your business is performing.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white py-12">
        <div class="max-w-screen-xl mx-auto px-4 text-center">
            <span class="text-2xl font-bold whitespace-nowrap text-white">Barber<span class="text-primary-500">Shoppe</span></span>
            <p class="mt-4 text-slate-400">© {{ date('Y') }} Hulmify. All rights reserved.</p>
        </div>
    </footer>
    
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }
    </style>

</body>
</html>
