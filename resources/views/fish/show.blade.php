@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="fish" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ $fish->name }}</h1>
                <p class="text-xs text-teal-400 font-medium">{{ $fish->family?->name ?? 'N/A' }} Family</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="/fish/breed/{{ $fish->id }}/edit" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit Species</span>
            </a>
            <a href="/fish" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Back to Index
            </a>
        </div>
    </div>

    @if($fish->image)
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex justify-center">
            <img src="{{ asset('/images/fish/'.$fish->image.'.jpg') }}" class="max-h-64 object-contain rounded-xl">
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 text-center space-y-1">
            <span class="text-3xl font-black text-slate-900 block">{{ $count }}</span>
            <span class="text-xs text-slate-500 font-medium block">Total Catches Logged</span>
        </div>

        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-md border border-slate-700/50 text-center space-y-1">
            <span class="text-3xl font-black text-white block">{{ $longest }} <span class="text-xs font-normal">in.</span></span>
            <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 block pt-1">Longest Record</span>
        </div>

        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-md border border-slate-700/50 text-center space-y-1">
            <span class="text-3xl font-black text-white block">{{ $fattest }} <span class="text-xs font-normal">lbs.</span></span>
            <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 block pt-1">Fattest Record</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
            <i data-lucide="waves" class="w-4 h-4 text-teal-600"></i>
            <span>Lakes Logged For {{ $fish->name }}</span>
        </h2>

        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="py-2.5 px-3">Lake Name</th>
                        <th scope="col" class="py-2.5 px-3 text-center">Latitude</th>
                        <th scope="col" class="py-2.5 px-3 text-center">Longitude</th>
                        <th scope="col" class="py-2.5 px-3 text-center">Catches</th>
                        <th scope="col" class="py-2.5 px-3 text-center">Length (Min / Max / Avg)</th>
                        <th scope="col" class="py-2.5 px-3 text-center">Visits</th>
                        <th scope="col" class="py-2.5 px-3 text-right">View Lake</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($lakes as $lake)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 font-bold text-slate-900">{{ $lake->lake->name }}</td>
                            <td class="py-2.5 px-3 text-center font-mono text-slate-500">{{ number_format($lake->lake->latitude, 4) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono text-slate-500">{{ number_format($lake->lake->longitude, 4) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono font-bold text-teal-700">{{ $lake->count }}</td>
                            <td class="py-2.5 px-3 text-center font-mono text-slate-600">{{ $lake->min_length }}/{{ $lake->max_length }}/{{ $lake->avg_length }} in.</td>
                            <td class="py-2.5 px-3 text-center font-mono text-slate-800">{{ $lake->visits }}</td>
                            <td class="py-2.5 px-3 text-right">
                                <a href='/lake/{{ $lake->lake->id }}' class="p-1 rounded-lg text-slate-500 hover:text-teal-600 hover:bg-teal-50 transition-colors inline-block" title="View Lake">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
