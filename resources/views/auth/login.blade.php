@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6 py-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="log-in" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Welcome Back') }}</h1>
            <p class="text-xs text-slate-500">Sign in to access your Fishing Logbook</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address Input -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('E-Mail Address') }}</label>
                <div class="relative">
                    <input id="email" type="email" class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors {{ $errors->has('email') ? 'border-rose-500' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
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

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300" {{ old('remember') ? 'checked' : '' }}>
                    <span>{{ __('Remember Me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-teal-600 hover:text-teal-700 font-semibold" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-900/10 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    <span>{{ __('Sign In') }}</span>
                </button>
            </div>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Don't have an account? <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:underline">Register now</a>
        </div>
    </div>
</div>
@endsection
