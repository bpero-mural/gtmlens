<?php

namespace App\Livewire;

use App\Models\SalesforceOrg;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SalesforceOrgsPanel extends Component
{
    public function render(): View
    {
        return view('livewire.salesforce-orgs-panel', [
            'orgs' => SalesforceOrg::query()->latest()->get(),
        ]);
    }
}
