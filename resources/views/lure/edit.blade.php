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
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Lure</h1>
                    <p class="text-xs text-slate-500">Lure #{{ $lure->id }}</p>
                </div>
            </div>
            <a href="/lure/{{ $lure->id }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        <form action="{{ url('/lure') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $lure->id }}">

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lure Model / Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $lure->name) }}" list="nameList" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">

                <datalist id="nameList">
                    @foreach($lureNames as $item)
                        <option value="{{ $item->name }}">
                    @endforeach
                </datalist>
            </div>

            <div class="space-y-1.5">
                <label for="color" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Color Pattern</label>
                <input type="text" id="color" name="color" value="{{ old('color', $lure->color) }}" list="colorList" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">

                <datalist id="colorList">
                    @foreach($lureColors as $item)
                        <option value="{{ $item->color }}">
                    @endforeach
                </datalist>
            </div>

            <div class="space-y-1.5">
                <label for="size" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Size / Weight</label>
                <input type="text" id="size" name="size" value="{{ old('size', $lure->size) }}" list="sizeList" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">

                <datalist id="sizeList">
                    @foreach($lureSizes as $item)
                        <option value="{{ $item->size }}">
                    @endforeach
                </datalist>
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
