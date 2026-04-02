<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    function add(Request $r)
    {
        if ($this->varify_request($r, ["user_id", "company_name", "city", "website_link"])) {
            return $this->response();
        }

        $user_id = $r->user_id;
        $company_name = $r->company_name;
        $city = $r->city;
        $postal_code = $r->postal_code ?? null;
        $website_link = $r->website_link;

        $query = DB::insert("INSERT INTO company (user_id,company_name,city,postal_code,website_link) VALUES (?,?,?,?,?)", [$user_id, $company_name, $city, $postal_code, $website_link]);

        $company_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM company WHERE id = ?", [$company_id]);
            return $this->response();
        } else {
            $this->response();
        }
    }

    function update(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE company SET";
        $perm = [];


        if ($r->has('company_name')) {

            $query .= " company_name = ?,";
            array_push($perm, $r->company_name);
        }

        if ($r->has('city')) {

            $query .= " city = ?,";
            array_push($perm, $r->city);
        }

        if ($r->has('postal_code')) {

            $query .= " postal_code = ?,";
            array_push($perm, $r->postal_code);
        }

        if ($r->has('website_link')) {

            $query .= " website_link = ?,";
            array_push($perm, $r->website_link);
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
        $this->r = DB::selectOne("SELECT * FROM company WHERE id = ?", [$id]);
        return $this->response();
    }

    function get_details(Request $r)
    {
        if ($this->varify_request($r, ["company_id"])) {
            return $this->response();
        }

        $company_id = $r->company_id;

        $query = DB::selectOne("SELECT * FROM company WHERE id = ?", [$company_id]);
        if ($query) {

            $query->user_details = DB::selectOne("SELECT user_details.*,countries.country_name FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id WHERE user_details.id = ?", [$query->user_id]);

            $query->avg_rating = DB::selectOne("SELECT AVG(rating) AS avg_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->id, 1, 1])->avg_rating ?? 0;

            $query->total_rating = DB::selectOne("SELECT COUNT(id) AS total_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->id, 1, 1])->total_rating ?? 0;


            $query->caterer = DB::selectOne("SELECT * FROM company_caterer WHERE company_id = ?", [$query->id]);

            $query->food_truck = DB::select("SELECT company_food_truck.*,truck_category.truck_category FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id WHERE company_food_truck.company_id = ? AND company_food_truck.status != ?", [$query->id, 0]);
            if ($query->food_truck) {
                foreach ($query->food_truck as $key => $val) {
                    $query->food_truck[$key]->food_truck_image = DB::select("SELECT * FROM company_food_truck_image WHERE truck_id = ? AND status = ?", [$val->id, 1]);

                    $query->food_truck[$key]->meal = DB::select("SELECT company_food_truck_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_food_truck_meal LEFT JOIN meal_category ON company_food_truck_meal.meal_id = meal_category.id WHERE company_food_truck_meal.truck_id = ? AND company_food_truck_meal.status = ?", [$val->id, 1]);

                    $query->food_truck[$key]->diet = DB::select("SELECT company_food_truck_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_food_truck_diet LEFT JOIN diet_category ON company_food_truck_diet.diet_id = diet_category.id WHERE company_food_truck_diet.truck_id = ? AND company_food_truck_diet.status = ?", [$val->id, 1]);

                    $query->food_truck[$key]->state = DB::select("SELECT company_food_truck_state.*,german_federal_states.state_name,german_federal_states.german FROM company_food_truck_state LEFT JOIN german_federal_states ON company_food_truck_state.state_id = german_federal_states.id WHERE company_food_truck_state.truck_id = ? AND company_food_truck_state.status = ?", [$val->id, 1]);

                    $query->food_truck[$key]->requirement = DB::select("SELECT company_food_truck_requirement.*,requirement_category.requirement,requirement_category.german FROM company_food_truck_requirement LEFT JOIN requirement_category ON company_food_truck_requirement.requirement_id = requirement_category.id WHERE company_food_truck_requirement.truck_id = ? AND company_food_truck_requirement.status = ?", [$val->id, 1]);

                    $query->food_truck[$key]->company_meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$val->company_id, 1]);

                    $query->food_truck[$key]->company_diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$val->company_id, 1]);

                    $query->food_truck[$key]->company_state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$val->company_id, 1]);
                }
            }

            $query->image = DB::select("SELECT * FROM company_image WHERE company_id = ? AND status = ?", [$query->id, 1]);

            $query->meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$query->id, 1]);

            $query->diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$query->id, 1]);

            $query->state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$query->id, 1]);

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "Inavalid Id";
            return $this->response();
        }
    }

    function global_listing(Request $r)
    {
        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);

        $caterer_filter = "";
        $food_truck_filter = "";
        if ($r->has("meal_ids")) {
            $caterer_filter .= " AND EXISTS (
                SELECT 1
                FROM company_meal
                WHERE company_meal.company_id = company_caterer.company_id
                AND company_meal.meal_id IN ($r->meal_ids) AND company_meal.status = 1
            )";

            $food_truck_filter .= " AND EXISTS (
                SELECT 1
                FROM company_food_truck_meal
                WHERE company_food_truck_meal.truck_id = company_food_truck.id
                AND company_food_truck_meal.meal_id IN ($r->meal_ids) AND company_food_truck_meal.status = 1
            )";
        }

        if ($r->has("diet_ids")) {

            $caterer_filter .= " AND EXISTS (
                SELECT 1
                FROM company_diet
                WHERE company_diet.company_id = company_caterer.company_id
                AND company_diet.diet_id IN ($r->diet_ids) AND company_diet.status = 1
            )";

            $food_truck_filter .= " AND EXISTS (
                SELECT 1
                FROM company_food_truck_diet
                WHERE company_food_truck_diet.truck_id = company_food_truck.id
                AND company_food_truck_diet.diet_id IN ($r->diet_ids) AND company_food_truck_diet.status = 1
            )";
        }

        if ($r->has("state_ids")) {
            $caterer_filter .= " AND EXISTS (
                SELECT 1
                FROM company_state
                WHERE company_state.company_id = company_caterer.company_id
                AND company_state.state_id IN ($r->state_ids) AND company_state.status = 1
            )";

            $food_truck_filter .= " AND EXISTS (
                SELECT 1
                FROM company_food_truck_state
                WHERE company_food_truck_state.truck_id = company_food_truck.id
                AND company_food_truck_state.state_id IN ($r->state_ids) AND company_food_truck_state.status = 1
            )";
        }

        if ($r->has("zip_code")) {
            $caterer_filter .= " AND company.postal_code = $r->zip_code";
            $food_truck_filter .= " AND company_food_truck.zip_code = $r->zip_code";
        }

        if ($r->has("caterer_type")) {
            $caterer_filter .= " AND company_caterer.caterer_type = $r->caterer_type";
        }

        if ($r->has("company_id")) {
            $caterer_filter .= " AND company_caterer.company_id = $r->company_id";
            $food_truck_filter .= " AND company_food_truck.company_id = $r->company_id";
        }

        if ($r->has("truck_cat_id")) {
            $food_truck_filter .= " AND company_food_truck.truck_cat_id = $r->truck_cat_id";
        }

        if ($r->has("is_premium") && $r->is_premium == 1) {
            $caterer_filter .= " AND user_subscription.truck_limit IN (3,-1)";
            $food_truck_filter .= " AND user_subscription.truck_limit IN (3,-1)";
        }

        if ($r->has("is_admin") && $r->is_admin == 1) {
            $caterer_order = "company_caterer.created_at DESC";
            $food_truck_order = "company_food_truck.created_at DESC";
        } else {
            $caterer_filter .= " AND user_subscription.end_time > CURRENT_TIMESTAMP()";
            $caterer_order = " company.is_default DESC,IFNULL(avg_rating, company_caterer.created_at) DESC";

            $food_truck_filter .= " AND user_subscription.end_time > CURRENT_TIMESTAMP()";
            $food_truck_order = " company.is_default DESC,IFNULL(avg_rating, company_food_truck.created_at) DESC";
        }

        $caterer_query = DB::select(
            "SELECT company_caterer.*,company.company_name,company.user_id,company.is_default,
        IFNULL((SELECT AVG(rating) AS avg_rating FROM rating WHERE company_id = company.id AND is_display = 1 AND status = 1), 0) AS avg_rating,
        IFNULL((SELECT COUNT(id) AS total_rating FROM rating WHERE company_id = company.id AND is_display = 1 AND status = 1), 0) AS total_rating
        FROM company_caterer 
        LEFT JOIN company ON company_caterer.company_id = company.id 
        LEFT JOIN user_subscription ON user_subscription.user_id = company.user_id AND user_subscription.is_current = 1 
        WHERE company_caterer.status = ? AND company.status = ? $caterer_filter 
        ORDER BY $caterer_order 
        LIMIT ?, ?",
            [1, 1, $count, $offset]
        );

        // Query for food trucks
        $food_truck_query = DB::select(
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
            company_food_truck.status = ? AND company.status = ? $food_truck_filter
        ORDER BY
            $food_truck_order LIMIT ?, ?",
            [1, 1, $count, $offset]
        );

        // Attach additional information to caterers
        if ($caterer_query) {
            foreach ($caterer_query as $key => $val) {
                $caterer_query[$key]->subscription = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$val->user_id, 1]);

                $caterer_query[$key]->image = DB::select("SELECT * FROM company_image WHERE company_id = ? AND status = ? AND type = ?", [$val->company_id, 1, 1]);

                $caterer_query[$key]->meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$val->company_id, 1]);

                $caterer_query[$key]->diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$val->company_id, 1]);

                $caterer_query[$key]->state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$val->company_id, 1]);
            }
        }

        // Attach additional information to food trucks
        if ($food_truck_query) {
            foreach ($food_truck_query as $key => $val) {
                $food_truck_query[$key]->subscription = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$val->user_id, 1]);

                $food_truck_query[$key]->food_truck_image = DB::select("SELECT * FROM company_food_truck_image WHERE truck_id = ? AND status = ?", [$val->id, 1]);
                $food_truck_query[$key]->meal = DB::select("SELECT company_food_truck_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_food_truck_meal LEFT JOIN meal_category ON company_food_truck_meal.meal_id = meal_category.id WHERE company_food_truck_meal.truck_id = ? AND company_food_truck_meal.status = ?", [$val->id, 1]);

                $food_truck_query[$key]->diet = DB::select("SELECT company_food_truck_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_food_truck_diet LEFT JOIN diet_category ON company_food_truck_diet.diet_id = diet_category.id WHERE company_food_truck_diet.truck_id = ? AND company_food_truck_diet.status = ?", [$val->id, 1]);

                $food_truck_query[$key]->state = DB::select("SELECT company_food_truck_state.*,german_federal_states.state_name,german_federal_states.german FROM company_food_truck_state LEFT JOIN german_federal_states ON company_food_truck_state.state_id = german_federal_states.id WHERE company_food_truck_state.truck_id = ? AND company_food_truck_state.status = ?", [$val->id, 1]);

                $food_truck_query[$key]->requirement = DB::select("SELECT company_food_truck_requirement.*,requirement_category.requirement,requirement_category.german FROM company_food_truck_requirement LEFT JOIN requirement_category ON company_food_truck_requirement.requirement_id = requirement_category.id WHERE company_food_truck_requirement.truck_id = ? AND company_food_truck_requirement.status = ?", [$val->id, 1]);

                $food_truck_query[$key]->company_meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$val->company_id, 1]);

                $food_truck_query[$key]->company_diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$val->company_id, 1]);

                $food_truck_query[$key]->company_state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$val->company_id, 1]);
            }
        }

        $combined = array_merge($caterer_query, $food_truck_query);
        usort($combined, function ($a, $b) {
            // First: company.is_default DESC
            if ($a->is_default != $b->is_default) {
                return $b->is_default - $a->is_default;
            }
            // Second: avg_rating DESC (if avg_rating is present)
            if ($a->avg_rating != $b->avg_rating) {
                return $b->avg_rating <=> $a->avg_rating;
            }
            // Third: created_at DESC
            return strtotime($b->created_at) - strtotime($a->created_at);
        });

        $this->s = 1;
        $this->m = "Success";
        $this->r = $combined;
        return $this->response(1);

        // // Build the response
        // $response = [
        //     "caterer" => $caterer_query ?: [],
        //     "food_truck" => $food_truck_query ?: []
        // ];

        // $this->s = 1;
        // $this->m = "Success";
        // $this->r = $response;
        // return $this->response();
    }
}
