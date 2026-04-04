<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CatererDetails extends Component
{
    public $truck;
    public $company;

    // Booking form properties
    public $full_name;
    public $email;
    public $phno;
    public $city_name;
    public $address;
    public $from_date;
    public $to_date;
    public $no_of_person;
    public $budget_per_person;
    
    // Derived property
    public $total_budget = 0;

    protected $rules = [
        'full_name' => 'required|string',
        'email' => 'required|email',
        'phno' => 'required|string',
        'city_name' => 'required|string',
        'address' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'no_of_person' => 'required|numeric|min:1',
        'budget_per_person' => 'required|numeric|min:1',
    ];

    public function mount($id)
    {
        $this->truck = DB::table('company_food_truck')
            ->leftJoin('truck_category', 'company_food_truck.truck_cat_id', '=', 'truck_category.id')
            ->select('company_food_truck.*', 'truck_category.truck_category')
            ->where('company_food_truck.id', $id)
            ->first();

        if (!$this->truck) {
            abort(404, 'Food truck not found');
        }

        $this->company = DB::table('company')->where('id', $this->truck->company_id)->first();

        if (Auth::check()) {
            $this->full_name = Auth::user()->full_name;
            $this->email = Auth::user()->email;
            $this->phno = Auth::user()->phno;
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['no_of_person', 'budget_per_person'])) {
            $this->total_budget = floatval($this->no_of_person) * floatval($this->budget_per_person);
        }
    }

    public function book()
    {
        $this->validate();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $check = DB::table('booking')
            ->where('company_id', $this->company->id)
            ->where(function($query) {
                $query->whereBetween('from_date', [$this->from_date, $this->to_date])
                      ->orWhereBetween('to_date', [$this->from_date, $this->to_date])
                      ->orWhere(function($q) {
                          $q->where('from_date', '<=', $this->from_date)
                            ->where('to_date', '>=', $this->to_date);
                      });
            })
            ->where('booking_status', 1)
            ->exists();

        if ($check) {
            session()->flash('error', 'This truck is fully booked for those dates. Try selecting another date.');
            return;
        }

        DB::table('booking')->insert([
            'user_id' => Auth::id(),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phno' => $this->phno,
            'company_id' => $this->company->id,
            'truck_id' => $this->truck->id,
            'city_name' => $this->city_name,
            'zip_code' => $this->truck->zip_code, // Defaulting to truck zip
            'address' => $this->address,
            'meal_ids' => '0', // Default mock if they have no explicit Meal picker built
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'no_of_person' => $this->no_of_person,
            'budget_per_person' => $this->budget_per_person,
            'total_budget' => $this->total_budget,
            'booking_status' => 0, // Pending
            'status' => 1,
            'notes' => 'Booked via Web Portal',
            'created_at' => now(),
        ]);

        session()->flash('message', 'Booking request successfully sent to the caterer! They will contact you shortly.');
        $this->reset(['from_date', 'to_date', 'no_of_person', 'budget_per_person', 'city_name', 'address']);
    }

    public function render()
    {
        return view('livewire.caterer-details')->layout('layouts.app');
    }
}
