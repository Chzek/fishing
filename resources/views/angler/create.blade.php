@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Create Angler Profile</h1>
                    <p class="text-xs text-slate-500">Register a new crew angler</p>
                </div>
            </div>
            <a href="/angler" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Return</a>
        </div>

        <form action="{{ url('/angler') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label for="firstName" class="block text-xs font-bold uppercase tracking-wider text-slate-700">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="{{ old('firstName') }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="middleName" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Middle Name</label>
                    <input type="text" id="middleName" name="middleName" value="{{ old('middleName') }}" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="lastName" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="user_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">User Account</label>
                    <select id="user_id" name="user_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        <option value="">Select account link...</option>
                        @foreach($users as $val => $label)
                            <option value="{{ $val }}" {{ old('user_id') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="birthdate" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Birthday</label>
                    <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="space-y-1.5 pt-1">
                <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Profile Photo Avatar</label>
                <input type="file" id="avatar" name="avatar" class="w-full p-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 text-slate-700">
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Create Angler</button>
                <a href="/angler" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
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
