<x-layouts.app title="Salesforce Orgs">
    @if ($orgs->isEmpty())
        <x-ui.empty-state title="No Salesforce orgs yet" message="Run php artisan salesforce:metadata-probe {orgAlias} to create a CLI placeholder org." />
    @else
        <x-ui.card>
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">CLI placeholders</p>
                    <h2 class="mt-1 text-base font-semibold">Salesforce orgs</h2>
                </div>
                <x-ui.badge tone="blue">{{ $orgs->count() }} orgs</x-ui.badge>
            </div>

            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Alias</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">API</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Updated</th>
                    </tr>
                </x-slot:head>

                @foreach ($orgs as $org)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $org->name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $org->alias ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm"><x-ui.badge>{{ $org->status }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $org->api_version }}</td>
                        <td class="px-4 py-3 text-sm text-slate-500">{{ $org->updated_at?->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
    @endif
</x-layouts.app>
