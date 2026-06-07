<x-layouts.app :title="$metadataEntity->api_name ?? $metadataEntity->external_key">
    <livewire:metadata-detail-panel :metadata-entity="$metadataEntity" />
</x-layouts.app>
