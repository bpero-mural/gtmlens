<x-layouts.app :title="$entity->api_name ?? $entity->external_key">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a wire:navigate href="{{ route('search.index', request()->query()) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Back to metadata search</a>
        <x-ui.badge>{{ $entity->metadata_type }}</x-ui.badge>
    </div>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <x-ui.card>
            <div class="border-b border-slate-200 pb-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $entity->metadata_type }}</p>
                <h2 class="mt-1 break-words text-xl font-semibold text-slate-950">{{ $entity->api_name ?? $entity->external_key }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $entity->label ?? 'No label captured yet.' }}</p>
            </div>

            <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">External key</dt>
                    <dd class="mt-1 break-words text-sm text-slate-900">{{ $entity->external_key }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Parent</dt>
                    <dd class="mt-1 break-words text-sm text-slate-900">{{ $entity->parent_external_key ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</dt>
                    <dd class="mt-1"><x-ui.badge :tone="$entity->status === 'active' || $entity->status === 'Active' ? 'teal' : 'slate'">{{ $entity->status }}</x-ui.badge></dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Org</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $entity->salesforceOrg?->alias ?? $entity->salesforceOrg?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">First seen</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $entity->first_seen_at?->diffForHumans() ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Last seen</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $entity->last_seen_at?->diffForHumans() ?? '-' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold text-slate-950">Latest Version</h2>
            <dl class="mt-4 space-y-4">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Version</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $latestVersion?->version_number ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payload</dt>
                    <dd class="mt-1 break-words text-sm text-slate-900">{{ $latestVersion?->payload_path ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hash</dt>
                    <dd class="mt-1 break-all text-xs text-slate-600">{{ $entity->content_hash ?? '-' }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-2">
        <x-ui.card>
            <h2 class="text-base font-semibold text-slate-950">Attributes</h2>
            @if (empty($entity->attributes))
                <p class="mt-3 text-sm text-slate-500">No normalized attributes captured yet.</p>
            @else
                <dl class="mt-4 divide-y divide-slate-200">
                    @foreach ($entity->attributes as $key => $value)
                        <div class="grid gap-2 py-3 sm:grid-cols-[160px_1fr]">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ str_replace('_', ' ', $key) }}</dt>
                            <dd class="break-words text-sm text-slate-900">
                                @if (is_bool($value))
                                    {{ $value ? 'true' : 'false' }}
                                @elseif (is_array($value))
                                    {{ json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-base font-semibold text-slate-950">Search Document</h2>
            @if ($entity->searchDocument)
                <p class="mt-3 whitespace-pre-wrap break-words text-sm leading-6 text-slate-700">{{ $entity->searchDocument->content }}</p>
            @else
                <p class="mt-3 text-sm text-slate-500">No search document captured yet.</p>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
