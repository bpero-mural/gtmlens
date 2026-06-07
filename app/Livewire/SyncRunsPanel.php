<?php

namespace App\Livewire;

use App\Jobs\RunSalesforceSourceSync;
use App\Models\SyncRun;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class SyncRunsPanel extends Component
{
    public string $orgAlias = 'stage';

    public ?string $statusMessage = null;

    /**
     * @return array<string, array<int, string|string[]>>
     */
    protected function rules(): array
    {
        return [
            'orgAlias' => ['required', 'string', 'max:80', 'regex:/^[A-Za-z0-9_.-]+$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'orgAlias.regex' => 'Use a safe Salesforce CLI alias with letters, numbers, dots, underscores, or hyphens only.',
        ];
    }

    public function queueSourceSync(): void
    {
        $validated = $this->validate();
        $alias = $validated['orgAlias'];

        RunSalesforceSourceSync::dispatch($alias);

        $this->statusMessage = "Salesforce source sync was queued for [{$alias}].";
    }

    public function refreshRuns(): void
    {
        $this->statusMessage = null;
    }

    public function render(): View
    {
        $syncRuns = SyncRun::query()
            ->with('salesforceOrg')
            ->latest()
            ->limit(50)
            ->get();

        return view('livewire.sync-runs-panel', [
            'syncRuns' => $syncRuns,
            'hasActiveRuns' => $this->hasActiveRuns($syncRuns),
        ]);
    }

    /**
     * @param Collection<int, SyncRun> $syncRuns
     */
    private function hasActiveRuns(Collection $syncRuns): bool
    {
        return $syncRuns->contains(fn (SyncRun $syncRun): bool => in_array($syncRun->status, ['pending', 'queued', 'running'], true));
    }
}
