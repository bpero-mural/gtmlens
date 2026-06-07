<?php

namespace App\Livewire;

use App\Models\MetadataEntity;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MetadataSearchPanel extends Component
{
    use WithPagination;

    public string $q = '';

    public string $type = '';

    public string $selectedStatus = '';

    public function mount(array $filters = []): void
    {
        $this->q = trim((string) ($filters['q'] ?? ''));
        $this->type = trim((string) ($filters['type'] ?? ''));
        $this->selectedStatus = trim((string) ($filters['entityStatus'] ?? ''));
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['q', 'type', 'selectedStatus'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->q = '';
        $this->type = '';
        $this->selectedStatus = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $query = MetadataEntity::query()
            ->with(['salesforceOrg', 'searchDocument'])
            ->when($this->q !== '', function ($query): void {
                $like = '%'.strtolower(str_replace(['%', '_'], ['\\%', '\\_'], $this->q)).'%';

                $query->where(function ($query) use ($like): void {
                    $query->whereRaw('lower(api_name) like ?', [$like])
                        ->orWhereRaw('lower(label) like ?', [$like])
                        ->orWhereRaw('lower(external_key) like ?', [$like])
                        ->orWhereHas('searchDocument', fn ($query) => $query->whereRaw('lower(content) like ?', [$like]));
                });
            })
            ->when($this->type !== '', fn ($query) => $query->where('metadata_type', $this->type))
            ->when($this->selectedStatus !== '', fn ($query) => $query->where('status', $this->selectedStatus))
            ->orderBy('metadata_type')
            ->orderBy('api_name');

        return view('livewire.metadata-search-panel', [
            'entities' => $query->paginate(25),
            'types' => MetadataEntity::query()->select('metadata_type')->distinct()->orderBy('metadata_type')->pluck('metadata_type'),
            'statuses' => MetadataEntity::query()->select('status')->distinct()->orderBy('status')->pluck('status'),
        ]);
    }
}
