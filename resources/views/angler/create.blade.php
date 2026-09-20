@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-6 transition-colors">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                    <x-lucide-user-plus class="w-5 h-5" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Create Angler Profile</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Register a new crew angler</p>
                </div>
            </div>
            <a href="/angler" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">Return</a>
        </div>

        <form action="{{ url('/angler') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label for="firstName" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="{{ old('firstName') }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="middleName" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Middle Name <span class="text-slate-400 dark:text-slate-500 font-normal lowercase">(optional)</span></label>
                    <input type="text" id="middleName" name="middleName" value="{{ old('middleName') }}" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500" placeholder="Optional...">
                </div>

                <div class="space-y-1.5">
                    <label for="lastName" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="space-y-1.5">
                        <label for="user_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">User Account Link (Admin Only)</label>
                        <select id="user_id" name="user_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                            <option value="">No linked user account...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ (string)old('user_id') === (string)$u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                @endif

                <div class="space-y-1.5">
                    <label for="birthdate" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Birthday</label>
                    <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Profile Photo Avatar</label>
                <div class="flex flex-col sm:flex-row items-center gap-4 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                    <div class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 text-slate-400 dark:text-slate-500 flex items-center justify-center shrink-0">
                        <x-lucide-user class="w-8 h-8" />
                    </div>
                    <div class="flex-1 w-full">
                        <x-photo-upload-input 
                            name="avatar" 
                            id="angler-avatar-uploader" 
                            :multiple="false"
                            label="Upload or Take Avatar" 
                            hint="Take a selfie or choose from album. Auto-compressed." 
                        />
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Create Angler</button>
                <a href="/angler" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm rounded-xl border border-slate-200 dark:border-slate-700 transition-colors">Cancel</a>
            </div>

        </form>

        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs rounded-xl p-4 space-y-1">
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
