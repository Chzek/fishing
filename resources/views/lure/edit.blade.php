@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Tackle Specs</h1>
                    <p class="text-xs text-slate-500">Update specifications for {{ $lure->displayName }}</p>
                </div>
            </div>
            <a href="/lure/{{ $lure->id }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        <form action="{{ url('/lure') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $lure->id }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="brand" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Brand / Manufacturer</label>
                    <input type="text" id="brand" name="brand" value="{{ old('brand', $lure->brand) }}" placeholder="e.g. Rapala" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tackle Category</label>
                    <select id="category" name="category" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        @foreach($categoriesList as $cat)
                            <option value="{{ $cat }}" {{ old('category', $lure->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lure Model Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $lure->name) }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label for="color" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Color Pattern</label>
                    <input type="text" id="color" name="color" value="{{ old('color', $lure->color) }}" placeholder="e.g. Firetiger" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="size" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Size / Weight</label>
                    <input type="text" id="size" name="size" value="{{ old('size', $lure->size ?: $lure->weight) }}" placeholder="e.g. 5/16 oz" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="depth_range" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Running Depth</label>
                    <input type="text" id="depth_range" name="depth_range" value="{{ old('depth_range', $lure->depth_range) }}" placeholder="e.g. 4-8 ft" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Save Changes</button>
                <a href="/lure/{{ $lure->id }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
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
