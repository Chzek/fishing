@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ mode: 'single' }">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="box" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Add Tackle to Inventory</h1>
                    <p class="text-xs text-slate-500">Register new lures, colors, and running depth specs</p>
                </div>
            </div>
            <a href="/lure" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        <!-- Mode Toggle Tabs -->
        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl text-xs font-bold">
            <button type="button" @click="mode = 'single'" :class="mode === 'single' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="py-2.5 rounded-lg transition-all text-center">
                Single Tackle Item
            </button>
            <button type="button" @click="mode = 'batch'" :class="mode === 'batch' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="py-2.5 rounded-lg transition-all text-center flex items-center justify-center gap-1.5">
                <span>Multi-Color Batch Builder</span>
                <span class="px-1.5 py-0.2 text-[10px] bg-teal-100 text-teal-800 rounded font-mono">Fast</span>
            </button>
        </div>

        <!-- Mode 1: Single Tackle Item Form -->
        <form x-show="mode === 'single'" action="{{ url('/lure') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="brand" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Brand / Manufacturer</label>
                    <input type="text" id="brand" name="brand" value="{{ old('brand') }}" placeholder="e.g. Rapala, Yamamoto, Keitech" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tackle Category</label>
                    <select id="category" name="category" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        @foreach($categoriesList as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lure Model Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Shad Rap, 5&quot; Senko, Aglia Spinner" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label for="color" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Color Pattern</label>
                    <input type="text" id="color" name="color" value="{{ old('color') }}" placeholder="e.g. Firetiger" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="size" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Size / Weight</label>
                    <input type="text" id="size" name="size" value="{{ old('size') }}" placeholder="e.g. 5/16 oz, #3" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="depth_range" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Running Depth</label>
                    <input type="text" id="depth_range" name="depth_range" value="{{ old('depth_range') }}" placeholder="e.g. 4-8 ft, Topwater" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Create Lure</button>
                <a href="/lure" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
            </div>
        </form>

        <!-- Mode 2: Multi-Color Batch Builder Form -->
        <form x-show="mode === 'batch'" action="{{ route('lure.batch') }}" method="POST" class="space-y-4" x-cloak>
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="batch_brand" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Brand / Manufacturer</label>
                    <input type="text" id="batch_brand" name="brand" placeholder="e.g. Rapala" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="batch_category" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tackle Category</label>
                    <select id="batch_category" name="category" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        @foreach($categoriesList as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="batch_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lure Model Name *</label>
                <input type="text" id="batch_name" name="name" placeholder="e.g. Shad Rap" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>

            <div class="space-y-1.5">
                <label for="colors_input" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Color Variants (Comma Separated) *</label>
                <textarea id="colors_input" name="colors_input" rows="2" placeholder="e.g. Firetiger, Silver, Perch, Bluegill, Bleeding Pearl" required class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-mono"></textarea>
                <p class="text-[11px] text-slate-400">Type colors separated by commas. Each color will automatically create a distinct lure entry!</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="batch_size" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Size / Weight</label>
                    <input type="text" id="batch_size" name="size" placeholder="e.g. 5/16 oz" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="batch_depth" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Running Depth</label>
                    <input type="text" id="batch_depth" name="depth_range" placeholder="e.g. 4-8 ft" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Create All Color Variants</button>
                <a href="/lure" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
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
