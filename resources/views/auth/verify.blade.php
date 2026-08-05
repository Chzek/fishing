@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6 py-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6 text-center">
        <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center mx-auto">
            <i data-lucide="mail-check" class="w-6 h-6"></i>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Verify Email') }}</h1>

        @if (session('resent'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold p-3.5 rounded-xl" role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif

        <p class="text-xs text-slate-600 leading-relaxed">
            {{ __('Before proceeding, please check your email for a verification link.') }}
            {{ __('If you did not receive the email') }},
        </p>

        <a href="{{ route('verification.resend') }}" class="inline-flex items-center gap-2 py-2.5 px-4 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors">
            <i data-lucide="send" class="w-3.5 h-3.5"></i>
            <span>{{ __('Request Another Link') }}</span>
        </a>
    </div>
</div>
@endsection
