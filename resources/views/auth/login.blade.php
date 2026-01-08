<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BarberShoppe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <!-- Optional Background Decoration -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-amber-200/20 blur-3xl"></div>
        <div class="absolute bottom-[20%] -right-[10%] w-[30%] h-[30%] rounded-full bg-slate-200/30 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-white border border-gray-100 rounded-2xl shadow-xl p-8 relative z-10 animate-fade-in-up">
        <div class="text-center mb-8">
            <a href="/" class="inline-block text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 mb-2">
                Barber<span class="text-amber-500">Shoppe.</span>
            </a>
            <h2 class="text-xl font-bold text-slate-900 mt-4">Welcome Back</h2>
            <p class="text-slate-500 mt-1 text-sm">Sign in to manage your appointments</p>
        </div>

        @if ($errors->any())
            <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
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
                <input type="email" name="email" id="email" required value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 transition-colors" placeholder="name@company.com">
            </div>
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-slate-900">Password</label>
                <input type="password" name="password" id="password" required class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 transition-colors" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-bold rounded-lg text-sm px-5 py-3 text-center transition-transform hover:-translate-y-0.5">Sign In</button>
            
            <p class="text-sm font-light text-gray-500 text-center">
                Don't have an account yet? <a href="{{ route('register') }}" class="font-medium text-amber-600 hover:underline">Sign up</a>
            </p>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
             <button onclick="fillDemo()" class="text-xs text-slate-500 bg-slate-50 hover:bg-slate-100 border border-gray-200 px-3 py-2 rounded-full transition-colors cursor-pointer inline-flex items-center gap-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Tap to fill Demo Account
            </button>
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
