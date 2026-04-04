<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $trucks = [];
    public $bookings = [];

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            // First find company associated with user
            $company = DB::table('company')->where('user_id', $user->id)->first();
            
            if ($company) {
                // Fetch trucks
                $this->trucks = DB::table('company_food_truck')
                    ->leftJoin('truck_category', 'company_food_truck.truck_cat_id', '=', 'truck_category.id')
                    ->where('company_food_truck.company_id', $company->id)
                    ->where('company_food_truck.status', '!=', 0)
                    ->select('company_food_truck.*', 'truck_category.truck_category')
                    ->get()
                    ->toArray();
                    
                // Fetch bookings
                $this->bookings = DB::table('booking')
                    ->where('company_id', $company->id)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->toArray();
            }
        }
    }

    public function render()
    {
        return view('livewire.user.dashboard')->layout('layouts.app');
    }
}
