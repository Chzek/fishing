@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 overflow-hidden border border-slate-200 shrink-0">
                    @if($angler->user && $angler->user->avatar)
                        <img src="/storage/avatars/{{ $angler->user->avatar }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <i data-lucide="user" class="w-6 h-6"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $angler->firstName}} {{ $angler->middleName }} {{ $angler->lastName }}</h1>
                    <p class="text-xs text-slate-500 font-medium">Crew Angler Profile</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(view()->exists('angler.edit') && Auth::id() == $angler->id)
                    <a href='/angler/{{ $angler->id }}/edit' class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Edit</span>
                    </a>
                @endif
                <a href='/angler' class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">Return</a>
            </div>
        </div>

        @if( $count > 0 )
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center space-y-1">
                    <span class="text-2xl font-black text-slate-900 block">{{ $count }}</span>
                    <span class="text-xs text-slate-500 font-medium block">Fish Caught</span>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-br from-slate-900 to-slate-800 text-white text-center space-y-1">
                    @isset($longest)
                        <span class="text-2xl font-black text-white block">{{ number_format($longest->length, 2) }} <span class="text-xs font-normal">in.</span></span>
                        <span class="text-[11px] text-teal-300 font-medium block">{{ $longest->fishBreed->name ?? 'Fish' }}</span>
                    @else
                        <span class="text-xl font-bold text-slate-400 block py-1">—</span>
                    @endisset
                    <span class="text-[10px] uppercase font-bold text-amber-400 tracking-wider block">Personal Best</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-center space-y-1">
                    <span class="text-2xl font-black text-slate-900 block">{{ $crews }}</span>
                    <span class="text-xs text-slate-500 font-medium block">Expeditions</span>
                </div>
            </div>
        @else
            <div class="text-center py-6 text-slate-400 text-xs italic">
                This angler hasn't logged any catches yet.
            </div>
        @endif

        @if( count($records) > 0)
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-teal-600"></i>
                    <span>Recent Catches (Last 10 Logged)</span>
                </h2>

                <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th scope="col" class="py-2.5 px-3">Fish Species</th>
                                <th scope="col" class="py-2.5 px-3 text-center">Length (in)</th>
                                <th scope="col" class="py-2.5 px-3 text-center">Weight (lbs)</th>
                                <th scope="col" class="py-2.5 px-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach( $records as $record)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-2.5 px-3 font-bold text-teal-700">{{ $record->fishBreed->name }}</td>
                                    <td class="py-2.5 px-3 text-center font-mono font-medium">{{ number_format($record->length, 2) }}</td>
                                    <td class="py-2.5 px-3 text-center font-mono font-medium">{{ $record->weight ? number_format($record->weight, 2) : '—' }}</td>
                                    <td class="py-2.5 px-3 text-slate-500 font-mono">{{ $record->caught }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
