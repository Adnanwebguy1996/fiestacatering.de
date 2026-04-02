<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class AdminController extends Controller
{

    function analytics(Request $r)
    {

        $total_users = DB::selectOne("SELECT COUNT(id) AS total_users FROM user_details WHERE status = ? AND role = ?", [1, 1])->total_users;

        $total_company = DB::selectOne("SELECT COUNT(id) AS total_company FROM user_details WHERE status = ? AND role = ?", [1, 2])->total_company;

        $total_revenue = DB::selectOne("SELECT SUM(price) AS total_revenue FROM user_subscription WHERE transaction_id IN (SELECT id FROM transaction WHERE status = 'ACTIVE') AND subscription_id != 'I-MANNUALUNLIMITED' ")->total_revenue;

        $this->s = 1;
        $this->m = "Success";
        $this->r = ["total_users" => $total_users, "total_company" => $total_company, "total_revenue" => $total_revenue];
        return $this->response();
    }
}
