<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RequirementController extends Controller
{

    function add_requirement_category(Request $r)
    {
        if ($this->varify_request($r, ["requirement"])) {
            return $this->response();
        }

        $requirement = $r->requirement;

        $query = DB::insert("INSERT INTO requirement_category (requirement) VALUES (?)", [$requirement]);

        $requirement_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM requirement_category WHERE id = ?", [$requirement_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_requirement_category(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE requirement_category SET";
        $perm = [];


        if ($r->has('requirement')) {

            $query .= " requirement = ?,";
            array_push($perm, $r->requirement);
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

    function get_requirement_category(Request $r)
    {

        $filter = "";
        if ($r->has("search")) {
            $filter .= " AND requirement LIKE '%$r->search%' ";
        }

        $query = DB::select("SELECT * FROM requirement_category WHERE status = ? $filter ORDER BY requirement ASC", [1]);
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM requirement_category WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function add_update_food_truck_requirement(Request $r)
    {
        if ($this->varify_request($r, ["truck_id", "requirement_ids"])) {
            return $this->response();
        }

        $truck_id = $r->truck_id;
        $requirement_ids = $r->requirement_ids;


        DB::update("UPDATE company_food_truck_requirement SET status = 0 WHERE truck_id = $truck_id AND requirement_id NOT IN ($requirement_ids)");

        DB::insert("INSERT INTO company_food_truck_requirement(truck_id,requirement_id) SELECT $truck_id,id FROM requirement_category WHERE id NOT IN (SELECT requirement_id FROM company_food_truck_requirement WHERE truck_id = $truck_id AND status = 1 ) AND id IN ($requirement_ids) ");

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }
}
