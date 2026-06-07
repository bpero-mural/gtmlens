@props([
    'label' => null,
    'name' => null,
])

<label class="block">
    @if ($label)
        <span class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</span>
    @endif

    <textarea
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'block min-h-28 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900']) }}
    >{{ $slot }}</textarea>

    @if ($name)
        @error($name)
            <span class="mt-1 block text-sm text-red-700">{{ $message }}</span>
        @enderror
    @endif
</label>
