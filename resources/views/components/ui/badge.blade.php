@props(['tone' => 'slate'])

@php
    $classes = [
        'blue' => 'border-blue-200 bg-blue-50 text-blue-700',
        'teal' => 'border-teal-200 bg-teal-50 text-teal-700',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-800',
        'green' => 'border-green-200 bg-green-50 text-green-700',
        'red' => 'border-red-200 bg-red-50 text-red-700',
        'yellow' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
        'slate' => 'border-slate-200 bg-slate-100 text-slate-700',
    ][$tone] ?? 'border-slate-200 bg-slate-100 text-slate-700';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md border px-2 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ $slot }}
</span>
