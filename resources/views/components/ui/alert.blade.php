@props(['tone' => 'info'])

@php
    $classes = [
        'info' => 'border-sky-200 bg-sky-50 text-sky-900',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-900',
        'danger' => 'border-red-200 bg-red-50 text-red-900',
    ][$tone] ?? 'border-sky-200 bg-sky-50 text-sky-900';
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border px-4 py-3 text-sm {$classes}"]) }}>
    {{ $slot }}
</div>
