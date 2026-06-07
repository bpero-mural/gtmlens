@props([
    'label' => null,
    'name' => null,
])

<label class="inline-flex items-center gap-2 text-sm text-slate-700">
    <input
        name="{{ $name }}"
        type="checkbox"
        value="1"
        {{ $attributes->merge(['class' => 'h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900']) }}
    >
    @if ($label)
        <span>{{ $label }}</span>
    @else
        {{ $slot }}
    @endif
</label>
