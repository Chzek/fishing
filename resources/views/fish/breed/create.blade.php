@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 dark:bg-teal-950/60 border border-teal-500/30 dark:border-teal-800/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                    <x-lucide-fish class="w-5 h-5" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Create Fish Species</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Add new fish breed</p>
                </div>
            </div>
            <a href="/fish" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">Cancel</a>
        </div>

        <form action="{{ url('fish/breed') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Species / Breed Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" list="nameList" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">

                <datalist id="nameList">
                    @foreach($breeds as $item)
                        <option value="{{ $item->name }}">
                    @endforeach
                </datalist>
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="fish_families_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Fish Family</label>
                    <a href="/fish/family/create" class="text-xs text-teal-600 dark:text-teal-400 hover:underline font-bold">+ New Family</a>
                </div>
                <select id="fish_families_id" name="fish_families_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    @foreach($families as $val => $label)
                        <option value="{{ $val }}" {{ old('fish_families_id') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Species Icon / Avatar</label>
                <input type="file" id="avatar" name="avatar" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 dark:file:bg-teal-950/60 file:text-teal-700 dark:file:text-teal-400 hover:file:bg-teal-100 dark:hover:file:bg-teal-900/60 transition-all cursor-pointer">
                <span class="text-[10px] text-slate-400 dark:text-slate-500 block">Square species avatar used in table rows & lists.</span>
            </div>

            <div class="space-y-1.5">
                <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Full Feature Illustration</label>
                <input type="file" id="image" name="image" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-700 transition-all cursor-pointer">
                <span class="text-[10px] text-slate-400 dark:text-slate-500 block">Wide side-profile illustration used on species dossier page.</span>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Create Species</button>
                <a href="/fish" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm rounded-xl border border-slate-200 dark:border-slate-700 transition-colors">Cancel</a>
            </div>

        </form>

        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs rounded-xl p-4 space-y-1">
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
