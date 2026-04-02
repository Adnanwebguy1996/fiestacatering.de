<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StateController extends Controller
{

    function add_state(Request $r)
    {
        if ($this->varify_request($r, ["state_name"])) {
            return $this->response();
        }

        $state_name = $r->state_name;


        $query = DB::insert("INSERT INTO german_federal_states (state_name) VALUES (?)", [$state_name]);

        $state_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM german_federal_states WHERE id = ?", [$state_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_state(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE german_federal_states SET";
        $perm = [];


        if ($r->has('state_name')) {

            $query .= " state_name = ?,";
            array_push($perm, $r->state_name);
        }

        if ($r->has('german')) {

            $query .= " german = ?,";
            array_push($perm, $r->german);
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
        return $this->response();
    }

    function get_state(Request $r)
    {

        $filter = "";
        if ($r->has("search")) {
            $filter .= " AND (state_name LIKE '%$r->search%' OR german LIKE '%$r->search%') ";
        }

        $query = DB::select("SELECT * FROM german_federal_states WHERE status = ? $filter", [1]);
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM german_federal_states WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "No Records Found";
            return $this->response(1);
        }
    }

    function add_update_company_state(Request $r)
    {
        if ($this->varify_request($r, ["company_id", "state_ids"])) {
            return $this->response();
        }

        $company_id = $r->company_id;
        $state_ids = $r->state_ids;


        DB::update("UPDATE company_state SET status = 0 WHERE company_id = $company_id AND state_id NOT IN ($state_ids)");

        DB::insert("INSERT INTO company_state (company_id,state_id) SELECT $company_id,id FROM german_federal_states WHERE id NOT IN (SELECT state_id FROM company_state WHERE company_id = $company_id AND status = 1 ) AND id IN ($state_ids) ");

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }

    function add_update_food_truck_state(Request $r)
    {
        if ($this->varify_request($r, ["truck_id", "state_ids"])) {
            return $this->response();
        }

        $truck_id = $r->truck_id;
        $state_ids = $r->state_ids;


        DB::update("UPDATE company_food_truck_state SET status = 0 WHERE truck_id = $truck_id AND state_id NOT IN ($state_ids)");

        DB::insert("INSERT INTO company_food_truck_state (truck_id,state_id) SELECT $truck_id,id FROM german_federal_states WHERE id NOT IN (SELECT state_id FROM company_food_truck_state WHERE truck_id = $truck_id AND status = 1 ) AND id IN ($state_ids) ");

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }
}
