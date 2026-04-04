<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $total_users = 0;
    public $total_company = 0;
    public $total_revenue = 0;
    
    public $usersList = [];
    public $partnersList = [];

    public function mount()
    {
        $this->total_users = DB::table('user_details')->where('status', 1)->where('role', 1)->count();
        $this->total_company = DB::table('user_details')->where('status', 1)->where('role', 2)->count();
        $revenue = DB::selectOne("SELECT SUM(price) AS total_revenue FROM user_subscription WHERE transaction_id IN (SELECT id FROM transaction WHERE status = 'ACTIVE') AND subscription_id != 'I-MANNUALUNLIMITED' ");
        $this->total_revenue = $revenue ? $revenue->total_revenue : 0;
        
        $this->usersList = DB::table('user_details')
            ->where('role', 1)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
            
        $this->partnersList = DB::table('user_details')
            ->leftJoin('company', 'user_details.id', '=', 'company.user_id')
            ->select('user_details.*', 'company.company_name', 'company.city_name')
            ->where('user_details.role', 2)
            ->orderBy('user_details.id', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.app');
    }
}
