@props([
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $classes = [
        'primary' => 'border-blue-700 bg-blue-700 text-white hover:bg-blue-600',
        'secondary' => 'border-slate-300 bg-white text-slate-700 hover:border-slate-400 hover:bg-slate-50',
        'quiet' => 'border-transparent bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-950',
        'danger' => 'border-red-700 bg-red-700 text-white hover:bg-red-600',
    ][$variant] ?? 'border-blue-700 bg-blue-700 text-white hover:bg-blue-600';
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex h-10 items-center gap-2 rounded-md border px-4 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {$classes}"]) }}
>
    {{ $slot }}
</button>
