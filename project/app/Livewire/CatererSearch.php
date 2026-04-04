<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class CatererSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $type = ''; // Food Truck, Caterer etc mapped to truck_category

    // Use Livewire pagination styles
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DB::table('company_food_truck')
            ->leftJoin('truck_category', 'company_food_truck.truck_cat_id', '=', 'truck_category.id')
            ->leftJoin('company', 'company_food_truck.company_id', '=', 'company.id')
            ->select('company_food_truck.*', 'truck_category.truck_category')
            ->where('company_food_truck.status', 1)
            ->where('company.status', 1);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('company_food_truck.truck_name', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('company_food_truck.description', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('company_food_truck.address', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('company_food_truck.zip_code', 'LIKE', '%' . $this->search . '%');
            });
        }

        if (!empty($this->type)) {
            $query->where('company_food_truck.truck_cat_id', $this->type);
        }

        $trucks = $query->paginate(12);
        
        $categories = DB::table('truck_category')->where('status', 1)->get();

        return view('livewire.caterer-search', [
            'trucks' => $trucks,
            'categories' => $categories
        ])->layout('layouts.app');
    }
}
