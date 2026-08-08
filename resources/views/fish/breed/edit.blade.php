@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Fish Species</h1>
                    <p class="text-xs text-slate-500">{{ $breed->name }}</p>
                </div>
            </div>
            <a href="/fish" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        <form action="{{ url('fish/breed') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $breed->id }}">

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Species / Breed Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $breed->name) }}" list="nameList" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">

                <datalist id="nameList">
                    @foreach($breeds as $item)
                        <option value="{{ $item->name }}">
                    @endforeach
                </datalist>
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="fish_families_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Fish Family</label>
                    <a href="/fish/family/create" class="text-xs text-teal-600 hover:underline font-bold">+ New Family</a>
                </div>
                <select id="fish_families_id" name="fish_families_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    @foreach($families as $val => $label)
                        <option value="{{ $val }}" {{ old('fish_families_id', $breed->fish_families_id) == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Save Changes</button>
                <a href="/fish" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
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
