<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - BarberShoppe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-800 text-slate-200 min-h-screen">

    <nav class="bg-slate-950 border-b border-slate-700 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Super<span class="text-amber-500">Admin</span></h1>
            <form action="{{ route('super_admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 md:p-10">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">User Accounts</h2>
            <p class="text-slate-400 mt-1">Manage subscription expiry dates for all BarberShoppe owners.</p>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-slate-400 rounded-lg bg-green-900/20 border border-green-800/50" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-slate-950 border border-slate-600 rounded-2xl overflow-hidden shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800/50 text-slate-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">User</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">Subscription Ends At</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">Created {{ $user->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm {{ $user->trial_ends_at && $user->trial_ends_at->isPast() ? 'text-red-400' : 'text-slate-300' }}">
                                    {{ $user->trial_ends_at ? $user->trial_ends_at->format('M d, Y H:i') : 'Never' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->trial_ends_at && $user->trial_ends_at->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/30 text-red-500 border border-red-800/30">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/30 text-green-500 border border-green-800/30">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('super_admin.update_expiry', $user) }}" method="POST" class="inline-flex items-center gap-2">
                                    @csrf
                                    <input type="datetime-local" name="trial_ends_at" required 
                                           value="{{ $user->trial_ends_at ? $user->trial_ends_at->format('Y-m-d\TH:i') : '' }}"
                                           class="bg-slate-800 border border-slate-700 text-white text-xs rounded-lg p-1.5 focus:ring-amber-500 focus:border-amber-500 p-4">
                                    <button type="submit" class="text-xs bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold py-1.5 px-3 rounded transition-colors p-4">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
