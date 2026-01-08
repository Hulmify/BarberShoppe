<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login - BarberShoppe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-800 flex items-center justify-center min-h-screen p-4">

    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-amber-500/10 blur-3xl"></div>
        <div class="absolute bottom-[20%] -right-[10%] w-[30%] h-[30%] rounded-full bg-purple-500/10 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-8 relative z-10">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-white mb-2">Super<span class="text-amber-500">Admin</span></h1>
            <p class="text-slate-400 text-sm">Platform Management Console</p>
        </div>

        @if ($errors->any())
            <div class="p-4 mb-6 text-sm text-red-400 rounded-lg bg-red-900/20 border border-red-800/50" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('super_admin.authenticate') }}" class="space-y-6">
            @csrf
            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-slate-300">Username</label>
                <input type="text" name="username" id="username" required class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 transition-all" placeholder="Username">
            </div>
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-slate-300">Master Password</label>
                <input type="password" name="password" id="password" required class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 transition-all" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full text-slate-900 bg-amber-500 hover:bg-amber-400 focus:ring-4 focus:outline-none focus:ring-amber-800 font-bold rounded-lg text-sm px-5 py-3 text-center transition-transform hover:-translate-y-0.5">Authorize Access</button>
        </form>
    </div>

</body>
</html>
