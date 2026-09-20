@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <x-statusAlert />

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl p-4 space-y-1 shadow-sm">
            <div class="font-bold flex items-center gap-2">
                <x-lucide-alert-circle class="w-5 h-5 text-rose-500" />
                <span>Please fix the following issues:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 pt-1 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <x-lucide-user-cog class="w-5 h-5" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">User Account Preferences</h1>
                    <p class="text-xs text-slate-500">Manage account credentials, email, and password settings.</p>
                </div>
            </div>
            <a href="/profile" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl border border-slate-200 transition-colors">Back to Dashboard</a>
        </div>

        @if($user->angler)
            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <x-anglerAvatar :angler="$user->angler" size="md" />
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Associated Angler Profile</span>
                        <span class="text-sm font-bold text-slate-900 block leading-tight">{{ $user->angler->firstName }} {{ $user->angler->lastName }}</span>
                    </div>
                </div>
                <a href="/angler/{{ $user->angler->id }}/edit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-teal-50 text-slate-700 hover:text-teal-700 font-semibold text-xs rounded-xl border border-slate-200 hover:border-teal-200 transition-colors">
                    <x-lucide-edit-3 class="w-3.5 h-3.5 text-teal-600" />
                    <span>Edit Angler Name / Bio</span>
                </a>
            </div>
        @endif

        <form action="{{ url('/profile') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: User Account Details -->
            <div class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-teal-700 flex items-center gap-1.5">
                    <x-lucide-user class="w-4 h-4 text-teal-600" /> Account Identity
                </h2>

                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">User Account Display Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <hr class="border-slate-100 dark:border-slate-800">

            <!-- Section 2: Interface & Theme Preference -->
            <div class="space-y-4">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 flex items-center gap-1.5">
                        <x-lucide-palette class="w-4 h-4 text-teal-600 dark:text-teal-400" /> Interface & Theme Preference
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Select your default theme mode across devices.</p>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preference" value="system" {{ old('theme_preference', $user->theme_preference ?? 'system') === 'system' ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 peer-checked:border-teal-500 peer-checked:bg-teal-50/50 dark:peer-checked:bg-teal-950/40 peer-checked:ring-2 peer-checked:ring-teal-500/20 text-center transition-all">
                            <x-lucide-monitor class="w-5 h-5 mx-auto mb-1.5 text-slate-500 peer-checked:text-teal-600 dark:peer-checked:text-teal-400" />
                            <span class="text-xs font-bold text-slate-800 dark:text-white block">System Auto</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Match OS Setting</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preference" value="light" {{ old('theme_preference', $user->theme_preference ?? 'system') === 'light' ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 peer-checked:border-amber-500 peer-checked:bg-amber-50/50 dark:peer-checked:bg-amber-950/40 peer-checked:ring-2 peer-checked:ring-amber-500/20 text-center transition-all">
                            <x-lucide-sun class="w-5 h-5 mx-auto mb-1.5 text-slate-500 peer-checked:text-amber-600 dark:peer-checked:text-amber-400" />
                            <span class="text-xs font-bold text-slate-800 dark:text-white block">Light Mode</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Outdoor Sunlight</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preference" value="dark" {{ old('theme_preference', $user->theme_preference ?? 'system') === 'dark' ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 dark:peer-checked:bg-indigo-950/40 peer-checked:ring-2 peer-checked:ring-indigo-500/20 text-center transition-all">
                            <x-lucide-moon class="w-5 h-5 mx-auto mb-1.5 text-slate-500 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400" />
                            <span class="text-xs font-bold text-slate-800 dark:text-white block">Dark Mode</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Night Cockpit</span>
                        </div>
                    </label>
                </div>
            </div>

            <hr class="border-slate-100 dark:border-slate-800">

            <!-- Section 3: Password Security -->
            <div class="space-y-4">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 flex items-center gap-1.5">
                        <x-lucide-key-round class="w-4 h-4 text-teal-600 dark:text-teal-400" /> Change Password
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Leave blank if you do not wish to change your password.</p>
                </div>

                <div class="space-y-1.5">
                    <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="••••••••" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Minimum 8 characters" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-sm rounded-xl shadow transition-all cursor-pointer">Save Account Preferences</button>
                <a href="/profile" class="px-5 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm rounded-xl border border-slate-200 dark:border-slate-700 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
