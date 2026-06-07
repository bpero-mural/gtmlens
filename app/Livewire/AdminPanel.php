<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class AdminPanel extends Component
{
    public function render(): View
    {
        return view('livewire.admin-panel', [
            'roles' => ['admin', 'architect', 'developer', 'viewer'],
        ]);
    }
}
