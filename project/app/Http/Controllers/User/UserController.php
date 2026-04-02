<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function get_all(Request $r)
    {

        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);


        $filter = "";
        if ($r->has("search")) {
            $filter .= " AND user_details.full_name LIKE '%{$r->search}%' ";
        }

        if ($r->has("role")) {
            $filter .= " AND user_details.role = $r->role";
        }

        $query = DB::select("SELECT user_details.*,countries.country_name FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id WHERE user_details.status != ? AND user_details.role != ? $filter ORDER BY created_at DESC LIMIT ?,?", [0, 3,  $count, $offset]);

        if ($query) {

            foreach ($query as $key => $val) {
                $query[$key]->password = null;
                $query[$key]->subscription = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$val->id, 1]);
            }

            $count = DB::selectOne("SELECT COUNT(user_details.id) AS count FROM user_details WHERE user_details.status != ? AND user_details.role != ? $filter", [0, 3])->count;

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = $count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "No Records Found";
            return $this->response(1);
        }
    }

    function get_by_id(Request $r)
    {

        if ($this->varify_request($r, ["user_id"])) {
            return $this->response();
        }

        $user_id = $r->user_id;

        $query = DB::selectOne("SELECT user_details.*,countries.country_name,user_auth.apikey,user_auth.token FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id INNER JOIN user_auth ON user_details.id = user_auth.user_id WHERE user_details.id = ?", [$user_id]);

        if ($query) {
            $query->password = null;
            $query->company_details = DB::selectOne("SELECT * FROM company WHERE user_id = ?", [$user_id]);

            if ($query->company_details) {

                $query->company_details->avg_rating = DB::selectOne("SELECT AVG(rating) AS avg_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->id, 1, 1])->avg_rating ?? 0;

                $query->company_details->total_rating = DB::selectOne("SELECT COUNT(id) AS total_rating FROM rating WHERE company_id = ? AND is_display = ? AND status = ? ", [$query->id, 1, 1])->total_rating ?? 0;

                $query->company_details->caterer = DB::selectOne("SELECT * FROM company_caterer WHERE company_id = ?", [$query->company_details->id]);

                $query->company_details->food_truck = DB::select("SELECT company_food_truck.*,truck_category.truck_category FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id WHERE company_food_truck.company_id = ? AND company_food_truck.status != ?", [$query->company_details->id, 0]);
                if ($query->company_details->food_truck) {
                    foreach ($query->company_details->food_truck as $key => $val) {
                        $query->company_details->food_truck[$key]->food_truck_image = DB::select("SELECT * FROM company_food_truck_image WHERE truck_id = ? AND status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->meal = DB::select("SELECT company_food_truck_meal.*,meal_category.meal_name,meal_category.image FROM company_food_truck_meal LEFT JOIN meal_category ON company_food_truck_meal.meal_id = meal_category.id WHERE company_food_truck_meal.truck_id = ? AND company_food_truck_meal.status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->diet = DB::select("SELECT company_food_truck_diet.*,diet_category.diet_name,diet_category.image FROM company_food_truck_diet LEFT JOIN diet_category ON company_food_truck_diet.diet_id = diet_category.id WHERE company_food_truck_diet.truck_id = ? AND company_food_truck_diet.status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->requirement = DB::select("SELECT company_food_truck_requirement.*,requirement_category.requirement,requirement_category.german FROM company_food_truck_requirement LEFT JOIN requirement_category ON company_food_truck_requirement.requirement_id = requirement_category.id WHERE company_food_truck_requirement.truck_id = ? AND company_food_truck_requirement.status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->state = DB::select("SELECT company_food_truck_state.*,german_federal_states.state_name,german_federal_states.german FROM company_food_truck_state LEFT JOIN german_federal_states ON company_food_truck_state.state_id = german_federal_states.id WHERE company_food_truck_state.truck_id = ? AND company_food_truck_state.status = ?", [$val->id, 1]);
                    }
                }

                $query->company_details->meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$query->company_details->id, 1]);

                $query->company_details->diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$query->company_details->id, 1]);

                $query->company_details->image = DB::select("SELECT * FROM company_image WHERE company_id = ? AND status = ?", [$query->company_details->id, 1]);

                $query->company_details->state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$query->company_details->id, 1]);


                $query->company_details->subscription = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$user_id, 1]);
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "No Records Found";
            return $this->response();
        }
    }

    function get_light_details(Request $r)
    {
        if ($this->varify_request($r, ["user_id"])) {
            return $this->response();
        }

        $user_id = $r->user_id;

        $query = DB::selectOne("SELECT id,full_name,profile_img,role,status FROM user_details WHERE id = ?", [$user_id]);

        if ($query) {
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

    function update(Request $r)
    {

        if ($this->varify_request($r, ["user_id"])) {
            return $this->response();
        }

        $user_id = $r->user_id;

        $query = "UPDATE user_details SET";
        $perm = [];


        if ($r->has('country_id')) {

            $query .= " country_id = ?,";
            array_push($perm, $r->country_id);
        }

        if ($r->has('full_name')) {

            $query .= " full_name = ?,";
            array_push($perm, $r->full_name);
        }

        if ($r->hasFile('profile_img')) {

            $profile_img = $this->upload_file($r->profile_img, $this->profile_image());

            $query .= " profile_img = ?,";
            array_push($perm, $profile_img);
        }

        if ($r->has('dob')) {

            $query .= " dob = ?,";
            array_push($perm, $r->dob);
        }

        if ($r->has('phno_cc')) {

            $query .= " phno_cc = ?,";
            array_push($perm, $r->phno_cc);
        }

        if ($r->has('phno')) {

            $query .= " phno = ?,";
            array_push($perm, $r->phno);
        }


        if (count($perm) == 0) {
            $this->s = 0;
            $this->m = "Please Provide Atleast One Field To Update";
            return $this->response();
        }



        $query = substr($query, 0, strlen($query) - 1);
        $query .= " where id = ?";
        array_push($perm, $user_id);
        $res = DB::update($query, $perm);

        $this->s = 1;
        $this->m = "Update Successfully";
        $this->r = DB::selectOne("SELECT user_details.*,countries.country_name,user_auth.apikey,user_auth.token FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id INNER JOIN user_auth ON user_details.id = user_auth.user_id WHERE user_details.id = ?", [$user_id]);
        return $this->response();
    }

    function change_password(Request $r)
    {

        if ($this->varify_request($r, ["user_id", "old_password", "new_password"])) {
            return $this->response();
        }

        $user_id = $r->user_id;
        $old_password = $r->old_password;
        $new_password =  Hash::make($r->new_password);

        $query = DB::selectOne("SELECT * FROM user_details WHERE id = ? AND status != ?", [$user_id, 0]);

        if ($query) {

            $check = Hash::check($old_password, $query->password);
            if ($check) {
                DB::update("UPDATE user_details SET password = ? WHERE id = ?", [$new_password, $user_id]);
                $this->s = 1;
                $this->m = "Password Changed Successfully";
                return $this->response();
            } else {
                $this->s = 0;
                $this->m = "Old Password Doesn't Match";
                return $this->response();
            }
        } else {
            $this->s = 0;
            $this->m = "Invalid User";
            return $this->response();
        }
    }

    function account_status(Request $r)
    {

        if ($this->varify_request($r, ["user_id", "status"])) {
            return $this->response();
        }

        $user_id = $r->user_id;
        $status = $r->status;


        $query = DB::selectOne("SELECT * FROM user_details WHERE id = ? AND status != ?", [$user_id, 0]);

        if ($query) {
            if ($status == 0) {
                $email = $query->email . $query->id;
                $temp_email = $query->email;

                DB::update("UPDATE user_details SET status = ?,temp_email = ?,email = ? WHERE id = ?", [$status, $temp_email, $email, $query->id]);
            } else {
                DB::update("UPDATE user_details SET status = ? WHERE id = ? ", [$status, $user_id]);
            }

            DB::update("UPDATE company SET status = ? WHERE user_id = ?", [$status, $user_id]);

            $this->s = 1;
            $this->m = "Account Status Changed Succesfully";
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Deleted account status can't be changed";
            return $this->response();
        }
    }
}
