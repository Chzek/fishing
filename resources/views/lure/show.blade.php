@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="fishing-hook" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $lure->name }}</h1>
                    <p class="text-xs text-slate-500">Tackle Details</p>
                </div>
            </div>
            <a href="/lure" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Return</a>
        </div>

        <div class="space-y-3">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Color Pattern</span>
                <span class="text-base font-bold text-slate-800 block">{{ $lure->color }}</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Size / Weight</span>
                <span class="text-base font-bold text-slate-800 font-mono block">{{ $lure->size }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <a href='/lure/{{ $lure->id }}/edit' class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                <span>Edit Lure</span>
            </a>
            <a href='/lure' class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">Back to Tackle Box</a>
        </div>
    </div>
</div>
@endsection
