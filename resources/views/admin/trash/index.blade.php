@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: 'records' }">
    <!-- Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0">
                <i data-lucide="trash-2" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Admin Trash Bin & Recovery Console</h1>
                <p class="text-xs text-slate-400">Restore soft-deleted items or permanently purge deleted data from storage</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                ← Admin Console
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Category Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-bold">
        <button @click="tab = 'records'" :class="tab === 'records' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Catches ({{ count($trashedCatches) }})
        </button>
        <button @click="tab = 'lakes'" :class="tab === 'lakes' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Lakes ({{ count($trashedLakes) }})
        </button>
        <button @click="tab = 'anglers'" :class="tab === 'anglers' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Anglers ({{ count($trashedAnglers) }})
        </button>
        <button @click="tab = 'lures'" :class="tab === 'lures' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Lures ({{ count($trashedLures) }})
        </button>
        <button @click="tab = 'expeditions'" :class="tab === 'expeditions' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Expeditions ({{ count($trashedExpeditions) }})
        </button>
    </div>

    <!-- Catches Tab -->
    <div x-show="tab === 'records'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Catches</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4">Catch Date</th>
                        <th class="py-3 px-4">Angler</th>
                        <th class="py-3 px-4">Lake</th>
                        <th class="py-3 px-4">Length</th>
                        <th class="py-3 px-4">Deleted At</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($trashedCatches as $cat)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-medium">{{ $cat->caught }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $cat->angler->fullName ?? 'Unknown' }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $cat->lake->name ?? 'Unknown' }}</td>
                            <td class="py-3.5 px-4 font-mono">{{ $cat->length }} in.</td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-rose-600">{{ $cat->deleted_at }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.trash.restore') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="record">
                                        <input type="hidden" name="id" value="{{ $cat->id }}">
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow transition-colors">Restore</button>
                                    </form>
                                    <form action="{{ route('admin.trash.force-delete') }}" method="POST" onsubmit="return confirm('Permanently delete this catch record?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="record">
                                        <input type="hidden" name="id" value="{{ $cat->id }}">
                                        <button type="submit" class="px-3 py-1 bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs rounded-lg hover:bg-rose-100 transition-colors">Delete Permanently</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 text-xs">No soft-deleted catches in trash.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lakes Tab -->
    <div x-show="tab === 'lakes'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Lakes & Waterbodies</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4">Lake Name</th>
                        <th class="py-3 px-4">Coordinates</th>
                        <th class="py-3 px-4">Deleted At</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($trashedLakes as $lk)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $lk->name }}</td>
                            <td class="py-3.5 px-4 font-mono text-[11px]">{{ $lk->latitude }}, {{ $lk->longitude }}</td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-rose-600">{{ $lk->deleted_at }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.trash.restore') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="lake">
                                        <input type="hidden" name="id" value="{{ $lk->id }}">
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow transition-colors">Restore</button>
                                    </form>
                                    <form action="{{ route('admin.trash.force-delete') }}" method="POST" onsubmit="return confirm('Permanently delete this lake?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="lake">
                                        <input type="hidden" name="id" value="{{ $lk->id }}">
                                        <button type="submit" class="px-3 py-1 bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs rounded-lg hover:bg-rose-100 transition-colors">Delete Permanently</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 text-xs">No soft-deleted lakes in trash.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Anglers Tab -->
    <div x-show="tab === 'anglers'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Angler Profiles</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4">Angler Name</th>
                        <th class="py-3 px-4">Deleted At</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($trashedAnglers as $ang)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $ang->fullName }}</td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-rose-600">{{ $ang->deleted_at }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.trash.restore') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="angler">
                                        <input type="hidden" name="id" value="{{ $ang->id }}">
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow transition-colors">Restore</button>
                                    </form>
                                    <form action="{{ route('admin.trash.force-delete') }}" method="POST" onsubmit="return confirm('Permanently delete this angler profile?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="angler">
                                        <input type="hidden" name="id" value="{{ $ang->id }}">
                                        <button type="submit" class="px-3 py-1 bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs rounded-lg hover:bg-rose-100 transition-colors">Delete Permanently</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400 text-xs">No soft-deleted anglers in trash.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lures Tab -->
    <div x-show="tab === 'lures'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Lures & Tackle</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4">Lure Name</th>
                        <th class="py-3 px-4">Deleted At</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($trashedLures as $lr)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $lr->displayName }}</td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-rose-600">{{ $lr->deleted_at }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.trash.restore') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="lure">
                                        <input type="hidden" name="id" value="{{ $lr->id }}">
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow transition-colors">Restore</button>
                                    </form>
                                    <form action="{{ route('admin.trash.force-delete') }}" method="POST" onsubmit="return confirm('Permanently delete this lure entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="lure">
                                        <input type="hidden" name="id" value="{{ $lr->id }}">
                                        <button type="submit" class="px-3 py-1 bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs rounded-lg hover:bg-rose-100 transition-colors">Delete Permanently</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400 text-xs">No soft-deleted lures in trash.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expeditions Tab -->
    <div x-show="tab === 'expeditions'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Expeditions</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3 px-4">Expedition Title</th>
                        <th class="py-3 px-4">Deleted At</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($trashedExpeditions as $exp)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $exp->title }}</td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-rose-600">{{ $exp->deleted_at }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.trash.restore') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="expedition">
                                        <input type="hidden" name="id" value="{{ $exp->id }}">
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow transition-colors">Restore</button>
                                    </form>
                                    <form action="{{ route('admin.trash.force-delete') }}" method="POST" onsubmit="return confirm('Permanently delete this expedition?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="expedition">
                                        <input type="hidden" name="id" value="{{ $exp->id }}">
                                        <button type="submit" class="px-3 py-1 bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs rounded-lg hover:bg-rose-100 transition-colors">Delete Permanently</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400 text-xs">No soft-deleted expeditions in trash.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
