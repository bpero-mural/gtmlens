<x-layouts.app title="Metadata Search">
    <livewire:metadata-search-panel :filters="['q' => request()->query('q', ''), 'type' => request()->query('type', ''), 'entityStatus' => request()->query('status', '')]" />
</x-layouts.app>
