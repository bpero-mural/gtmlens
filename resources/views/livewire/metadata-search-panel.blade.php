<div>
    <x-ui.card>
        <form wire:submit.prevent class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_180px_auto]">
            <x-ui.input label="Search" name="q" wire:model.live.debounce.300ms="q" placeholder="API name, label, type, parent" />

            <label class="block">
                <span class="mb-1 block text-sm font-medium text-slate-700">Type</span>
                <select wire:model.live="type" name="type" class="block h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">All types</option>
                    @foreach ($types as $typeOption)
                        <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-sm font-medium text-slate-700">Status</span>
                <select wire:model.live="selectedStatus" name="status" class="block h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-950 shadow-sm focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end gap-2">
                <x-ui.button type="button" wire:click="resetFilters" variant="secondary">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <div class="mt-5" wire:loading.class="opacity-60">
        @if ($entities->isEmpty())
            <x-ui.empty-state title="No metadata found" message="Run source retrieve and source normalization to populate the metadata browser." />
        @else
            <x-ui.card>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Normalized metadata</p>
                        <h2 class="mt-1 text-base font-semibold">{{ $entities->total() }} results</h2>
                    </div>
                    <x-ui.badge tone="blue">Stage 1D</x-ui.badge>
                </div>

                <x-ui.table>
                    <x-slot:head>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">API Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Label</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Parent</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Org</th>
                        </tr>
                    </x-slot:head>

                    @foreach ($entities as $entity)
                        <tr>
                            <td class="px-4 py-3 text-sm"><x-ui.badge>{{ $entity->metadata_type }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">
                                <a wire:navigate href="{{ route('metadata.show', $entity) }}" class="text-blue-700 hover:text-blue-900 hover:underline">{{ $entity->api_name ?? $entity->external_key }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $entity->label ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $entity->parent_external_key ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm"><x-ui.badge :tone="$entity->status === 'active' || $entity->status === 'Active' ? 'teal' : 'slate'">{{ $entity->status }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $entity->salesforceOrg?->alias ?? '-' }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>

                <div class="mt-4">
                    {{ $entities->links() }}
                </div>
            </x-ui.card>
        @endif
    </div>
</div>
