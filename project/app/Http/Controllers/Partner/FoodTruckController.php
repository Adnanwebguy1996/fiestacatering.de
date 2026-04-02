<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class FoodTruckController extends Controller
{
    function add_category(Request $r)
    {
        if ($this->varify_request($r, ["truck_category"])) {
            return $this->response();
        }

        $truck_category = $r->truck_category;

        $query = DB::insert("INSERT INTO truck_category (truck_category) VALUES (?)", [$truck_category]);

        $truck_category_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM truck_category WHERE id = ?", [$truck_category_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_category(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE truck_category SET";
        $perm = [];


        if ($r->has('truck_category')) {

            $query .= " truck_category = ?,";
            array_push($perm, $r->truck_category);
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

    function get_category(Request $r)
    {

        $query = DB::select("SELECT * FROM truck_category WHERE status = ? ORDER BY truck_category ASC", [1]);
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM truck_category WHERE status = ?", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function add_truck(Request $r)
    {
        if ($this->varify_request($r, ["company_id", "truck_name", "truck_cat_id", "address", "size", "operating_mode", "is_water_required"])) {
            return $this->response();
        }

        $company_id = $r->company_id;
        $truck_name = $r->truck_name;
        $truck_cat_id = $r->truck_cat_id;
        $specifications = $r->input("specifications", null);
        $address = $r->address;
        $zip_code = $r->zip_code ?? null;
        $lat = $r->input("lat", null);
        $lng = $r->input("lng", null);
        $work_area_radius = $r->work_area_radius ?? null;
        $description =  $r->input("description", null);
        $size = $r->size;
        $electricity_type = $r->input("electricity_type", null);
        $operating_mode = $r->operating_mode;
        $is_water_required = $r->is_water_required;


        $company_details = DB::selectOne("SELECT * FROM company WHERE id = ?", [$company_id]);

        $truck_count = DB::selectOne("SELECT COUNT(id) AS truck_count FROM company_food_truck WHERE company_id = ? AND status != ?", [$company_id, 0])->truck_count;


        $user_subscription = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$company_details->user_id, 1]);

        if ($user_subscription) {
            if ($user_subscription->truck_limit != -1) {
                if ($truck_count >= $user_subscription->truck_limit) {
                    $this->s = 0;
                    $this->m = "You have reached your truck limit.";
                    return $this->response();
                }
            }
        }

        $query = DB::insert("INSERT INTO company_food_truck (company_id,truck_name,truck_cat_id,specifications,address,zip_code,lat,lng,work_area_radius,description,size,electricity_type,operating_mode,is_water_required) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$company_id, $truck_name, $truck_cat_id, $specifications, $address, $zip_code, $lat, $lng, $work_area_radius, $description, $size, $electricity_type, $operating_mode, $is_water_required]);

        $truck_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Truck Added Successfully";
            $this->r = DB::selectOne("SELECT company_food_truck.*,truck_category.truck_category FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id WHERE company_food_truck.id = ?", [$truck_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_truck(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE company_food_truck SET";
        $perm = [];


        if ($r->has('truck_name')) {

            $query .= " truck_name = ?,";
            array_push($perm, $r->truck_name);
        }

        if ($r->has('truck_cat_id')) {

            $query .= " truck_cat_id = ?,";
            array_push($perm, $r->truck_cat_id);
        }

        if ($r->has('specifications')) {

            $query .= " specifications = ?,";
            array_push($perm, $r->specifications);
        }

        if ($r->has('address')) {

            $query .= " address = ?,";
            array_push($perm, $r->address);
        }

        if ($r->has('zip_code')) {

            $query .= " zip_code = ?,";
            array_push($perm, $r->zip_code);
        }

        if ($r->has('lat')) {

            $query .= " lat = ?,";
            array_push($perm, $r->lat);
        }
        if ($r->has('lng')) {

            $query .= " lng = ?,";
            array_push($perm, $r->lng);
        }

        if ($r->has('work_area_radius')) {

            $query .= " work_area_radius = ?,";
            array_push($perm, $r->work_area_radius);
        }

        if ($r->has('description')) {

            $query .= " description = ?,";
            array_push($perm, $r->description);
        }

        if ($r->has('size')) {

            $query .= " size = ?,";
            array_push($perm, $r->size);
        }

        if ($r->has('electricity_type')) {

            $query .= " electricity_type = ?,";
            array_push($perm, $r->electricity_type);
        }

        if ($r->has('operating_mode')) {

            $query .= " operating_mode = ?,";
            array_push($perm, $r->operating_mode);
        }

        if ($r->has('is_water_required')) {

            $query .= " is_water_required = ?,";
            array_push($perm, $r->is_water_required);
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
        $this->r = DB::selectOne("SELECT company_food_truck.*,truck_category.truck_category FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id WHERE company_food_truck.id = ?", [$id]);
        return $this->response();
    }

    function add_truck_image(Request $r)
    {
        if ($this->varify_request($r, ["truck_id"], ["image"])) {
            return $this->response();
        }

        $truck_id = $r->truck_id;
        $image = $this->upload_file($r->image, $this->truck_image());

        $query = DB::insert("INSERT INTO company_food_truck_image (truck_id,image) VALUES (?,?)", [$truck_id, $image]);
        $image_id = $this->getId();
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM company_food_truck_image WHERE id = ?", [$image_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_truck_image(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE company_food_truck_image SET";
        $perm = [];


        if ($r->hasFile('image')) {

            $image = $this->upload_file($r->image, $this->truck_image());

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

    function get_all(Request $r)
    {
        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);

        $filter = "";
        if ($r->has("meal_ids")) {
            // Commented code is applied on food truck meal and diet
            // $filter .= " AND company_food_truck.id IN (SELECT DISTINCT truck_id FROM company_food_truck_meal WHERE meal_id IN ($r->meal_ids))";

            $filter .= " AND EXISTS (
                SELECT 1
                FROM company_food_truck_meal
                WHERE company_food_truck_meal.truck_id = company_food_truck.id
                AND company_food_truck_meal.meal_id IN ($r->meal_ids) AND company_food_truck_meal.status = 1
            )";

            // $filter .= " AND company_food_truck.company_id IN (SELECT DISTINCT company_id FROM company_meal WHERE meal_id IN ($r->meal_ids))";

            // $filter .= " AND EXISTS (
            //     SELECT 1
            //     FROM company_meal
            //     WHERE company_meal.company_id = company_food_truck.company_id
            //     AND company_meal.meal_id IN ($r->meal_ids) AND company_meal.status = 1
            // )";
        }

        if ($r->has("diet_ids")) {
            // Commented code is applied on food truck meal and diet
            // $filter .= " AND company_food_truck.id IN (SELECT DISTINCT truck_id FROM company_food_truck_diet WHERE diet_id IN ($r->diet_ids))";

            $filter .= " AND EXISTS (
                SELECT 1
                FROM company_food_truck_diet
                WHERE company_food_truck_diet.truck_id = company_food_truck.id
                AND company_food_truck_diet.diet_id IN ($r->diet_ids) AND company_food_truck_diet.status = 1
            )";

            // $filter .= " AND company_food_truck.company_id IN (SELECT DISTINCT company_id FROM company_diet WHERE diet_id IN ($r->diet_ids)";

            // $filter .= " AND EXISTS (
            //     SELECT 1
            //     FROM company_diet
            //     WHERE company_diet.company_id = company_food_truck.company_id
            //     AND company_diet.diet_id IN ($r->diet_ids) AND company_diet.status = 1
            // )";
        }

        if ($r->has("state_ids")) {

            // $filter .= " AND company_food_truck.truck_id IN (SELECT DISTINCT truck_id FROM company_food_truck_state WHERE state_id IN ($r->state_ids)";

            $filter .= " AND EXISTS (
                SELECT 1
                FROM company_food_truck_state
                WHERE company_food_truck_state.truck_id = company_food_truck.id
                AND company_food_truck_state.state_id IN ($r->state_ids) AND company_food_truck_state.status = 1
            )";
        }

        if ($r->has("zip_code")) {
            $filter .= " AND company_food_truck.zip_code = $r->zip_code";
        }

        if ($r->has("company_id")) {
            $filter .= " AND company_food_truck.company_id = $r->company_id";
        }

        if ($r->has("truck_cat_id")) {
            $filter .= " AND company_food_truck.truck_cat_id = $r->truck_cat_id";
        }

        if ($r->has("is_premium") && $r->is_premium == 1) {
            $filter .= " AND user_subscription.truck_limit IN (3,-1)";
        }

        if ($r->has("is_admin") && $r->is_admin == 1) {
            $order = "company_food_truck.created_at DESC";
        } else {
            $filter .= " AND user_subscription.end_time > CURRENT_TIMESTAMP()";
            $order = " company.is_default DESC,IFNULL(avg_rating, company_food_truck.created_at) DESC";
        }

        $query = DB::select(
            "SELECT
                company_food_truck.*,
                truck_category.truck_category,
                company.is_default,
                company.user_id,
                IFNULL((SELECT AVG(rating) AS avg_rating FROM rating WHERE company_id = company_food_truck.company_id AND is_display = 1 AND status = 1), 0) AS avg_rating,
                IFNULL((SELECT COUNT(id) AS total_rating FROM rating WHERE company_id = company_food_truck.company_id AND is_display = 1 AND status = 1), 0) AS total_rating
                    FROM
                         company_food_truck
                    LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id
                    LEFT JOIN company ON company_food_truck.company_id = company.id
                    LEFT JOIN user_subscription ON user_subscription.user_id = company.user_id AND user_subscription.is_current = 1
                    WHERE
                        company_food_truck.status = ? AND company.status = ? $filter
                        ORDER BY
                            $order LIMIT ?, ?",
            [1, 1, $count, $offset]
        );

        if ($query) {

            foreach ($query as $key => $val) {

                $query[$key]->subscription = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$val->user_id, 1]);

                $query[$key]->food_truck_image = DB::select("SELECT * FROM company_food_truck_image WHERE truck_id = ? AND status = ?", [$val->id, 1]);

                $query[$key]->meal = DB::select("SELECT company_food_truck_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_food_truck_meal LEFT JOIN meal_category ON company_food_truck_meal.meal_id = meal_category.id WHERE company_food_truck_meal.truck_id = ? AND company_food_truck_meal.status = ?", [$val->id, 1]);

                $query[$key]->diet = DB::select("SELECT company_food_truck_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_food_truck_diet LEFT JOIN diet_category ON company_food_truck_diet.diet_id = diet_category.id WHERE company_food_truck_diet.truck_id = ? AND company_food_truck_diet.status = ?", [$val->id, 1]);

                $query[$key]->state = DB::select("SELECT company_food_truck_state.*,german_federal_states.state_name,german_federal_states.german FROM company_food_truck_state LEFT JOIN german_federal_states ON company_food_truck_state.state_id = german_federal_states.id WHERE company_food_truck_state.truck_id = ? AND company_food_truck_state.status = ?", [$val->id, 1]);

                $query[$key]->requirement = DB::select("SELECT company_food_truck_requirement.*,requirement_category.requirement,requirement_category.german FROM company_food_truck_requirement LEFT JOIN requirement_category ON company_food_truck_requirement.requirement_id = requirement_category.id WHERE company_food_truck_requirement.truck_id = ? AND company_food_truck_requirement.status = ?", [$val->id, 1]);

                $query[$key]->company_meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$val->company_id, 1]);

                $query[$key]->company_diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$val->company_id, 1]);

                $query[$key]->company_state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$val->company_id, 1]);
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(company_food_truck.id) AS count FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id LEFT JOIN company ON company_food_truck.company_id = company.id LEFT JOIN user_subscription ON user_subscription.user_id = company.user_id AND user_subscription.is_current = 1 WHERE company_food_truck.status = ? AND company.status = ? $filter", [1, 1])->count;
            return $this->response(1);
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function get_details(Request $r)
    {
        if ($this->varify_request($r, ["food_truck_id"])) {
            return $this->response();
        }

        $food_truck_id = $r->food_truck_id;

        $query = DB::selectOne("SELECT company_food_truck.*,truck_category.truck_category FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id WHERE company_food_truck.id = ? AND company_food_truck.status != ?", [$food_truck_id, 0]);

        if ($query) {

            $query->avg_rating = DB::selectOne("SELECT AVG(rating) AS avg_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->company_id, 1, 1])->avg_rating ?? 0;

            $query->total_rating = DB::selectOne("SELECT COUNT(id) AS total_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->company_id, 1, 1])->total_rating ?? 0;

            $query->food_truck_image = DB::select("SELECT * FROM company_food_truck_image WHERE truck_id = ? AND status = ?", [$query->id, 1]);

            $query->meal = DB::select("SELECT company_food_truck_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_food_truck_meal LEFT JOIN meal_category ON company_food_truck_meal.meal_id = meal_category.id WHERE company_food_truck_meal.truck_id = ? AND company_food_truck_meal.status = ?", [$query->id, 1]);

            $query->diet = DB::select("SELECT company_food_truck_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_food_truck_diet LEFT JOIN diet_category ON company_food_truck_diet.diet_id = diet_category.id WHERE company_food_truck_diet.truck_id = ? AND company_food_truck_diet.status = ?", [$query->id, 1]);

            $query->state = DB::select("SELECT company_food_truck_state.*,german_federal_states.state_name,german_federal_states.german FROM company_food_truck_state LEFT JOIN german_federal_states ON company_food_truck_state.state_id = german_federal_states.id WHERE company_food_truck_state.truck_id = ? AND company_food_truck_state.status = ?", [$query->id, 1]);

            $query->requirement = DB::select("SELECT company_food_truck_requirement.*,requirement_category.requirement,requirement_category.german FROM company_food_truck_requirement LEFT JOIN requirement_category ON company_food_truck_requirement.requirement_id = requirement_category.id WHERE company_food_truck_requirement.truck_id = ? AND company_food_truck_requirement.status = ?", [$query->id, 1]);

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "Invalid Id";
            return $this->response();
        }
    }
}
