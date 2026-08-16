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
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if (session('invite_url'))
        <div class="bg-gradient-to-r from-teal-900 to-slate-900 border border-teal-500/40 text-white rounded-2xl p-5 shadow-lg space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/10 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center shrink-0">
                        <i data-lucide="link" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white tracking-tight">Invitation Link Ready for {{ session('invite_name') }}</h3>
                        <p class="text-xs text-teal-300/80">{{ session('invite_email') }} • Valid for 7 days</p>
                    </div>
                </div>
                <span class="text-[11px] font-semibold bg-teal-500/20 text-teal-300 px-2.5 py-1 rounded-lg border border-teal-500/30 self-start sm:self-auto">
                    Relative Signature Enabled
                </span>
            </div>

            <!-- NAS URL if available -->
            @if (session('invite_url_nas'))
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-teal-200 flex items-center gap-1.5">
                            <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-400"></i> NAS / Production URL (Recommended for sending):
                        </span>
                        <span class="text-[10px] text-slate-400">Accessible anywhere via DDNS</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="invite-url-nas" readonly value="{{ session('invite_url_nas') }}" class="w-full h-10 px-3.5 rounded-xl bg-slate-800/90 border border-slate-700 text-xs font-mono text-teal-300 focus:outline-none select-all">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('invite-url-nas').value); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy NAS Link', 2000)" class="h-10 px-4 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs rounded-xl shadow transition-colors shrink-0 cursor-pointer flex items-center gap-1.5">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span>Copy NAS Link</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Localhost / Field URL if different -->
            @if (session('invite_url_local') && session('invite_url_local') !== session('invite_url_nas'))
                <div class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="laptop" class="w-3.5 h-3.5 text-slate-400"></i> Local Machine URL:
                        </span>
                        <span class="text-[10px] text-slate-400">For field laptop direct testing</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="invite-url-local" readonly value="{{ session('invite_url_local') }}" class="w-full h-9 px-3 rounded-xl bg-slate-800/60 border border-slate-700/60 text-xs font-mono text-slate-300 focus:outline-none select-all">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('invite-url-local').value); this.innerText='Copied!'; setTimeout(() => this.innerText='Copy Local Link', 2000)" class="h-9 px-3.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors shrink-0 cursor-pointer flex items-center gap-1.5">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span>Copy Local Link</span>
                        </button>
                    </div>
                </div>
            @endif

            <p class="text-[11px] text-slate-400 italic">
                Tip: If authenticating on the NAS, ensure the NAS and laptop share the same <code>APP_KEY</code> in their <code>.env</code>.
            </p>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold p-4 rounded-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Unlinked Accounts / Notifications Alert Banner -->
    @if(!empty($unreadNotifications) && $unreadNotifications->count() > 0)
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 text-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-white">{{ $unreadNotifications->count() }} New User Registration Notification(s)</h3>
                    <p class="text-[11px] text-amber-300/80">Pair newly registered accounts with their corresponding Angler profile in the table below.</p>
                </div>
            </div>
            <form action="{{ route('admin.notifications.mark_read') }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                    Dismiss Notifications
                </button>
            </form>
        </div>
    @endif

    <!-- Invitation & Offline Quick-Add Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Email Invitation Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                <i data-lucide="mail-plus" class="w-4 h-4 text-teal-600"></i>
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Email Signed Invitation (Home Mode)</h3>
            </div>
            <p class="text-xs text-slate-500">Generates a secure signed registration URL valid for 7 days to email an angler.</p>
            <form action="{{ route('admin.users.invite') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <input type="text" name="name" required placeholder="Angler Full Name" class="h-9 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <input type="email" name="email" required placeholder="Angler Email Address" class="h-9 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                </div>
                <button type="submit" class="w-full py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    <span>Generate Signed Invite</span>
                </button>
            </form>
        </div>

        <!-- Canada Field Offline Quick-Add Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                <i data-lucide="user-plus" class="w-4 h-4 text-amber-600"></i>
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Canada Offline Quick-Add (Field Mode)</h3>
            </div>
            <p class="text-xs text-slate-500">Create an Angler account locally without internet. Syncs upstream to NAS when returning home.</p>
            <form action="{{ route('admin.users.quick-add') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <input type="text" name="name" required placeholder="Angler Name" class="h-9 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <input type="email" name="email" required placeholder="Email" class="h-9 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                    <input type="password" name="password" required placeholder="Temp Password" class="h-9 px-3 rounded-xl border border-slate-200 text-xs bg-slate-50">
                </div>
                <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    <span>Create Offline Angler Account</span>
                </button>
            </form>
        </div>
    </div>

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
                        <tr class="hover:bg-slate-50/80 transition-colors {{ !$user->angler ? 'bg-amber-50/40' : '' }}">
                            <td class="py-4 px-4 font-bold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <span>{{ $user->name }}</span>
                                    @if(!$user->angler)
                                        <span class="text-[9px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300 px-1.5 py-0.2 rounded-md">
                                            Unlinked
                                        </span>
                                    @endif
                                </div>
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
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                                        <i data-lucide="alert-circle" class="w-3 h-3 text-amber-600"></i> Needs Angler Linking
                                    </span>
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
