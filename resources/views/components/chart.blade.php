@props([
    'type' => 'doughnut',
    'data' => ['labels' => [], 'datasets' => []],
    'options' => [],
    'centerText' => null,
    'height' => 'h-44',
    'class' => '',
    'id' => null,
])

@php
    $chartId = $id ?? 'chart-' . str_replace('.', '-', uniqid('', true));
    $config = [
        'type' => $type,
        'data' => $data,
        'options' => $options,
        'centerText' => $centerText,
    ];
@endphp

<div 
    wire:ignore
    x-data="telemetryChart(@js($config))" 
    class="relative w-full {{ $height }} {{ $class }}"
>
    <canvas x-ref="canvas" id="{{ $chartId }}" class="w-full h-full"></canvas>
</div>
