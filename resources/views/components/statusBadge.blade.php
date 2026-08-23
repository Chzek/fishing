@props([
    'type' => 'default',
    'label' => null,
    'size' => 'sm',
])

@php
    $typeLower = strtolower((string) $type);

    $styles = match ($typeLower) {
        'synced', 'sync_success' => 'bg-emerald-500/15 text-emerald-700 border-emerald-500/30 dark:text-emerald-300',
        'pending', 'pending_upstream' => 'bg-amber-500/15 text-amber-700 border-amber-500/30 dark:text-amber-300',
        'admin' => 'bg-purple-500/15 text-purple-700 border-purple-500/30 dark:text-purple-300',
        'pb', 'trophy' => 'bg-amber-500/20 text-amber-900 border-amber-400 font-bold',
        'kept' => 'bg-emerald-500/15 text-emerald-700 border-emerald-500/30 dark:text-emerald-300',
        'released' => 'bg-amber-500/15 text-amber-700 border-amber-500/30 dark:text-amber-300',
        'unlinked', 'warning' => 'bg-amber-500/15 text-amber-800 border-amber-500/30',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $displayLabel = $label ?? match ($typeLower) {
        'synced', 'sync_success' => 'Synced',
        'pending', 'pending_upstream' => 'Pending Sync',
        'admin' => 'Admin',
        'pb', 'trophy' => 'Personal Best',
        'released' => 'Released',
        'kept' => 'Kept',
        'unlinked' => 'Unlinked',
        default => ucfirst($typeLower),
    };

    $sizeClasses = match ($size) {
        'xs' => 'px-1.5 py-0.5 text-[10px]',
        'lg' => 'px-3 py-1 text-xs',
        default => 'px-2 py-0.5 text-[11px]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 font-semibold rounded-full border shadow-2xs font-mono {$styles} {$sizeClasses}"]) }}>
    @if(in_array($typeLower, ['synced', 'sync_success']))
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
    @elseif(in_array($typeLower, ['pending', 'pending_upstream']))
        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
    @elseif(in_array($typeLower, ['pb', 'trophy']))
        <span>🏆</span>
    @endif
    <span>{{ $displayLabel }}</span>
</span>
