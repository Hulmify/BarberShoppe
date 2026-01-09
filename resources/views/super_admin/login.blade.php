<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorize | Super Admin - BarberShoppe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass-container {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(51, 65, 85, 0.3);
        }
        .input-premium {
            background: rgba(2, 6, 23, 0.6);
            border: 1px solid rgba(51, 65, 85, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-premium:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
            outline: none;
            background: rgba(2, 6, 23, 0.9);
        }
    </style>
</head>
<body class="bg-[#020617] flex items-center justify-center min-h-screen p-6 overflow-hidden">

    <!-- Decorative Elements -->
    <div class="absolute inset-0 z-0 select-none">
        <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] rounded-full bg-primary-500/10 blur-[120px]"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] rounded-full bg-primary-600/10 blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-[0.03]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="w-full max-w-lg relative z-10 animate-in fade-in zoom-in-95 duration-700">
        <div class="glass-container rounded-[40px] shadow-2xl p-10 md:p-16 border border-white/5">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-black text-white tracking-tight mb-3">Super<span class="text-primary-500">Admin</span></h1>
            </div>

            @if ($errors->any())
                <div class="mb-8 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-3 animate-in shake duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('super_admin.authenticate') }}" class="space-y-8">
                @csrf
                <div class="space-y-2">
                    <label for="username" class="block pl-1 text-[10px] font-black text-slate-500 uppercase tracking-widest">Username</label>
                    <input type="text" name="username" id="username" required 
                           class="input-premium block w-full px-6 py-4 rounded-2xl text-white font-medium placeholder:text-slate-600 focus:ring-0" 
                           placeholder="Username">
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="block pl-1 text-[10px] font-black text-slate-500 uppercase tracking-widest">Password</label>
                    <input type="password" name="password" id="password" required 
                           class="input-premium block w-full px-6 py-4 rounded-2xl text-white font-mono placeholder:text-slate-600 focus:ring-0" 
                           placeholder="••••••••••••">
                </div>

                <button type="submit" 
                        class="relative group w-full bg-white hover:bg-primary-500 text-black font-black py-5 rounded-2xl transition-all duration-300 transform active:scale-[0.98] shadow-xl hover:shadow-primary-500/20 overflow-hidden">
                    <span class="relative z-10 flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                        Authorize Session
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary-400 to-primary-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
