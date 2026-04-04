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
        // Use a static list since countries table is not present
        $this->countries = [
            (object)['id' => 1, 'name' => 'Germany'],
            (object)['id' => 2, 'name' => 'Austria'],
            (object)['id' => 3, 'name' => 'Switzerland'],
            (object)['id' => 4, 'name' => 'United States'],
            (object)['id' => 5, 'name' => 'United Kingdom'],
        ];
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'country_id' => $this->country_id,
            'dob' => '2000-01-01', // Default required by schema
            'phno' => '', // Default required by schema
            'phno_cc' => '', // Default required by schema
            'role' => 1,
            'status' => 1,
        ]);

        // Automatically log them in after registration
        Auth::login($user);
        session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.app');
    }
}
