<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class PlaceholderPanel extends Component
{
    public string $title;

    public string $message;

    public function mount(string $title, string $message): void
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function render(): View
    {
        return view('livewire.placeholder-panel');
    }
}
