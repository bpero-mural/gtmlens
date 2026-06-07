@props(['title' => null])

<aside {{ $attributes->merge(['class' => 'hidden']) }}>
    <div class="fixed inset-0 bg-slate-950/40"></div>
    <section class="fixed right-0 top-0 h-full w-full max-w-md bg-white p-6 shadow-xl">
        @if ($title)
            <h2 class="text-lg font-semibold">{{ $title }}</h2>
        @endif
        <div class="mt-4">{{ $slot }}</div>
    </section>
</aside>
