@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>User Account & Angler Profile Management</span>
                    <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">Admin Portal</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium pt-0.5">Manage user accounts, send cryptographic invite links, and pair registered users to logbook Angler profiles</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.trash') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-400"></i>
                <span>Trash Archive</span>
            </a>
            <a href="{{ url('/admin/sync') }}" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>NAS / Laptop Sync</span>
            </a>
        </div>
    </div>

    <!-- Generated Invitation Token Display Box -->
    @if (session('invite_url_nas') || session('invite_url_local'))
        <div class="bg-slate-900 border border-teal-500/40 rounded-2xl p-5 text-white space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2 text-teal-400 font-bold text-sm">
                    <i data-lucide="check-circle" class="w-4 h-4 text-teal-400"></i>
                    <span>Signed Invite Link Generated Successfully</span>
                </div>
                <span class="text-[11px] font-mono bg-teal-500/10 text-teal-300 border border-teal-500/30 px-2 py-0.5 rounded-full">
                    Expires in 7 days
                </span>
            </div>

            <!-- Primary NAS Network URL -->
            @if (session('invite_url_nas'))
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="server" class="w-3.5 h-3.5 text-teal-400"></i> Network / NAS Server URL:
                        </span>
                        <span class="text-[10px] text-teal-400/90 font-medium">Recommended for anglers on WiFi / Home Network</span>
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
        <div class="bg-amber-50/80 rounded-2xl p-4 sm:p-5 border border-amber-200/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>{{ $unreadNotifications->count() }} New User Registration Notification(s)</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-200/70 text-amber-900 border border-amber-300 font-mono">New</span>
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">Pair newly registered accounts with their corresponding Angler profile in the table below.</p>
                </div>
            </div>
            <form action="{{ route('admin.notifications.mark_read') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200/80 shadow-sm transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="check" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>Dismiss Notifications</span>
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

    <!-- Users Table with Local Filter & Multi-Sort -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-teal-600"></i> Registered User Accounts
            </h2>
            <span class="text-xs text-slate-500 font-mono">{{ count($users) }} Account(s)</span>
        </div>

        <div x-data="dataTable({ defaultDensity: 'normal' })">
            <x-table.wrapper 
                searchPlaceholder="Quick filter users by name, email, or role..." 
                itemName="users"
                :showColumnPicker="false"
                :showDensity="true"
            >
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <x-table.th col="account" type="text" label="User Account">User Account</x-table.th>
                            <x-table.th col="email" type="text" label="Email">Email / Verification</x-table.th>
                            <x-table.th col="role" type="text" label="Role">Role Privileges</x-table.th>
                            <x-table.th col="angler" type="text" label="Angler Profile">Associated Angler Profile</x-table.th>
                            <th scope="col" class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @foreach($users as $user)
                            <tr data-table-row class="hover:bg-slate-50/80 transition-colors {{ !$user->angler ? 'bg-amber-50/40' : '' }}">
                                <td data-col="account" data-sort-val="{{ $user->name }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-4 px-4'" class="font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $user->name }}</span>
                                        @if(!$user->angler)
                                            <span class="text-[9px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300 px-1.5 py-0.2 rounded-md">
                                                Unlinked
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td data-col="email" data-sort-val="{{ $user->email }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-4 px-4'">
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
                                <td data-col="role" data-sort-val="{{ $user->isAdmin() ? 'Admin' : 'Standard' }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-4 px-4'">
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
                                <td data-col="angler" data-sort-val="{{ $user->angler ? $user->angler->fullName : 'Unlinked' }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-4 px-4'">
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
                                <td :class="density === 'compact' ? 'py-2 px-4' : 'py-4 px-4'" class="text-right">
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
            </x-table.wrapper>
        </div>
    </div>
</div>
@endsection
