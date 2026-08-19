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

    $isCurrentSort = request('sort_by') === $col;
    $currentOrder = strtolower(request('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';
    $nextOrder = ($isCurrentSort && $currentOrder === 'asc') ? 'desc' : 'asc';
    $sortUrl = $col ? request()->fullUrlWithQuery(['sort_by' => $col, 'sort_order' => $nextOrder, 'page' => 1]) : '#';
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
        <a 
            href="{{ $sortUrl }}" 
            class="group inline-flex items-center gap-1.5 hover:text-teal-600 focus:outline-none transition-colors cursor-pointer {{ $alignmentClasses }} w-full"
            title="Sort database by {{ $label ?? $slot }}"
        >
            <span class="truncate">{{ $slot }}</span>

            <!-- Active Database Sort State Badge -->
            <span class="inline-flex items-center">
                @if($isCurrentSort)
                    <span class="inline-flex items-center gap-0.5 text-teal-600 font-bold text-xs bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">
                        <span class="text-[10px]">{{ $currentOrder === 'asc' ? '▲' : '▼' }}</span>
                    </span>
                @else
                    <span class="text-slate-300 group-hover:text-slate-500 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity">
                        ⇅
                    </span>
                @endif
            </span>
        </a>
    @else
        <div class="inline-flex items-center {{ $alignmentClasses }} w-full">
            <span>{{ $slot }}</span>
        </div>
    @endif
</th>

