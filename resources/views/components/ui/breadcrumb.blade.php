@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'text-sm text-slate-500']) }} aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2">
        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                @if (! $loop->first)
                    <span>/</span>
                @endif
                @if (isset($item['href']))
                    <a wire:navigate href="{{ $item['href'] }}" class="font-medium text-slate-700 hover:text-slate-950">{{ $item['label'] }}</a>
                @else
                    <span>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
