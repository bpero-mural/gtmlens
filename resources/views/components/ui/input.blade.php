@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
])

<label class="block">
    @if ($label)
        <span class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</span>
    @endif

    <input
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => 'block h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600']) }}
    >

    @if ($name)
        @error($name)
            <span class="mt-1 block text-sm font-medium text-red-700">{{ $message }}</span>
        @enderror
    @endif
</label>
