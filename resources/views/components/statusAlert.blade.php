@props([
    'status' => null,
    'type' => 'success',
])

@php
    $message = $status ?? session('status') ?? session('success');
    $errorMessage = session('error');
    $warningMessage = session('warning');

    $isError = $type === 'error' || !empty($errorMessage);
    $isWarning = $type === 'warning' || !empty($warningMessage);

    $displayMessage = $isError ? ($errorMessage ?? $message) : ($isWarning ? ($warningMessage ?? $message) : $message);

    $themeClasses = match (true) {
        $isError => 'bg-rose-50 border-rose-200 text-rose-800',
        $isWarning => 'bg-amber-50 border-amber-200 text-amber-800',
        default => 'bg-emerald-50 border-emerald-200 text-emerald-800',
    };

    $iconColor = match (true) {
        $isError => 'text-rose-600',
        $isWarning => 'text-amber-600',
        default => 'text-emerald-600',
    };

    $iconName = match (true) {
        $isError => 'alert-circle',
        $isWarning => 'alert-triangle',
        default => 'check-circle',
    };
@endphp

@if ($displayMessage)
    <div {{ $attributes->merge(['class' => "border text-xs font-bold p-4 rounded-xl shadow-sm flex items-center gap-2 {$themeClasses}"]) }} role="alert">
        <x-dynamic-component :component="'lucide-' . $iconName" class="w-4 h-4 shrink-0 {{ $iconColor }}" />
        <span>{{ $displayMessage }}</span>
    </div>
@endif
