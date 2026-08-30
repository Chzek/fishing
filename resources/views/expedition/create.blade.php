@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <x-lucide-plus-circle class="w-5 h-5" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Create Expedition Trip</h1>
                    <p class="text-xs text-slate-500">Plan a wilderness trip</p>
                </div>
            </div>
            <a href="/expedition" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        <form action="{{ url('/expedition') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Trip Description</label>
                <input type="text" id="description" name="description" value="{{ old('description') }}" placeholder="e.g. Wawa Lake Fly-in 2026" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="start" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Start Date</label>
                    <input type="date" id="start" name="start" value="{{ old('start', date('Y-m-d')) }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="finish" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Finish Date</label>
                    <input type="date" id="finish" name="finish" value="{{ old('finish', date('Y-m-d')) }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Create Expedition</button>
            </div>

        </form>
    </div>
</div>
@endsection
