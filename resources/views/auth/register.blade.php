@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6 py-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Create Account') }}</h1>
            <p class="text-xs text-slate-500">Join Fishing Logbook to start tracking catches</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name Input -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('Name') }}</label>
                <div class="relative">
                    <input id="name" type="text" class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors {{ $errors->has('name') ? 'border-rose-500' : '' }}" name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                    <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>
                @if ($errors->has('name'))
                    <p class="text-xs font-semibold text-rose-600 mt-1">{{ $errors->first('name') }}</p>
                @endif
            </div>

            <!-- Email Address Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('E-Mail Address') }}</label>
                <div class="relative">
                    <input id="email" type="email" class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors {{ $errors->has('email') ? 'border-rose-500' : '' }}" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>
                @if ($errors->has('email'))
                    <p class="text-xs font-semibold text-rose-600 mt-1">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('Password') }}</label>
                <div class="relative">
                    <input id="password" type="password" class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors {{ $errors->has('password') ? 'border-rose-500' : '' }}" name="password" required placeholder="••••••••">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>
                @if ($errors->has('password'))
                    <p class="text-xs font-semibold text-rose-600 mt-1">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password Input -->
            <div class="space-y-1.5">
                <label for="password-confirm" class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('Confirm Password') }}</label>
                <div class="relative">
                    <input id="password-confirm" type="password" class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors" name="password_confirmation" required placeholder="••••••••">
                    <i data-lucide="shield-check" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-900/10 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>{{ __('Register Account') }}</span>
                </button>
            </div>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Already registered? <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:underline">Sign in here</a>
        </div>
    </div>
</div>
@endsection
