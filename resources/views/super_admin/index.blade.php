<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin Dashboard - BarberShoppe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(51, 65, 85, 0.3);
        }
        .form-input {
            background: rgba(2, 6, 23, 0.8);
            border: 1px solid rgba(51, 65, 85, 0.6);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
            outline: none;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen selection:bg-primary-500/30">

    <nav class="bg-[#020617]/80 backdrop-blur-xl border-b border-slate-800/60 sticky top-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white tracking-tight flex items-center">
                Super<span class="text-primary-500">Admin</span>
            </h1>
            <div class="flex items-center gap-8">
                <nav class="hidden md:flex items-center gap-6">
                    <a href="#" class="text-sm font-medium text-white border-b-2 border-primary-500 pb-1">Accounts</a>
                </nav>
                <form action="{{ route('super_admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="group flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-white transition-all">
                        <span class="bg-slate-800 group-hover:bg-slate-700 p-2 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 md:p-10 space-y-10">
        <!-- Header & Stats -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="space-y-2">
                <h2 class="text-4xl font-black text-white tracking-tight">System Console</h2>
                <p class="text-slate-400 max-w-xl text-lg leading-relaxed">Intelligence dashboard for global BarberShoppe management and account orchestration.</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 w-full lg:w-auto">
                <div class="glass-card p-5 rounded-2xl flex flex-col gap-1">
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-[0.2em]">Total Entities</span>
                    <p class="text-3xl font-black text-white italic">{{ $users->count() }}</p>
                </div>
                <div class="glass-card p-5 rounded-2xl border-green-500/20 flex flex-col gap-1">
                    <span class="text-[10px] uppercase font-bold text-green-500/70 tracking-[0.2em]">Live Status</span>
                    <p class="text-3xl font-black text-white italic">{{ $users->where('trial_ends_at', '>', now())->count() }}</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-4 animate-in fade-in slide-in-from-top-4" role="alert">
                <div class="bg-emerald-500/20 p-2 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="font-medium tracking-tight">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-6 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 space-y-3" role="alert">
                <div class="flex items-center gap-3 font-bold uppercase tracking-[0.15em] text-xs">
                    <div class="bg-rose-500/20 p-1.5 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    Constraint Violations
                </div>
                <ul class="list-disc list-inside text-sm font-medium opacity-90 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Content Table -->
        <div class="glass-card rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-slate-800/50">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="bg-slate-900/80 text-slate-500 text-[10px] uppercase font-bold tracking-[0.25em] border-b border-slate-800">
                            <th class="px-10 py-6">Identity</th>
                            <th class="px-10 py-6">Email</th>
                            <th class="px-10 py-6">Status</th>
                            <th class="px-10 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @foreach($users as $user)
                            @php
                                $isExpired = $user->trial_ends_at && $user->trial_ends_at->isPast();
                            @endphp
                            <tr class="group hover:bg-slate-800/20 transition-all duration-300">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-5">
                                        <div class="relative">
                                            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center text-primary-500 font-black border border-slate-700/50 shadow-inner group-hover:scale-110 transition-transform duration-500">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-slate-950 {{ $isExpired ? 'bg-rose-500' : 'bg-emerald-500' }}"></div>
                                        </div>
                                        <div class="space-y-0.5">
                                            <div class="font-black text-white text-lg tracking-tight group-hover:text-primary-500 transition-colors">{{ $user->name }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider flex items-center gap-2">
                                                <span>#{{ $user->id }}</span>
                                                <span class="h-1 w-1 bg-slate-700 rounded-full"></span>
                                                <span>v.{{ $user->created_at->format('Y.m.d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-semibold text-slate-300">{{ $user->email }}</span>
                                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-black italic">Primary Channel</span>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="flex flex-col gap-2">
                                        @if($isExpired)
                                            <div class="flex items-center gap-2 text-rose-400">
                                                <div class="h-1.5 w-1.5 rounded-full bg-rose-500 animate-pulse"></div>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Access Expired</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-emerald-400">
                                                <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Active License</span>
                                            </div>
                                        @endif
                                        <div class="bg-slate-900/50 border border-slate-800/50 rounded-lg px-3 py-1.5 w-fit">
                                            <span class="text-[11px] font-mono {{ $isExpired ? 'text-rose-400/80' : 'text-slate-400' }}">
                                                {{ $user->trial_ends_at ? $user->trial_ends_at->format('d M y • H:i') : 'OFF-GRID' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="flex flex-col gap-4 items-end">
                                        <form action="{{ route('super_admin.update_expiry', $user) }}" method="POST" class="flex items-end gap-3 bg-[#020617]/40 p-2.5 rounded-2xl border border-slate-800/50 hover:border-primary-500/30 transition-colors">
                                            @csrf
                                            <div class="flex flex-col gap-1">
                                                <label class="text-[9px] text-slate-500 uppercase font-black tracking-widest pl-1">Lifecycle Matrix</label>
                                                <input type="datetime-local" name="trial_ends_at" required 
                                                       value="{{ $user->trial_ends_at ? $user->trial_ends_at->format('Y-m-d\TH:i') : '' }}"
                                                       class="form-input text-white text-xs rounded-xl px-3 py-2 w-48 font-mono">
                                            </div>
                                            <button type="submit" class="bg-primary-500 hover:bg-primary-400 text-slate-950 p-2.5 rounded-xl transition-all shadow-lg shadow-primary-500/10 group/btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover/btn:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>

                                        <form action="{{ route('super_admin.change_password', $user) }}" method="POST" class="flex items-end gap-3 bg-[#020617]/40 p-2.5 rounded-2xl border border-slate-800/50 hover:border-white/10 transition-colors">
                                            @csrf
                                            <div class="flex flex-col gap-1">
                                                <label class="text-[9px] text-slate-500 uppercase font-black tracking-widest pl-1">Password</label>
                                                <input type="password" name="password" required placeholder="New Password"
                                                       class="form-input text-white text-xs rounded-xl px-3 py-2 w-48 font-mono">
                                            </div>
                                            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white p-2.5 rounded-xl border border-slate-700 transition-all uppercase tracking-widest group/btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover/btn:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        
                        @if($users->isEmpty())
                            <tr>
                                <td colspan="4" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="bg-slate-900 p-4 rounded-3xl border border-slate-800 text-slate-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">No user clusters found in database</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="flex flex-col md:flex-row justify-between items-center text-[10px] text-slate-600 font-black uppercase tracking-[0.3em] gap-4">
            <div>&copy; {{ date('Y') }} BarberShoppe Global Systems</div>
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-2 italic"><span class="h-1 w-1 bg-emerald-500 rounded-full"></span> Service Core v4.2.0</span>
                <span>Latency: 24ms</span>
            </div>
        </div>
    </main>

</body>
</html>
