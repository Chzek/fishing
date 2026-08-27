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

    <!-- Users Table with Livewire Filter & Multi-Sort -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-teal-600"></i> Registered User Accounts
            </h2>
            <span class="text-xs text-slate-500 font-mono">{{ count($users) }} Account(s)</span>
        </div>

        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\User::class,
            'with' => ['angler'],
            'columns' => [
                ['key' => 'name', 'label' => 'User Account', 'type' => 'user_account', 'sortable' => true, 'searchable' => true],
                ['key' => 'email', 'label' => 'Email / Verification', 'type' => 'user_email', 'sortable' => true, 'searchable' => true],
                ['key' => 'type', 'label' => 'Role Privileges', 'type' => 'user_role', 'sortable' => true],
                ['key' => 'angler.lastName', 'label' => 'Associated Angler Profile', 'type' => 'link', 'urlPrefix' => 'angler', 'urlParam' => 'angler.id', 'sortable' => true, 'sortKey' => 'angler'],
            ],
            'searchPlaceholder' => 'Quick filter users by name, email, or role...',
            'itemName' => 'users',
            'perPage' => 15,
            'defaultSortBy' => 'name',
            'defaultSortOrder' => 'asc',
        ])
    </div>
</div>
@endsection
