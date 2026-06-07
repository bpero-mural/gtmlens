@props(['label' => 'Menu'])

<details {{ $attributes->merge(['class' => 'relative inline-block']) }}>
    <summary class="inline-flex h-10 cursor-pointer list-none items-center rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        {{ $label }}
    </summary>
    <div class="absolute right-0 z-10 mt-2 min-w-48 rounded-md border border-slate-200 bg-white p-2 shadow-lg">
        {{ $slot }}
    </div>
</details>
