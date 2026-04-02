<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Register extends Component
{
    public $full_name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $country_id = '';
    
    // We can fetch countries for the dropdown just like API did
    public $countries = [];

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:user_details',
        'password' => 'required|string|min:8|confirmed',
        'country_id' => 'required|integer',
    ];

    public function mount()
    {
        $this->countries = DB::table('countries')->where('status', 1)->get();
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'country_id' => $this->country_id,
            'role' => 1,
            'status' => 1,
        ]);

        // Automatically log them in after registration
        Auth::login($user);
        session()->regenerate();

        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.app');
    }
}
