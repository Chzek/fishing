@props([
    'icon' => 'fish-off',
    'title' => 'No Records Logged',
    'description' => 'There are no entries recorded yet for this selection.',
    'actionUrl' => null,
    'actionLabel' => 'Add Entry',
])

@php
    $safeIcon = match($icon) {
        'database-off' => 'database',
        default => $icon,
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm text-center space-y-3']) }}>
    <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200/80 text-slate-400 flex items-center justify-center mx-auto shadow-inner">
        <x-dynamic-component :component="'lucide-' . $safeIcon" class="w-6 h-6" />
    </div>
    <div class="space-y-1 max-w-sm mx-auto">
        <h3 class="text-sm font-bold text-slate-900 tracking-tight">{{ $title }}</h3>
        <p class="text-xs text-slate-500 leading-relaxed">{{ $description }}</p>
    </div>
    @if($actionUrl)
        <div class="pt-2">
            <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors cursor-pointer">
                <x-lucide-plus class="w-3.5 h-3.5" />
                <span>{{ $actionLabel }}</span>
            </a>
        </div>
    @endif
</div>
