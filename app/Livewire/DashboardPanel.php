<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DashboardPanel extends Component
{
    public function render(): View
    {
        return view('livewire.dashboard-panel', [
            'environment' => app()->environment(),
            'queue' => config('queue.default'),
            'database' => config('database.default'),
        ]);
    }
}
