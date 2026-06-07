<div class="space-y-6">
    <x-ui.breadcrumb :items="[['label' => 'Dashboard']]" />

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.05)]">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap gap-2">
                    <x-ui.badge tone="teal">Healthy foundation</x-ui.badge>
                    <x-ui.badge tone="amber">No Salesforce sync yet</x-ui.badge>
                </div>
                <h2 class="mt-4 text-2xl font-semibold text-slate-950">GTM Lens is ready for controlled metadata work.</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Stage 0 establishes the local application surface, database foundation, auth boundary, and design contract before any Salesforce connection is introduced.</p>
            </div>

            <div class="grid min-w-72 grid-cols-2 gap-2">
                <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-xs text-slate-500">Runtime</p>
                    <p class="mt-1 text-sm font-semibold">Docker Compose</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-xs text-slate-500">Access</p>
                    <p class="mt-1 text-sm font-semibold">Local auth</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3">
        <x-ui.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Environment</p>
            <p class="mt-2 text-2xl font-semibold">{{ $environment }}</p>
            <p class="mt-1 text-sm text-slate-500">Docker-first local runtime</p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Queue</p>
            <p class="mt-2 text-2xl font-semibold">{{ $queue }}</p>
            <p class="mt-1 text-sm text-slate-500">Prepared for future sync jobs</p>
        </x-ui.card>

        <x-ui.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Database</p>
            <p class="mt-2 text-2xl font-semibold">{{ $database }}</p>
            <p class="mt-1 text-sm text-slate-500">PostgreSQL search foundation</p>
        </x-ui.card>
    </section>

    <livewire:stage-zero-status />

    <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
        <x-ui.card>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">MVP surfaces</p>
                    <h2 class="mt-1 text-base font-semibold">Navigation map</h2>
                </div>
                <x-ui.badge>Placeholders</x-ui.badge>
            </div>

            <div class="mt-4 overflow-hidden rounded-lg border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Area</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Purpose</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Stage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ([
                            ['Salesforce Orgs', 'Connection inventory', 'Stage 1'],
                            ['Search', 'Metadata quick find', 'Stage 3'],
                            ['Dictionary', 'Business definitions and ownership', 'Stage 4'],
                            ['Timeline', 'Change history between syncs', 'Stage 6'],
                            ['Issues', 'Potential metadata risks', 'Stage 6'],
                        ] as [$area, $purpose, $stage])
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $area }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $purpose }}</td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ $stage }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Design contract</p>
            <h2 class="mt-1 text-base font-semibold">DESIGN.md active</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">The repo now has a design-system file for future UI work: tokens, layout rules, color intent, accessibility, and MVP boundaries.</p>
            <div class="mt-4 space-y-2">
                <div class="rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-900">Enterprise structure</div>
                <div class="rounded-md border border-teal-200 bg-teal-50 px-3 py-2 text-sm text-teal-900">Metadata-first clarity</div>
                <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-950">Strict data boundary</div>
            </div>
        </x-ui.card>
    </section>

    <x-ui.alert>
        Stage 0 intentionally stops at local auth, Docker, schema, UI shell, and documentation. Salesforce OAuth and sync begin in Stage 1.
    </x-ui.alert>
    </div>
