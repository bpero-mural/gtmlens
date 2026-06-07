<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class StageZeroStatus extends Component
{
    public function render(): View
    {
        return view('livewire.stage-zero-status', [
            'checks' => [
                'Docker-first Laravel foundation',
                'PostgreSQL database queues',
                'Local auth with seeded roles',
                'Livewire and Tailwind shell',
            ],
        ]);
    }
}
