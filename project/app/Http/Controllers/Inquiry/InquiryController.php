<?php

namespace App\Http\Controllers\Inquiry;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class InquiryController extends Controller
{

    function send(Request $r)
    {

        if ($this->varify_request($r, ["full_name", "email", "phno", "city_name", "address", "meal_ids", "from_date", "to_date", "no_of_person", "budget_per_person", "total_budget"])) {
            return $this->response();
        }

        $full_name = $r->full_name;
        $email = $r->email;
        $phno = $r->phno;
        $city_name = $r->city_name;
        $zip_code = $r->zip_code ?? null;
        $address = $r->address;
        $meal_ids = $r->meal_ids;
        $diet_ids = $r->diet_ids ?? 0;
        $state_ids = $r->state_ids ?? 0;
        $from_date = $r->from_date;
        $to_date = $r->to_date;
        $no_of_person = $r->no_of_person;
        $budget_per_person = $r->budget_per_person;
        $total_budget = $r->total_budget;
        $notes = $r->notes ?? null;


        $query = DB::insert("INSERT INTO inquiry (full_name,email,phno,city_name,zip_code,address,meal_ids,diet_ids,state_ids,from_date,to_date,no_of_person,budget_per_person,total_budget,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$full_name, $email, $phno, $city_name, $zip_code, $address, $meal_ids, $diet_ids, $state_ids, $from_date, $to_date, $no_of_person, $budget_per_person, $total_budget, $notes]);

        $inquiry_id = $this->getId();

        if ($query) {


            $inquiry = DB::selectOne("SELECT * FROM inquiry WHERE id = ?", [$inquiry_id]);

            $inquiry->meals_german = DB::selectOne("SELECT IFNULL(GROUP_CONCAT(german), 'N/A') AS meal_german FROM meal_category WHERE id IN ($meal_ids) ")->meal_german;
            $inquiry->meals_english = DB::selectOne("SELECT IFNULL(GROUP_CONCAT(meal_name), 'N/A') AS meal_english FROM meal_category WHERE id IN ($meal_ids) ")->meal_english;

            $inquiry->diet_german = DB::selectOne("SELECT IFNULL(GROUP_CONCAT(german), 'N/A') AS diet_german FROM diet_category WHERE id IN ($diet_ids) ")->diet_german;
            $inquiry->diet_english = DB::selectOne("SELECT IFNULL(GROUP_CONCAT(diet_name), 'N/A') AS diet_english FROM diet_category WHERE id IN ($diet_ids) ")->diet_english;

            $inquiry->state_german = DB::selectOne("SELECT IFNULL(GROUP_CONCAT(german), 'N/A') AS state_german FROM german_federal_states WHERE id IN ($state_ids) ")->state_german;
            $inquiry->state_english = DB::selectOne("SELECT IFNULL(GROUP_CONCAT(state_name), 'N/A') AS state_english FROM german_federal_states WHERE id IN ($state_ids) ")->state_english;

            $this->s = 1;
            $this->m = "Success";
            $this->r = $inquiry;
            $this->new_inquiry_mail($inquiry);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function action(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE inquiry SET";
        $perm = [];


        if ($r->has('is_followup')) {

            $query .= " is_followup = ?,";
            array_push($perm, $r->is_followup);
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
        $this->r = DB::selectOne("SELECT * FROM contact WHERE id = ? AND status = ?", [$id, 1]);
        return $this->response();
    }


    function get(Request $r)
    {

        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);

        $query = DB::select(
            "SELECT * FROM inquiry WHERE status = ? ORDER BY created_at DESC LIMIT ?,?",
            [1, $count, $offset]
        );

        if ($query) {

            foreach ($query as $key => $val) {
                $query[$key]->meal = DB::select("SELECT * FROM meal_category WHERE id IN ($val->meal_ids) AND status = ?", [1]);

                $query[$key]->diet = DB::select("SELECT * FROM diet_category WHERE id IN (" . (empty($val->diet_ids) ? '0' : $val->diet_ids) . ") AND status = ?", [1]);

                $query[$key]->state = DB::select("SELECT * FROM german_federal_states WHERE id IN (" . (empty($val->state_ids) ? '0' : $val->state_ids) . ") AND status = ?", [1]);
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM inquiry WHERE status = ?", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }
}
