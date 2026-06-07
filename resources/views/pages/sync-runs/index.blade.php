<x-layouts.app title="Sync Runs">
    <x-ui.card>
        <form method="POST" action="{{ route('sync-runs.source-sync.store') }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto]">
            @csrf
            <x-ui.input label="Salesforce CLI org alias" name="org_alias" value="stage" placeholder="stage" />
            <div class="flex items-end">
                <x-ui.button type="submit">Run source sync</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <div class="mt-5">
        @if ($syncRuns->isEmpty())
            <x-ui.empty-state title="No sync runs yet" message="Run a source sync from this page or use php artisan salesforce:metadata-probe {orgAlias}." />
        @else
            <x-ui.card>
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Latest 50</p>
                        <h2 class="mt-1 text-base font-semibold">Metadata sync runs</h2>
                    </div>
                    <x-ui.badge tone="blue">{{ $syncRuns->count() }} runs</x-ui.badge>
                </div>

                <x-ui.table>
                    <x-slot:head>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Org</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Trigger</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Counts</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Finished</th>
                        </tr>
                    </x-slot:head>

                    @foreach ($syncRuns as $syncRun)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">#{{ $syncRun->id }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $syncRun->salesforceOrg?->alias ?? $syncRun->salesforceOrg?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <x-ui.badge :tone="$syncRun->status === 'completed' ? 'teal' : ($syncRun->status === 'failed' ? 'red' : 'amber')">{{ $syncRun->status }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $syncRun->triggered_by }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ collect($syncRun->counts ?? [])->sum() }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $syncRun->finished_at?->diffForHumans() ?? '-' }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>
