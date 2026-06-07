<x-ui.card>
    <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
        <div>
            <x-ui.badge tone="teal">Livewire active</x-ui.badge>
            <h2 class="mt-3 text-lg font-semibold">Local foundation status</h2>
            <p class="mt-1 text-sm text-slate-600">This component is rendered by Livewire and backed by the Stage 0 Laravel app.</p>
        </div>

        <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($checks as $check)
                <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                    {{ $check }}
                </div>
            @endforeach
        </div>
    </div>
</x-ui.card>
