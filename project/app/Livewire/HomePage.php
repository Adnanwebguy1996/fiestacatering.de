<?php

namespace App\Livewire;

use Livewire\Component;

class HomePage extends Component
{
    public $newsApiCalled = false;
    public $isSubscribed = false;
    public $isSubscriptionExpired = false;
    public $hasShownExpiredModal = false;
    public $subType = '';

    public function mount()
    {
        // Example logic from React: fetching user data and setting states
        // In Laravel this would typically be done via auth()->user()
    }

    public function render()
    {
        return view('livewire.home-page')
            ->layout('layouts.app');
    }
}
