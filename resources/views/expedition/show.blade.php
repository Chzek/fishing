@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Expedition Header Card -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="ship" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>{{ $expedition->description }}</span>
                    @if(view()->exists('expedition.edit'))
                        <a href="/expedition/{{ $expedition->id }}/edit" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Edit Expedition">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                    @endif
                </h1>
                <p class="text-xs text-teal-400 font-medium mt-0.5 flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                    <span>{{ $expedition->start }} &mdash; {{ $expedition->finish }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <form action="/expedition/{{ $expedition->id }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this expedition?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-rose-950/80 hover:bg-rose-900 text-rose-300 font-semibold text-xs rounded-xl border border-rose-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-400"></i>
                    <span>Delete</span>
                </button>
            </form>
            <a href="/expedition" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Return to Index
            </a>
        </div>
    </div>

    <!-- Crew Roster & Posts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Crew Members List Column -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-teal-600"></i>
                    <span>Crew Members</span>
                </h2>
                @if(view()->exists('expedition.crew.create'))
                    <a href="/crew/create?expeditions_id={{ $expedition->id }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-lg shadow transition-colors">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        <span>Add</span>
                    </a>
                @endif
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($expedition->crews as $crew)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <x-anglerAvatar :angler="$crew->angler" size="xs" />
                            <span class="font-semibold text-slate-800">{{ $crew->angler->fullName }}</span>
                        </div>
                        @if($expedition->start != $crew->joined)
                            <span class="text-[10px] text-slate-400 font-mono">Joined {{ $crew->joined }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Posts Journal Stream Column -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-teal-600"></i>
                    <span>Trip Posts & Logs</span>
                </h2>
                @if(view()->exists('expedition.post.create'))
                    <a href="/post/create?expeditions_id={{ $expedition->id }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-lg shadow transition-colors">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Post Update</span>
                    </a>
                @endif
            </div>

            @if(count($expedition->posts) > 0)
                <div class="space-y-4">
                    @foreach($expedition->posts as $post)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 flex items-start gap-3">
                            <x-anglerAvatar :angler="$post->creator" size="md" />
                            <div class="space-y-1 flex-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-900">{{ $post->creator->full_name }}</span>
                                    <span class="text-slate-400 font-mono">{{ $post->date }}</span>
                                </div>
                                <blockquote class="text-xs text-slate-700 italic border-l-2 border-teal-500 pl-2">
                                    "{{ $post->description }}"
                                </blockquote>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-slate-400 text-xs italic">
                    No posts logged for this expedition trip yet.
                </div>
            @endif
        </div>
    </div>

    <!-- Expedition Catches Log -->
    @if(count($records) > 0)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
            <h2 class="font-bold text-slate-900 text-base flex items-center gap-2 border-b border-slate-100 pb-3">
                <i data-lucide="fish" class="w-4 h-4 text-teal-600"></i>
                <span>Expedition Catches Log</span>
            </h2>

            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-3 px-4">Date</th>
                            <th scope="col" class="py-3 px-4">Angler</th>
                            <th scope="col" class="py-3 px-4">Lake</th>
                            <th scope="col" class="py-3 px-4">Species</th>
                            <th scope="col" class="py-3 px-4">Lure</th>
                            <th scope="col" class="py-3 px-4 text-center">Weight</th>
                            <th scope="col" class="py-3 px-4 text-center">Length</th>
                            <th scope="col" class="py-3 px-4">Status</th>
                            <th scope="col" class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($records as $record)
                            <tr class="hover:bg-slate-50/70 transition-colors text-xs">
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $record->caught }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $record->angler->full_name }}</td>
                                <td class="py-3 px-4 text-slate-700">{{ $record->lake->name }}</td>
                                <td class="py-3 px-4 font-bold text-teal-700">{{ $record->fishBreed->name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ optional($record->lure)->displayName ?? '—' }}</td>
                                <td class="py-3 px-4 text-center font-mono font-medium">{{ $record->weight ? number_format($record->weight, 2) . ' lbs' : '—' }}</td>
                                <td class="py-3 px-4 text-center font-mono font-medium">{{ $record->length ? number_format($record->length, 2) . ' in' : '—' }}</td>
                                <td class="py-3 px-4">
                                    @if($record->released == 1)
                                        <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-[11px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">Released</span>
                                    @else
                                        <span class="inline-flex items-center bg-sky-50 text-sky-700 text-[11px] font-bold px-2 py-0.5 rounded-full border border-sky-200">Kept</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <x-tableOptions name='record' identifier='{{ $record->id }}' />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection