<div @if ($hasActiveRuns) wire:poll.5s @endif>
    <x-ui.card>
        <form wire:submit="queueSourceSync" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto_auto]">
            <x-ui.input label="Salesforce CLI org alias" name="orgAlias" wire:model.live="orgAlias" placeholder="stage" />

            <div class="flex items-end">
                <x-ui.button type="submit" wire:loading.attr="disabled" wire:target="queueSourceSync">
                    <span wire:loading.remove wire:target="queueSourceSync">Run source sync</span>
                    <span wire:loading wire:target="queueSourceSync">Queueing...</span>
                </x-ui.button>
            </div>

            <div class="flex items-end">
                <x-ui.button type="button" variant="secondary" wire:click="refreshRuns" wire:loading.attr="disabled" wire:target="refreshRuns">
                    Refresh
                </x-ui.button>
            </div>
        </form>

        @if ($statusMessage)
            <div class="mt-4 rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900">
                {{ $statusMessage }}
            </div>
        @endif

        @if ($hasActiveRuns)
            <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900">
                Active sync detected. This panel refreshes automatically every 5 seconds until all active runs finish.
            </div>
        @endif
    </x-ui.card>

    <div class="mt-5">
        @if ($syncRuns->isEmpty())
            <x-ui.empty-state title="No sync runs yet" message="Run a source sync from this page or use php artisan salesforce:metadata-probe {orgAlias}." />
        @else
            <x-ui.card>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Latest 50</p>
                        <h2 class="mt-1 text-base font-semibold">Metadata sync runs</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($hasActiveRuns)
                            <x-ui.badge tone="amber">Auto-refreshing</x-ui.badge>
                        @endif
                        <x-ui.badge tone="blue">{{ $syncRuns->count() }} runs</x-ui.badge>
                    </div>
                </div>

                <x-ui.table>
                    <x-slot:head>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Org</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Progress</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Counts</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Started</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Finished</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                        </tr>
                    </x-slot:head>

                    @foreach ($syncRuns as $syncRun)
                        @php
                            $isActive = in_array($syncRun->status, ['pending', 'queued', 'running'], true);
                            $triggerLabel = match ($syncRun->triggered_by) {
                                'cli_source_retrieve' => 'Retrieving source',
                                'source_normalization' => 'Normalizing metadata',
                                'cli_probe' => 'Probing metadata',
                                default => str_replace('_', ' ', $syncRun->triggered_by),
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">#{{ $syncRun->id }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $syncRun->salesforceOrg?->alias ?? $syncRun->salesforceOrg?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <x-ui.badge :tone="$syncRun->status === 'completed' ? 'teal' : ($syncRun->status === 'failed' ? 'red' : 'amber')">{{ $syncRun->status }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $triggerLabel }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if (empty($syncRun->counts))
                                    -
                                @else
                                    <div class="space-y-1">
                                        @foreach ($syncRun->counts as $name => $count)
                                            <div>{{ str_replace('_', ' ', $name) }}: {{ $count }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $syncRun->started_at?->diffForHumans() ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $syncRun->finished_at?->diffForHumans() ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-sm">
                                @if ($isActive)
                                    <button type="button" wire:click="refreshRuns" class="font-semibold text-blue-700 hover:text-blue-900 hover:underline">Refresh</button>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </x-ui.card>
        @endif
    </div>
</div>
