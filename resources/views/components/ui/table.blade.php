<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg border border-slate-200']) }}>
    <table class="min-w-full divide-y divide-slate-200 bg-white">
        @isset($head)
            <thead class="bg-slate-50">
                {{ $head }}
            </thead>
        @endisset
        <tbody class="divide-y divide-slate-200">
            {{ $slot }}
        </tbody>
    </table>
</div>
