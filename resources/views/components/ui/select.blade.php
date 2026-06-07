@props([
    'label' => null,
    'name' => null,
])

<label class="block">
    @if ($label)
        <span class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</span>
    @endif

    <select
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'block h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900']) }}
    >
        {{ $slot }}
    </select>
</label>
