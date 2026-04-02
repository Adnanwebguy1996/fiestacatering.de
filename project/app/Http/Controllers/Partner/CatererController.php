<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CatererController extends Controller
{
    function add(Request $r)
    {
        if ($this->varify_request($r, ["company_id", "caterer_type", "about"])) {
            return $this->response();
        }

        $company_id = $r->company_id;
        $caterer_type = $r->caterer_type; // 1:Business 2:Food Truck 3:Barista/Bartender 4:Other	
        $about = $r->about;


        $facebook = $r->input('facebook', null);
        $twitter = $r->input('twitter', null);
        $insta = $r->input('insta', null);
        $whatsapp = $r->input('whatsapp', null);
        $linkedin = $r->input('linkedin', null);


        $query = DB::insert("INSERT INTO company_caterer (company_id,caterer_type,about,facebook,twitter,insta,whatsapp,linkedin) VALUES (?,?,?,?,?,?,?,?)", [$company_id, $caterer_type, $about, $facebook, $twitter, $insta, $whatsapp, $linkedin]);


        $caterer_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM company_caterer WHERE id = ?", [$caterer_id]);
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

        $query = "UPDATE company_caterer SET";
        $perm = [];


        if ($r->has('caterer_type')) {

            $query .= " caterer_type = ?,";
            array_push($perm, $r->caterer_type);
        }

        if ($r->has('about')) {

            $query .= " about = ?,";
            array_push($perm, $r->about);
        }

        if ($r->has('facebook')) {

            $query .= " facebook = ?,";
            array_push($perm, $r->facebook);
        }

        if ($r->has('twitter')) {

            $query .= " twitter = ?,";
            array_push($perm, $r->twitter);
        }

        if ($r->has('insta')) {

            $query .= " insta = ?,";
            array_push($perm, $r->insta);
        }

        if ($r->has('whatsapp')) {

            $query .= " whatsapp = ?,";
            array_push($perm, $r->whatsapp);
        }

        if ($r->has('linkedin')) {

            $query .= " linkedin = ?,";
            array_push($perm, $r->linkedin);
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
        $this->r = DB::selectOne("SELECT * FROM company_caterer WHERE id = ?", [$id]);
        return $this->response();
    }

    function get_all(Request $r)
    {
        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);

        $filter = "";
        if ($r->has("meal_ids")) {
            // $filter .= " AND company_caterer.company_id IN (SELECT DISTINCT company_id FROM company_meal WHERE meal_id IN ($r->meal_ids))";

            $filter .= " AND EXISTS (
                SELECT 1
                FROM company_meal
                WHERE company_meal.company_id = company_caterer.company_id
                AND company_meal.meal_id IN ($r->meal_ids) AND company_meal.status = 1
            )";
        }

        if ($r->has("diet_ids")) {
            // $filter .= " AND company_caterer.company_id IN (SELECT DISTINCT company_id FROM company_diet WHERE diet_id IN ($r->diet_ids)";

            $filter .= " AND EXISTS (
                SELECT 1
                FROM company_diet
                WHERE company_diet.company_id = company_caterer.company_id
                AND company_diet.diet_id IN ($r->diet_ids) AND company_diet.status = 1
            )";
        }

        if ($r->has("state_ids")) {
            // $filter .= " AND company_caterer.company_id IN (SELECT DISTINCT company_id FROM company_state WHERE state_id IN ($r->state_ids))";

            $filter .= " AND EXISTS (
                SELECT 1
                FROM company_state
                WHERE company_state.company_id = company_caterer.company_id
                AND company_state.state_id IN ($r->state_ids) AND company_state.status = 1
            )";
        }

        if ($r->has("zip_code")) {
            $filter .= " AND company.postal_code = $r->zip_code";
        }

        if ($r->has("caterer_type")) {
            $filter .= " AND company_caterer.caterer_type = $r->caterer_type";
        }

        if ($r->has("company_id")) {
            $filter .= " AND company_caterer.company_id = $r->company_id";
        }

        if ($r->has("search")) {
            $filter .= " AND company.company_name LIKE '%$r->search%' ";
        }

        if ($r->has("is_premium") && $r->is_premium == 1) {
            $filter .= " AND user_subscription.truck_limit IN (3,-1)";
        }

        if ($r->has("is_admin") && $r->is_admin == 1) {
            $order = "company_caterer.created_at DESC";
        } else {
            $filter .= " AND user_subscription.end_time > CURRENT_TIMESTAMP()";
            $order = " company.is_default DESC,IFNULL(avg_rating, company_caterer.created_at) DESC";
        }

        $query = DB::select(
            "SELECT company_caterer.*,company.company_name,company.user_id,company.is_default,
            IFNULL((SELECT AVG(rating) AS avg_rating FROM rating WHERE company_id = company.id AND is_display = 1 AND status = 1), 0) AS avg_rating,
            IFNULL((SELECT COUNT(id) AS total_rating FROM rating WHERE company_id = company.id AND is_display = 1 AND status = 1), 0) AS total_rating
            FROM company_caterer 
            LEFT JOIN company ON company_caterer.company_id = company.id 
            LEFT JOIN user_subscription ON user_subscription.user_id = company.user_id AND user_subscription.is_current = 1 
            WHERE company_caterer.status = ? AND company.status = ? $filter 
            ORDER BY $order 
            LIMIT ?, ?",
            [1, 1, $count, $offset]
        );

        if ($query) {

            foreach ($query as $key => $val) {

                $query[$key]->subscription = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$val->user_id, 1]);

                $query[$key]->image = DB::select("SELECT * FROM company_image WHERE company_id = ? AND status = ? AND type = ?", [$val->company_id, 1, 1]);

                $query[$key]->meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$val->company_id, 1]);

                $query[$key]->diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$val->company_id, 1]);

                $query[$key]->state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$val->company_id, 1]);
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(company_caterer.id) AS count FROM company_caterer LEFT JOIN company ON company_caterer.company_id = company.id LEFT JOIN user_subscription ON user_subscription.user_id = company.user_id AND user_subscription.is_current = 1 WHERE company_caterer.status = ? AND company.status = ? $filter", [1, 1,])->count;
            return $this->response(1);
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function get_details(Request $r)
    {
        if ($this->varify_request($r, ["caterer_id"])) {
            return $this->response();
        }

        $caterer_id = $r->caterer_id;

        $query = DB::selectOne("SELECT company_caterer.*,company.company_name FROM company_caterer LEFT JOIN company ON company_caterer.company_id = company.id WHERE company_caterer.status = ? AND company_caterer.id = ?", [1, $caterer_id]);
        if ($query) {

            $query->avg_rating = DB::selectOne("SELECT AVG(rating) AS avg_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->company_id, 1, 1])->avg_rating ?? 0;

            $query->total_rating = DB::selectOne("SELECT COUNT(id) AS total_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->company_id, 1, 1])->total_rating ?? 0;


            $query->image = DB::select("SELECT * FROM company_image WHERE company_id = ? AND status = ? AND type = ?", [$query->company_id, 1, 1]);


            $query->meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$query->company_id, 1]);

            $query->diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$query->company_id, 1]);

            $query->state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$query->company_id, 1]);

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
