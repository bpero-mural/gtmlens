@props(['items' => []])

<div {{ $attributes->merge(['class' => 'border-b border-slate-200']) }}>
    <nav class="-mb-px flex gap-6" aria-label="Tabs">
        @forelse ($items as $item)
            <a href="{{ $item['href'] ?? '#' }}" class="border-b-2 border-transparent px-1 py-3 text-sm font-medium text-slate-600 hover:border-slate-300 hover:text-slate-950">
                {{ $item['label'] }}
            </a>
        @empty
            {{ $slot }}
        @endforelse
    </nav>
</div>
