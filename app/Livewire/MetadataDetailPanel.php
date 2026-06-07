<?php

namespace App\Livewire;

use App\Models\MetadataEntity;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MetadataDetailPanel extends Component
{
    public MetadataEntity $metadataEntity;

    public function mount(MetadataEntity $metadataEntity): void
    {
        $this->metadataEntity = $metadataEntity;
    }

    public function render(): View
    {
        $this->metadataEntity->load(['salesforceOrg', 'versions' => fn ($query) => $query->latest('version_number'), 'searchDocument']);

        return view('livewire.metadata-detail-panel', [
            'entity' => $this->metadataEntity,
            'latestVersion' => $this->metadataEntity->versions->first(),
        ]);
    }
}
