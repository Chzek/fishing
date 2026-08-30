@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6 py-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center mx-auto mb-3">
                <x-lucide-key-round class="w-6 h-6" />
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Reset Password') }}</h1>
            <p class="text-xs text-slate-500">We will send a password reset link to your email</p>
        </div>

        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold p-3.5 rounded-xl" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">{{ __('E-Mail Address') }}</label>
                <div class="relative">
                    <input id="email" type="email" class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors {{ $errors->has('email') ? 'border-rose-500' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                    <x-lucide-mail class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" />
                </div>
                @if ($errors->has('email'))
                    <p class="text-xs font-semibold text-rose-600 mt-1">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-900/10 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <x-lucide-send class="w-4 h-4" />
                    <span>{{ __('Send Reset Link') }}</span>
                </button>
            </div>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Remember your password? <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:underline">Sign in</a>
        </div>
    </div>
</div>
@endsection
