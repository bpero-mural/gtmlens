@props(['title' => null])

<div {{ $attributes->merge(['class' => 'hidden']) }}>
    <div class="fixed inset-0 bg-slate-950/40"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <section class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            @if ($title)
                <h2 class="text-lg font-semibold">{{ $title }}</h2>
            @endif
            <div class="mt-4">{{ $slot }}</div>
        </section>
    </div>
</div>
