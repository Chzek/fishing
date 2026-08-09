@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Zone Hero Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-black px-3 py-1 rounded-full font-mono">
                    {{ $fishingZone->code }}
                </span>
                <h1 class="text-2xl font-extrabold text-white tracking-tight pt-1">{{ $fishingZone->name }}</h1>
                <p class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-teal-400"></i>
                    <span>{{ $fishingZone->province_state }}, {{ $fishingZone->country }} License Management Zone</span>
                </p>
            </div>

            @if($fishingZone->regulations_url)
                <a href="{{ $fishingZone->regulations_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white text-xs font-bold rounded-xl shadow-lg shadow-teal-950/40 transition-all shrink-0">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>Official Ontario Regs Guide</span>
                </a>
            @endif
        </div>

        @if($fishingZone->description)
            <div class="pt-3 border-t border-slate-800 text-xs text-slate-300 leading-relaxed max-w-3xl">
                {{ $fishingZone->description }}
            </div>
        @endif
    </div>

    <!-- Species Regulations & Limits Card -->
    @if(isset($fishingZone->rules) && count($fishingZone->rules) > 0)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="book-open" class="w-5 h-5 text-indigo-600"></i>
                    <span>Species Regulations & Possession Limits ({{ $fishingZone->code }})</span>
                </h2>
                <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">
                    {{ count($fishingZone->rules) }} Species Guidelines
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-3 px-4">Target Species</th>
                            <th scope="col" class="py-3 px-4">Open Season</th>
                            <th scope="col" class="py-3 px-4 text-center">Sport Limit (S)</th>
                            <th scope="col" class="py-3 px-4 text-center">Conservation Limit (C)</th>
                            <th scope="col" class="py-3 px-4">Size Restrictions & Slot Limits</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($fishingZone->rules as $rule)
                            <tr class="hover:bg-slate-50/70 transition-colors {{ $rule->is_aggregate ? 'bg-indigo-50/40 border-l-4 border-l-indigo-500' : '' }}">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        @if($rule->is_aggregate)
                                            <i data-lucide="layers" class="w-4 h-4 text-indigo-600 shrink-0"></i>
                                        @else
                                            <i data-lucide="fish" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                        @endif
                                        <div>
                                            <span class="block leading-tight">{{ $rule->species_name }}</span>
                                            @if($rule->is_aggregate)
                                                <span class="inline-block mt-0.5 text-[9px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded font-mono">
                                                    📊 Aggregate Limit ({{ $rule->aggregate_group }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    {{ $rule->season }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-black text-slate-900 {{ $rule->is_aggregate ? 'bg-indigo-100/50' : 'bg-slate-50/50' }}">
                                    {{ $rule->sport_limit }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-black text-teal-700 {{ $rule->is_aggregate ? 'bg-teal-100/50' : 'bg-teal-50/30' }}">
                                    {{ $rule->conservation_limit }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    <span class="font-medium text-slate-900">{{ $rule->size_restriction }}</span>
                                    @if($rule->notes)
                                        <span class="text-[11px] text-slate-500 block mt-0.5 italic">{{ $rule->notes }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Assigned Lakes in Zone -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="waves" class="w-5 h-5 text-teal-600"></i>
                <span>Registered Waterbodies in {{ $fishingZone->code }}</span>
            </h2>
            <span class="text-xs font-mono font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">
                {{ count($fishingZone->lakes) }} Lakes
            </span>
        </div>

        @if(count($fishingZone->lakes) > 0)
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-3 px-4">Lake Name</th>
                            <th scope="col" class="py-3 px-4 text-center">Coordinates</th>
                            <th scope="col" class="py-3 px-4 text-center">Catches Logged</th>
                            <th scope="col" class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($fishingZone->lakes as $lake)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <a href="{{ url('/lake/' . $lake->id) }}" class="hover:text-teal-600 hover:underline flex items-center gap-2">
                                        <i data-lucide="waves" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                        <span>{{ $lake->name }}</span>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center text-xs font-mono text-slate-600">
                                    @if($lake->latitude && $lake->longitude)
                                        {{ number_format($lake->latitude, 3) }}°N, {{ number_format($lake->longitude, 3) }}°W
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700">
                                    {{ $lake->records_count }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ url('/lake/' . $lake->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                        <span>View Water</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center text-slate-400 text-xs italic space-y-2">
                <i data-lucide="info" class="w-8 h-8 text-slate-300 mx-auto"></i>
                <p>No registered lakes currently tagged in {{ $fishingZone->code }}.</p>
                <a href="{{ url('/lake/create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:underline">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Register New Lake & Tag {{ $fishingZone->code }}</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
