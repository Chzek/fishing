@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <x-lucide-user-plus class="w-5 h-5" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Add Crew Member</h1>
                    <p class="text-xs text-slate-500">{{ $expedition->description }}</p>
                </div>
            </div>
            <a href="/expedition/{{ $expedition->id }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Return</a>
        </div>

        <form action="{{ url('/crew') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="expeditions_id" value="{{ $expedition->id }}">

            <div class="space-y-1.5">
                <label for="anglers_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Select Angler</label>
                <select id="anglers_id" name="anglers_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    @foreach($anglers as $val => $label)
                        <option value="{{ $val }}" {{ old('anglers_id') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="joined" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Date Joined ({{ $expedition->start }} to {{ $expedition->finish }})</label>
                <input type="date" id="joined" name="joined" value="{{ old('joined', $expedition->start) }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Add Crew Member</button>
                <a href="/expedition/{{ $expedition->id }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Return</a>
            </div>
        </form>

        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-4 space-y-1">
                <strong class="font-bold">Please correct the errors below:</strong>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
