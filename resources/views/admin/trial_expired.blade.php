@extends('layouts.admin')

@section('title', 'Subscription Expired')

@section('header', 'Account subscription expired')
@section('subheader', 'Your subscription period has come to an end.')

@section('content')
<div class="max-w-2xl mx-auto mt-12 text-center">
    <div class="bg-white border border-red-100 rounded-3xl shadow-xl p-10 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-amber-50 rounded-full blur-3xl opacity-50"></div>

        <div class="relative z-10 p-4">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 mb-8 border border-red-100">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h2 class="text-3xl font-bold text-slate-900 mb-4">
                Subscription Required
            </h2>
            <p class="text-slate-600 mb-8 leading-relaxed">
                We hope you enjoyed using BarberShoppe! Your subscription expired on <span class="font-bold text-slate-900">{{ auth()->user()->trial_ends_at->format('M d, Y') }}</span>. 
                To continue managing your shop, please contact Zoeb Chhatriwala at <a href="mailto:zoeb@chhatriwala.com">zoeb@chhatriwala.com</a>.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="mailto:zoeb@chhatriwala.com" class="inline-flex items-center justify-center px-8 py-3 text-white bg-slate-900 hover:bg-slate-800 font-bold rounded-xl transition-all shadow-lg hover:shadow-slate-200">
                    Contact Zoeb
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 text-slate-700 bg-slate-100 hover:bg-slate-200 font-bold rounded-xl transition-all">
                        Sign Out
                    </button>
                </form>
            </div>
            
            <p class="mt-8 text-xs text-slate-400">
                Your data is safe and will be restored immediately once your account is active.
            </p>
        </div>
    </div>
</div>
@endsection
