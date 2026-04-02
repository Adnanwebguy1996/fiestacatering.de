<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MealDietController extends Controller
{
    function add_meal_category(Request $r)
    {
        if ($this->varify_request($r, ["meal_name"])) {
            return $this->response();
        }

        $meal_name = $r->meal_name;
        if ($r->hasFile("image")) {
            $image = $this->upload_file($r->image, $this->meal_image());
        } else {
            $image = null;
        }

        $query = DB::insert("INSERT INTO meal_category (meal_name,image) VALUES (?,?)", [$meal_name, $image]);

        $meal_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM meal_category WHERE id = ?", [$meal_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_meal_category(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE meal_category SET";
        $perm = [];


        if ($r->has('meal_name')) {

            $query .= " meal_name = ?,";
            array_push($perm, $r->meal_name);
        }

        if ($r->has('german')) {

            $query .= " german = ?,";
            array_push($perm, $r->german);
        }

        if ($r->hasFile('image')) {

            $image = $this->upload_file($r->image, $this->meal_image());

            $query .= " image = ?,";
            array_push($perm, $image);
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

    function get_meal_category(Request $r)
    {

        $filter = "";
        if ($r->has("search")) {
            $filter .= " AND (meal_name LIKE '%$r->search%' OR german LIKE '%$r->search%') ";
        }

        $query = DB::select("SELECT * FROM meal_category WHERE status = ? $filter ORDER BY meal_name ASC", [1]);
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM meal_category WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function add_diet_category(Request $r)
    {
        if ($this->varify_request($r, ["diet_name"])) {
            return $this->response();
        }

        $diet_name = $r->diet_name;
        if ($r->hasFile("image")) {
            $image = $this->upload_file($r->image, $this->diet_image());
        } else {
            $image = null;
        }

        $query = DB::insert("INSERT INTO diet_category (diet_name,image) VALUES (?,?)", [$diet_name, $image]);

        $meal_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM diet_category WHERE id = ?", [$meal_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_diet_category(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE diet_category SET";
        $perm = [];


        if ($r->has('diet_name')) {

            $query .= " diet_name = ?,";
            array_push($perm, $r->diet_name);
        }

        if ($r->has('german')) {

            $query .= " german = ?,";
            array_push($perm, $r->german);
        }

        if ($r->hasFile('image')) {

            $image = $this->upload_file($r->image, $this->diet_image());

            $query .= " image = ?,";
            array_push($perm, $image);
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

    function get_diet_category(Request $r)
    {

        $filter = "";
        if ($r->has("search")) {
            $filter .= " AND (diet_name LIKE '%$r->search%' OR german LIKE '%$r->search%') ";
        }

        $query = DB::select("SELECT * FROM diet_category WHERE status = ? $filter ORDER BY diet_name ASC", [1]);
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM diet_category WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function add_update_company_meal(Request $r)
    {
        if ($this->varify_request($r, ["company_id", "meal_ids"])) {
            return $this->response();
        }

        $company_id = $r->company_id;
        $meal_ids = $r->meal_ids;


        DB::update("UPDATE company_meal SET status = 0 WHERE company_id = $company_id AND meal_id NOT IN ($meal_ids)");

        DB::insert("INSERT INTO company_meal(company_id,meal_id) SELECT $company_id,id FROM meal_category WHERE id NOT IN (SELECT meal_id FROM company_meal WHERE company_id = $company_id AND status = 1 ) AND id IN ($meal_ids) ");

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }

    function add_update_company_diet(Request $r)
    {
        if ($this->varify_request($r, ["company_id", "diet_ids"])) {
            return $this->response();
        }

        $company_id = $r->company_id;
        $diet_ids = $r->diet_ids;


        DB::update("UPDATE company_diet SET status = 0 WHERE company_id = $company_id AND diet_id NOT IN ($diet_ids)");

        DB::insert("INSERT INTO company_diet(company_id,diet_id) SELECT $company_id,id FROM diet_category WHERE id NOT IN (SELECT diet_id FROM company_diet WHERE company_id = $company_id AND status = 1 ) AND id IN ($diet_ids) ");

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }

    function add_update_food_truck_meal(Request $r)
    {
        if ($this->varify_request($r, ["truck_id", "meal_ids"])) {
            return $this->response();
        }

        $truck_id = $r->truck_id;
        $meal_ids = $r->meal_ids;


        DB::update("UPDATE company_food_truck_meal SET status = 0 WHERE truck_id = $truck_id AND meal_id NOT IN ($meal_ids)");

        DB::insert("INSERT INTO company_food_truck_meal(truck_id,meal_id) SELECT $truck_id,id FROM meal_category WHERE id NOT IN (SELECT meal_id FROM company_food_truck_meal WHERE truck_id = $truck_id AND status = 1 ) AND id IN ($meal_ids) ");

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }

    function add_update_food_truck_diet(Request $r)
    {
        if ($this->varify_request($r, ["truck_id", "diet_ids"])) {
            return $this->response();
        }

        $truck_id = $r->truck_id;
        $diet_ids = $r->diet_ids;


        DB::update("UPDATE company_food_truck_diet SET status = 0 WHERE truck_id = $truck_id AND diet_id NOT IN ($diet_ids)");

        DB::insert("INSERT INTO company_food_truck_diet(truck_id,diet_id) SELECT $truck_id,id FROM diet_category WHERE id NOT IN (SELECT diet_id FROM company_food_truck_diet WHERE truck_id = $truck_id AND status = 1 ) AND id IN ($diet_ids) ");

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }
}
