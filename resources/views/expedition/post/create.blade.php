@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="message-square-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">New Trip Post</h1>
                    <p class="text-xs text-slate-500">{{ $expedition->description }}</p>
                </div>
            </div>
            <a href="/expedition/{{ $expedition->id }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Return</a>
        </div>

        <form action="{{ url('/post') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="expeditions_id" value="{{ $expedition->id }}">

            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Post Update / Note</label>
                <textarea id="description" name="description" placeholder="What happened on the trip today?" required class="w-full p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 h-32">{{ old('description') }}</textarea>
            </div>

            <div class="space-y-1.5">
                <label for="date" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Post Date ({{ $expedition->start }} to {{ $expedition->finish }})</label>
                <input type="date" id="date" name="date" value="{{ old('date', $expedition->start) }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>
            
            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Post Update</button>
                <a href="/expedition/{{ $expedition->id }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Return</a>
            </div>

        </form>
    </div>
</div>
@endsection
