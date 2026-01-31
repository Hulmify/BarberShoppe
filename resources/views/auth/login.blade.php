<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BarberShoppe</title>
    @include('partials.pwa')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <!-- Optional Background Decoration -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-primary-200/20 blur-3xl"></div>
        <div class="absolute bottom-[20%] -right-[10%] w-[30%] h-[30%] rounded-full bg-slate-200/30 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-white border border-gray-100/80 rounded-3xl shadow-2xl p-8 relative z-10 animate-fade-in-up hover:shadow-3xl transition-all duration-500">
        <div class="text-center mb-8">
            <a href="/" class="inline-block text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 mb-2 hover:scale-105 transition-transform duration-200">
                Barber<span class="text-primary-500">Shoppe</span>
            </a>
            <h2 class="text-xl font-bold text-slate-900 mt-4">Welcome Back</h2>
            <p class="text-slate-500 mt-1 text-sm">Sign in to manage your appointments</p>
        </div>

        @if ($errors->any())
            <div class="flex items-center p-4 mb-6 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-md hover:shadow-lg transition-all duration-300" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                     {{ $errors->first() }}
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-slate-900">Email Address</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all duration-200 hover:border-gray-400" placeholder="name@company.com">
            </div>
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-slate-900">Password</label>
                <input type="password" name="password" id="password" required class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all duration-200 hover:border-gray-400" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center transition-all duration-200 hover:shadow-lg hover:shadow-slate-900/30 active:scale-95">Sign In</button>
            
            <p class="text-sm font-light text-gray-500 text-center">
                Don't have an account yet? <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:underline hover:text-primary-700 transition-colors">Sign up</a>
            </p>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center flex flex-col items-center gap-4">
             <button onclick="fillDemo()" class="text-xs text-slate-500 bg-slate-50 hover:bg-slate-100 border border-gray-200 px-4 py-2.5 rounded-xl transition-all duration-200 cursor-pointer inline-flex items-center gap-2 hover:shadow-md active:scale-95">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Tap to fill Demo Account
            </button>
            <a href="{{ route('super_admin.login') }}" class="text-[10px] uppercase tracking-widest text-slate-300 hover:text-slate-500 transition-colors">Platform Admin</a>
        </div>
    </div>

    <script>
        function fillDemo() {
            document.getElementById('email').value = 'joe@barbershoppe.com';
            document.getElementById('password').value = 'password';
        }
    </script>
    
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</body>
</html>
