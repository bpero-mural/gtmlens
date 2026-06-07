@props(['paginator' => null])

<div {{ $attributes->merge(['class' => 'mt-4']) }}>
    @if ($paginator)
        {{ $paginator->links() }}
    @else
        {{ $slot }}
    @endif
</div>
