<?php

namespace App\Livewire;

use Livewire\Component;

class Navbar extends Component
{
    public $themeMode = 'light';

    public function mount()
    {
        // For simplicity, we can load themeMode from Session or ignore if handled purely by AlpineJS/JS
    }

    public function toggleTheme()
    {
        $this->themeMode = $this->themeMode === 'light' ? 'dark' : 'light';
        // Usually, in TALL, you'd dispatch a browser event or handle this via Alpine.js directly
        $this->dispatch('theme-changed', mode: $this->themeMode);
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
