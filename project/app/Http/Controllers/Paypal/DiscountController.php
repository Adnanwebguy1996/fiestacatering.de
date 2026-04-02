<?php

namespace App\Http\Controllers\Paypal;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DiscountController extends Controller
{

    function add(Request $r)
    {
        if ($this->varify_request($r, ["subscription_type", "coupon_code", "coupon_name", "description", "discount_type", "discount_amount", "start_date", "end_date", "total_usage_limit", "usage_limit_per_user"])) {
            return $this->response();
        }

        $subscription_type = $r->subscription_type; // Initiallly 1: Monthly | 2: Yearly | 3: Both but now we store actual subscription and if -1 then applies on all
        $coupon_code = $r->coupon_code;
        $coupon_name = $r->coupon_name;
        $description = $r->description;
        $discount_type = $r->discount_type;
        $discount_amount = $r->discount_amount;
        $start_date = $r->start_date;
        $end_date = $r->end_date;
        $total_usage_limit = $r->total_usage_limit;
        $used_usage_count = 0;
        $usage_limit_per_user = $r->usage_limit_per_user;


        $check = DB::selectOne("SELECT * FROM discounts WHERE BINARY coupon_code = ?", [$coupon_code]);
        if ($check) {
            $this->s = 0;
            $this->m = "Coupon code already exists";
            return $this->response();
        }


        $query = DB::insert("INSERT INTO discounts (subscription_type, coupon_code, coupon_name, description, discount_type, discount_amount, start_date, end_date, total_usage_limit,used_usage_count, usage_limit_per_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$subscription_type, $coupon_code, $coupon_name, $description, $discount_type, $discount_amount, $start_date, $end_date, $total_usage_limit, $used_usage_count, $usage_limit_per_user]);
        if ($query) {
            $this->s = 1;
            $this->m = "Discount added successfully";
            $this->r = DB::selectOne("SELECT * FROM discounts WHERE id =?", [$this->getId()]);
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "Failed to add discount";
        }
    }

    function update(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }


        $id = $r->id;

        $query = "UPDATE discounts SET";
        $perm = [];


        if ($r->has('subscription_type')) {

            $query .= " subscription_type = ?,";
            array_push($perm, $r->subscription_type);
        }

        if ($r->has('coupon_name')) {

            $query .= " coupon_name = ?,";
            array_push($perm, $r->coupon_name);
        }

        if ($r->has('description')) {

            $query .= " description = ?,";
            array_push($perm, $r->description);
        }

        if ($r->has('discount_type')) {

            $query .= " discount_type = ?,";
            array_push($perm, $r->discount_type);
        }

        if ($r->has('discount_amount')) {

            $query .= " discount_amount = ?,";
            array_push($perm, $r->discount_amount);
        }

        if ($r->has('start_date')) {

            $query .= " start_date = ?,";
            array_push($perm, $r->start_date);
        }

        if ($r->has('end_date')) {

            $query .= " end_date = ?,";
            array_push($perm, $r->end_date);
        }

        if ($r->has('total_usage_limit')) {

            $query .= " total_usage_limit = ?,";
            array_push($perm, $r->total_usage_limit);
        }

        if ($r->has('usage_limit_per_user')) {

            $query .= " usage_limit_per_user = ?,";
            array_push($perm, $r->usage_limit_per_user);
        }

        if ($r->has('status')) {

            $query .= " status = ?,";
            array_push($perm, $r->status);
        }

        if (count($perm) == 0) {
            $this->s = 0;
            $this->m = "Please Provide Atleast One Field To Update";
            return $this->response();
        }



        $query = substr($query, 0, strlen($query) - 1);
        $query .= " where id = ?";
        array_push($perm, $id);
        $res = DB::update($query, $perm);

        $this->s = 1;
        $this->m = "Update Successfully";
        $this->r = DB::selectOne("SELECT * FROM discounts WHERE id = ?", [$id]);
        return $this->response();
    }

    function get(Request $r)
    {

        $current_date = Carbon::now();

        $filter = "WHERE 1 = 1";
        if ($r->has("is_admin") && $r->is_admin == 1) {
            $filter .= "";
        } else {
            $filter .= " AND start_date <= '$current_date' AND end_date >= '$current_date' AND status = 1";
        }



        $query = DB::select("SELECT * FROM discounts $filter ORDER BY id DESC");
        if ($query) {
            $this->s = 1;
            $this->m = "Discount fetched successfully";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "No Discount Found";
            return $this->response(1);
        }
    }

    function verify(Request $r)
    {
        if ($this->varify_request($r, ["subscription_id", "coupon_code"])) {
            return $this->response();
        }

        $subscription_id = $r->subscription_id;
        $coupon_code = $r->coupon_code;
        $user_id = $r->_id;


        $check_used_discount = DB::selectOne("SELECT * FROM used_discount WHERE BINARY coupon_code = ? AND user_id = ?", [$coupon_code, $user_id]);
        if ($check_used_discount) {
            $this->s = 0;
            $this->m = "You have already used this coupon code";
            return $this->response();
        } else {

            $current_date = Carbon::now();
            $query = DB::selectOne("SELECT * FROM discounts WHERE BINARY coupon_code = ? AND start_date <= '$current_date' AND end_date >= '$current_date' AND status = ? AND subscription_type = ?", [$coupon_code, 1, $subscription_id]);
            if ($query) {

                if ($query->total_usage_limit - $query->used_usage_count <= 0) {
                    $this->s = 0;
                    $this->m = "The coupon code has reached its maximum usage limit";
                    return $this->response();
                }

                $this->s = 1;
                $this->m = "Success";
                $this->r = $query;
                return $this->response();
            } else {
                $this->s = 0;
                $this->m = "Coupon code is invalid or expired";
                return $this->response();
            }
        }
    }
}
