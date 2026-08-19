@props([
    'col' => null,
    'type' => 'text', // text, number, date
    'sortable' => true,
    'align' => 'left', // left, center, right
    'label' => null,
    'visible' => true,
])

@php
    $alignmentClasses = [
        'left' => 'text-left justify-start',
        'center' => 'text-center justify-center',
        'right' => 'text-right justify-end',
    ][$align] ?? 'text-left justify-start';

    $textAlignment = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align] ?? 'text-left';
@endphp

<th 
    scope="col" 
    @if($col) 
        data-col="{{ $col }}" 
        data-col-label="{{ $label ?? $slot }}"
        data-col-visible="{{ $visible ? 'true' : 'false' }}"
    @endif
    {{ $attributes->merge(['class' => "py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider select-none {$textAlignment}"]) }}
>
    @if($sortable && $col)
        <button 
            type="button" 
            @click="toggleSort('{{ $col }}', '{{ $type }}', $event)" 
            class="group inline-flex items-center gap-1.5 hover:text-teal-600 focus:outline-none transition-colors cursor-pointer {{ $alignmentClasses }} w-full"
            title="Click to sort. Shift+Click for multi-column sort."
        >
            <span class="truncate">{{ $slot }}</span>

            <!-- Active Sort State Badge -->
            <span 
                x-data="{ info: null }" 
                x-effect="info = getSortInfo('{{ $col }}')" 
                class="inline-flex items-center"
            >
                <template x-if="info">
                    <span class="inline-flex items-center gap-0.5 text-teal-600 font-bold text-xs bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">
                        <span x-text="info.dir === 'asc' ? '▲' : '▼'" class="text-[10px]"></span>
                        <template x-if="info.isMulti">
                            <span x-text="info.priority" class="text-[9px] font-mono leading-none"></span>
                        </template>
                    </span>
                </template>
                <template x-if="!info">
                    <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">
                        ⇅
                    </span>
                </template>
            </span>
        </button>
    @else
        <div class="inline-flex items-center {{ $alignmentClasses }} w-full">
            <span>{{ $slot }}</span>
        </div>
    @endif
</th>
