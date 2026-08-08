@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">User Account & Angler Profile Management</h1>
                <p class="text-xs text-slate-400">Link registered web accounts to Angler profiles and assign administrator privileges</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                ← Admin Console
            </a>
        </div>
    </div>

    <!-- Status / Error Alerts -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold p-4 rounded-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-teal-600"></i> Registered User Accounts
            </h2>
            <span class="text-xs text-slate-500 font-mono">{{ count($users) }} Account(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4">User Account</th>
                        <th class="py-3 px-4">Email / Verification</th>
                        <th class="py-3 px-4">Role Privileges</th>
                        <th class="py-3 px-4">Associated Angler Profile</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-4 font-bold text-slate-900">
                                {{ $user->name }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-mono text-slate-800">{{ $user->email }}</div>
                                <div class="mt-1 flex items-center gap-2">
                                    @if($user->isRegistered())
                                        <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Verified</span>
                                    @else
                                        <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Pending Verification</span>
                                        <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline" onsubmit="return confirm('Manually verify email address for {{ $user->name }}?')">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-bold text-teal-800 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 px-2 py-0.5 rounded border border-teal-300 shadow-sm transition-colors cursor-pointer" title="Manually mark user email as verified">
                                                ✓ Verify Now
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @if($user->isAdmin())
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200">
                                        <i data-lucide="shield" class="w-3.5 h-3.5 text-teal-600"></i> Administrator
                                    </span>
                                @else
                                    <span class="text-[11px] font-medium text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                        Standard User
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($user->angler)
                                    <div class="flex items-center gap-2">
                                        <x-anglerAvatar :angler="$user->angler" size="w-7 h-7 text-xs" />
                                        <div>
                                            <a href="/angler/{{ $user->angler->id }}" class="font-bold text-slate-900 hover:text-teal-600 hover:underline">
                                                {{ $user->angler->fullName }}
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">No Angler Profile Linked</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Link Angler Form Modal Trigger / Dropdown -->
                                    <form action="{{ route('admin.users.link') }}" method="POST" class="flex items-center gap-1">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <select name="angler_id" class="h-8 px-2 rounded-lg border border-slate-200 text-xs bg-slate-50 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                                            <option value="">Unlink Angler...</option>
                                            @foreach($anglers as $ang)
                                                <option value="{{ $ang->id }}" {{ $user->angler && $user->angler->id == $ang->id ? 'selected' : '' }}>
                                                    {{ $ang->fullName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="h-8 px-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-lg shadow transition-colors">
                                            Assign
                                        </button>
                                    </form>

                                    <!-- Toggle Admin Privileges -->
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" onsubmit="return confirm('Change admin privileges for {{ $user->name }}?')">
                                            @csrf
                                            <button type="submit" class="h-8 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg border border-slate-200 transition-colors">
                                                {{ $user->isAdmin() ? 'Demote' : 'Make Admin' }}
                                            </button>
                                        </form>

                                        <!-- Delete User Account -->
                                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to PERMANENTLY remove user account {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 px-2.5 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 font-bold text-xs rounded-lg transition-colors cursor-pointer" title="Delete User">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
