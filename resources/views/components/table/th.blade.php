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

    $currentSortByRaw = request('sort_by');
    $currentSortOrderRaw = request('sort_order');

    if (!$currentSortByRaw) {
        if (request()->is('record*')) {
            $sortCols = ['date'];
            $sortOrders = ['desc'];
        } elseif (request()->is('angler*')) {
            $sortCols = ['angler', 'lastName'];
            $sortOrders = ['asc', 'asc'];
        } else {
            $sortCols = ['name', 'species', 'lake', 'lure'];
            $sortOrders = ['desc', 'desc', 'desc', 'desc'];
        }
    } else {
        $sortCols = array_values(array_filter(explode(',', $currentSortByRaw)));
        $sortOrders = explode(',', $currentSortOrderRaw ?? 'asc');
    }

    $colIndex = array_search($col, $sortCols);
    $isCurrentSort = $colIndex !== false;
    $currentOrder = $isCurrentSort ? (strtolower($sortOrders[$colIndex] ?? 'asc') === 'desc' ? 'desc' : 'asc') : null;
    $nextOrder = ($isCurrentSort && $currentOrder === 'asc') ? 'desc' : 'asc';
    $isMultiSort = count($sortCols) > 1;
    $sortPriorityBadge = ($isMultiSort && $isCurrentSort && $currentSortByRaw) ? ($colIndex + 1) : '';
@endphp


<th 
    scope="col" 
    @if($col) 
        data-col="{{ $col }}" 
        data-col-label="{{ $label ?? $slot }}"
        data-col-visible="{{ $visible ? 'true' : 'false' }}"
        x-show="isColumnVisible('{{ $col }}')"
    @endif
    {{ $attributes->merge(['class' => "py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider select-none {$textAlignment}"]) }}
>

    @if($sortable && $col)
        <a 
            href="#" 
            @click="
                $event.preventDefault();
                const urlParams = new URLSearchParams(window.location.search);
                let cols = (urlParams.get('sort_by') || '').split(',').filter(Boolean);
                let orders = (urlParams.get('sort_order') || '').split(',').filter(Boolean);
                
                if ($event.shiftKey) {
                    const idx = cols.indexOf('{{ $col }}');
                    if (idx !== -1) {
                        const cur = (orders[idx] || 'asc').toLowerCase();
                        if (cur === 'asc') {
                            orders[idx] = 'desc';
                        } else {
                            cols.splice(idx, 1);
                            orders.splice(idx, 1);
                        }
                    } else {
                        cols.push('{{ $col }}');
                        orders.push('asc');
                    }
                } else {
                    if (cols.length === 1 && cols[0] === '{{ $col }}') {
                        const cur = (orders[0] || 'asc').toLowerCase();
                        orders[0] = cur === 'asc' ? 'desc' : 'asc';
                    } else {
                        cols = ['{{ $col }}'];
                        orders = ['asc'];
                    }
                }

                if (cols.length) {
                    urlParams.set('sort_by', cols.join(','));
                    urlParams.set('sort_order', orders.join(','));
                } else {
                    urlParams.delete('sort_by');
                    urlParams.delete('sort_order');
                }
                urlParams.set('page', '1');
                window.location.search = urlParams.toString();
            "
            class="group inline-flex items-center gap-1.5 hover:text-teal-600 focus:outline-none transition-colors cursor-pointer {{ $alignmentClasses }} w-full"
            title="Sort database by {{ $label ?? $slot }} (Hold Shift for Multi-Sort)"
        >
            <span class="truncate">{{ $slot }}</span>

            <!-- Active Database Sort State Badge -->
            <span class="inline-flex items-center">
                @if($isCurrentSort)
                    <span class="inline-flex items-center gap-0.5 text-teal-600 font-bold text-xs bg-teal-50 px-1 py-0.5 rounded border border-teal-200/60">
                        <span class="text-[10px]">{{ $currentOrder === 'asc' ? '▲' : '▼' }}</span>
                        @if($isMultiSort)
                            <span class="text-[9px] font-mono text-teal-700 font-bold ml-0.5">{{ $sortPriorityBadge }}</span>
                        @endif
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


