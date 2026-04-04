<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TruckForm extends Component
{
    public $truck_name;
    public $truck_cat_id;
    public $description;
    public $address;
    public $size;
    public $operating_mode;
    public $is_water_required = 0;

    public $categories = [];

    protected $rules = [
        'truck_name' => 'required|string|max:255',
        'truck_cat_id' => 'required|integer',
        'address' => 'required|string',
        'size' => 'required|string',
        'operating_mode' => 'required|string',
    ];

    public function mount()
    {
        $this->categories = DB::table('truck_category')->where('status', 1)->get();
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Find or create company
        $company = DB::table('company')->where('user_id', $user->id)->first();
        if (!$company) {
            $companyId = DB::table('company')->insertGetId([
                'user_id' => $user->id,
                'company_name' => $user->full_name . ' Catering',
                'status' => 1,
            ]);
        } else {
            $companyId = $company->id;
        }

        DB::table('company_food_truck')->insert([
            'company_id' => $companyId,
            'truck_name' => $this->truck_name,
            'truck_cat_id' => $this->truck_cat_id,
            'description' => $this->description,
            'address' => $this->address,
            'size' => $this->size,
            'operating_mode' => $this->operating_mode,
            'is_water_required' => $this->is_water_required,
            'status' => 1
        ]);

        session()->flash('message', 'Food truck added successfully.');
        return redirect()->route('user.dashboard');
    }

    public function render()
    {
        return view('livewire.user.truck-form')->layout('layouts.app');
    }
}
