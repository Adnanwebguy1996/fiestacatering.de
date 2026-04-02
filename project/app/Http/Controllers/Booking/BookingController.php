<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    function check_availability(Request $r)
    {
        if ($this->varify_request($r, ["company_id", "from_date", "to_date"])) {
            return $this->response();
        }

        $company_id = $r->company_id;
        $from_date = $r->from_date;
        $to_date = $r->to_date;

        $check = DB::selectOne("SELECT * FROM booking WHERE company_id = ? AND ((from_date BETWEEN ? AND ?) OR (to_date BETWEEN ? AND ?) OR (from_date <= ? AND to_date >= ?)) AND booking_status = ?", [$company_id, $from_date, $to_date, $from_date, $to_date, $from_date, $to_date, 1]);

        if ($check) {
            $this->s = 0;
            $this->m = "Booking found during these dates";
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Booking available for these dates";
            return $this->response();
        }
    }

    function book(Request $r)
    {
        if ($this->varify_request($r, ["user_id", "full_name", "email", "phno", "company_id", "city_name", "address", "meal_ids", "from_date", "to_date", "no_of_person", "budget_per_person", "total_budget"])) {
            return $this->response();
        }

        $user_id = $r->user_id;
        $full_name = $r->full_name;
        $email = $r->email;
        $phno = $r->phno;
        $company_id = $r->company_id;
        $truck_id = $r->input("truck_id", 0);
        $city_name = $r->city_name;
        $zip_code = $r->zip_code ?? null;
        $address = $r->address;
        $meal_ids = $r->meal_ids;
        $diet_ids = $r->diet_ids ?? null;
        $state_ids = $r->state_ids ?? null;
        $from_date = $r->from_date;
        $to_date = $r->to_date;
        $no_of_person = $r->no_of_person;
        $budget_per_person = $r->budget_per_person;
        $total_budget = $r->total_budget;
        $notes = $r->notes ?? null;

        $company_details = DB::selectOne("SELECT * FROM company WHERE id = ?", [$company_id]);

        $company_owner = DB::selectOne("SELECT * FROM user_details WHERE id = ?", [$company_details->user_id]);

        $query = DB::insert("INSERT INTO booking (user_id,full_name,email,phno,company_id,truck_id,city_name,zip_code,address,meal_ids,diet_ids,state_ids,from_date,to_date,no_of_person,budget_per_person,total_budget,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$user_id, $full_name, $email, $phno, $company_id, $truck_id, $city_name, $zip_code, $address, $meal_ids, $diet_ids, $state_ids, $from_date, $to_date, $no_of_person, $budget_per_person, $total_budget, $notes]);

        $booking_id = $this->getId();

        if ($query) {

            $this->booking_mail($company_owner->email, $full_name, $from_date, $to_date, $no_of_person, $budget_per_person, $total_budget);

            $this->s = 1;
            $this->m = "Booking successful";
            $this->r = DB::selectOne("SELECT * FROM booking WHERE id = ?", [$booking_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function get_all(Request $r)
    {
        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);


        $filter = "";
        if ($r->has("company_id")) {
            $filter .= " AND company_id = $r->company_id";
        }

        if ($r->has("user_id")) {
            $filter .= " AND user_id = $r->user_id";
        }

        if ($r->has("booking_status")) {
            $filter .= " AND booking_status = $r->booking_status";
        }

        $query = DB::select("SELECT * FROM booking WHERE status = ? $filter ORDER BY created_at DESC LIMIT ?,?", [1, $count, $offset]);

        if ($query) {

            foreach ($query as $key => $val) {

                $query[$key]->user_details = DB::selectOne("SELECT user_details.id,user_details.country_id,countries.country_name,user_details.full_name,user_details.profile_img,user_details.dob,user_details.phno_cc,user_details.phno,user_details.role FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id WHERE user_details.id = ?", [$val->user_id]);

                $query[$key]->booked_meal = DB::select("SELECT * FROM meal_category WHERE id IN ($val->meal_ids)");

                $query[$key]->booked_diet = DB::select("SELECT * FROM diet_category WHERE id IN (" . (empty($val->diet_ids) ? '0' : $val->diet_ids) . ")");

                $query[$key]->booked_state = DB::select("SELECT * FROM german_federal_states WHERE id IN (" . (empty($val->state_ids) ? '0' : $val->state_ids) . ")");

                $query[$key]->company_details = DB::selectOne("SELECT * FROM company WHERE id = ?", [$val->company_id]);

                if ($query[$key]->company_details) {

                    $query[$key]->company_details->user_details = DB::selectOne("SELECT user_details.id,user_details.country_id,countries.country_name,user_details.full_name,user_details.profile_img,user_details.dob,user_details.phno_cc,user_details.phno,user_details.role FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id WHERE user_details.id = ?", [$query[$key]->company_details->user_id]);

                    $query[$key]->company_details->caterer = DB::selectOne("SELECT * FROM company_caterer WHERE company_id = ?", [$query[$key]->company_details->id]);

                    $query[$key]->company_details->food_truck = DB::select("SELECT company_food_truck.*,truck_category.truck_category FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id WHERE company_food_truck.company_id = ?", [$query[$key]->company_details->id]);

                    if ($query[$key]->company_details->food_truck) {

                        foreach ($query[$key]->company_details->food_truck as $k => $v) {
                            $query[$key]->company_details->food_truck[$k]->food_truck_image = DB::select("SELECT * FROM company_food_truck_image WHERE truck_id = ? AND status = ?", [$v->id, 1]);

                            $query[$key]->company_details->food_truck[$k]->food_truck_meal = DB::select("SELECT company_food_truck_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_food_truck_meal LEFT JOIN meal_category ON company_food_truck_meal.meal_id = meal_category.id WHERE company_food_truck_meal.truck_id = ? AND company_food_truck_meal.status = ?", [$v->id, 1]);

                            $query[$key]->company_details->food_truck[$k]->food_truck_diet = DB::select("SELECT company_food_truck_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_food_truck_diet LEFT JOIN diet_category ON company_food_truck_diet.diet_id = diet_category.id WHERE company_food_truck_diet.truck_id = ? AND company_food_truck_diet.status = ?", [$v->id, 1]);

                            $query[$key]->company_details->food_truck[$k]->food_truck_state = DB::select("SELECT company_food_truck_state.*,german_federal_states.state_name,german_federal_states.german FROM company_food_truck_state LEFT JOIN german_federal_states ON company_food_truck_state.state_id = german_federal_states.id WHERE company_food_truck_state.truck_id = ? AND company_food_truck_state.status = ?", [$v->id, 1]);

                            $query[$key]->company_details->food_truck[$k]->food_truck_requirement = DB::select("SELECT company_food_truck_requirement.*,requirement_category.requirement,requirement_category.german FROM company_food_truck_requirement LEFT JOIN requirement_category ON company_food_truck_requirement.requirement_id = requirement_category.id WHERE company_food_truck_requirement.truck_id = ? AND company_food_truck_requirement.status = ?", [$v->id, 1]);
                        }
                    }

                    $query[$key]->company_details->image = DB::select("SELECT * FROM company_image WHERE company_id = ? AND status = ?", [$query[$key]->company_details->id, 1]);

                    $query[$key]->company_details->meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$query[$key]->company_details->id, 1]);

                    $query[$key]->company_details->diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$query[$key]->company_details->id, 1]);

                    $query[$key]->company_details->state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$query[$key]->company_details->id, 1]);
                }
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM booking WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function get_details(Request $r)
    {
        if ($this->varify_request($r, ["booking_id"])) {
            return $this->response();
        }

        $booking_id = $r->booking_id;

        $query = DB::selectOne("SELECT * FROM booking WHERE id = ? AND status = ?", [$booking_id, 1]);

        if ($query) {

            $query->user_details = DB::selectOne("SELECT user_details.id,user_details.country_id,countries.country_name,user_details.full_name,user_details.profile_img,user_details.dob,user_details.phno_cc,user_details.phno,user_details.role FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id WHERE user_details.id = ?", [$query->user_id]);

            $query->booked_meal = DB::select("SELECT * FROM meal_category WHERE id IN ($query->meal_ids)");

            $query->booked_diet = DB::select("SELECT * FROM diet_category WHERE id IN (" . (empty($query->diet_ids) ? '0' : $query->diet_ids) . ")");

            $query->booked_state = DB::select("SELECT * FROM german_federal_states WHERE id IN (" . (empty($query->state_ids) ? '0' : $query->state_ids) . ")");

            $query->company_details = DB::selectOne("SELECT * FROM company WHERE id = ?", [$query->id]);
            if ($query->company_details) {
                $query->company_details->caterer = DB::selectOne("SELECT * FROM company_caterer WHERE company_id = ?", [$query->company_details->id]);

                $query->company_details->food_truck = DB::select("SELECT company_food_truck.*,truck_category.truck_category FROM company_food_truck LEFT JOIN truck_category ON company_food_truck.truck_cat_id = truck_category.id WHERE company_food_truck.company_id = ?", [$query->company_details->id]);
                if ($query->company_details->food_truck) {
                    foreach ($query->company_details->food_truck as $key => $val) {
                        $query->company_details->food_truck[$key]->food_truck_image = DB::select("SELECT * FROM company_food_truck_image WHERE truck_id = ? AND status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->food_truck_meal = DB::select("SELECT company_food_truck_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_food_truck_meal LEFT JOIN meal_category ON company_food_truck_meal.meal_id = meal_category.id WHERE company_food_truck_meal.truck_id = ? AND company_food_truck_meal.status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->food_truck_diet = DB::select("SELECT company_food_truck_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_food_truck_diet LEFT JOIN diet_category ON company_food_truck_diet.diet_id = diet_category.id WHERE company_food_truck_diet.truck_id = ? AND company_food_truck_diet.status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->food_truck_state = DB::select("SELECT company_food_truck_state.*,german_federal_states.state_name,german_federal_states.german FROM company_food_truck_state LEFT JOIN german_federal_states ON company_food_truck_state.state_id = german_federal_states.id WHERE company_food_truck_state.truck_id = ? AND company_food_truck_state.status = ?", [$val->id, 1]);

                        $query->company_details->food_truck[$key]->food_truck_requirement = DB::select("SELECT company_food_truck_requirement.*,requirement_category.requirement,requirement_category.german FROM company_food_truck_requirement LEFT JOIN requirement_category ON company_food_truck_requirement.requirement_id = requirement_category.id WHERE company_food_truck_requirement.truck_id = ? AND company_food_truck_requirement.status = ?", [$val->id, 1]);
                    }
                }

                $query->company_details->image = DB::select("SELECT * FROM company_image WHERE company_id = ? AND status = ?", [$query->company_details->id, 1]);

                $query->company_details->meal = DB::select("SELECT company_meal.*,meal_category.meal_name,meal_category.german,meal_category.image FROM company_meal LEFT JOIN meal_category ON company_meal.meal_id = meal_category.id WHERE company_meal.company_id = ? AND company_meal.status = ?", [$query->company_details->id, 1]);

                $query->company_details->diet = DB::select("SELECT company_diet.*,diet_category.diet_name,diet_category.german,diet_category.image FROM company_diet LEFT JOIN diet_category ON company_diet.diet_id = diet_category.id WHERE company_diet.company_id = ? AND company_diet.status = ?", [$query->company_details->id, 1]);

                $query->company_details->state = DB::select("SELECT company_state.*,german_federal_states.state_name,german_federal_states.german FROM company_state LEFT JOIN german_federal_states ON company_state.state_id = german_federal_states.id WHERE company_state.company_id = ? AND company_state.status = ?", [$query->company_details->id, 1]);
            }

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

    function cancel(Request $r)
    {
        if ($this->varify_request($r, ["booking_id"])) {
            return $this->response();
        }

        $booking_id = $r->booking_id;
        $action_by = $r->_id;

        $booking_details = DB::selectOne("SELECT * FROM booking WHERE id = ?", [$booking_id]);
        $company_details = DB::selectOne("SELECT * FROM company WHERE id = ?", [$booking_details->company_id]);
        $company_owner = DB::selectOne("SELECT * FROM user_details WHERE id = ?", [$company_details->user_id]);
        $user_details = DB::selectOne("SELECT * FROM user_details WHERE id = ?", [$booking_details->user_id]);

        DB::update("UPDATE booking SET booking_status = ?,action_by = ? WHERE id = ? ", [-1, $action_by, $booking_id]);

        if ($action_by == $company_owner->id) {
            $this->booking_cancle_user_mail($user_details->email);
        }

        if ($action_by == $user_details->id) {
            $this->booking_cancle_company_mail($company_owner->email);
        }

        $this->s = 1;
        $this->m = "Booking status update succesfully";
        return $this->response();
    }

    function approve(Request $r)
    {
        if ($this->varify_request($r, ["booking_id"])) {
            return $this->response();
        }

        $booking_id = $r->booking_id;
        $action_by = $r->_id;


        $booking_details = DB::selectOne("SELECT * FROM booking WHERE id = ?", [$booking_id]);

        $user_details = DB::selectOne("SELECT * FROM user_details WHERE id = ?", [$booking_details->user_id]);

        DB::update("UPDATE booking SET booking_status = ?,action_by = ? WHERE id = ? ", [1, $action_by, $booking_id]);

        $this->booking_approve_mail($user_details->email);

        $this->s = 1;
        $this->m = "Booking status update succesfully";
        return $this->response();
    }

    function booking_analytics(Request $r)
    {


        if ($this->varify_request($r, ["company_id"])) {
            return $this->response();
        }

        $company_id = $r->company_id;

        $total_booking = DB::selectOne("SELECT COUNT(id) AS total_booking FROM booking WHERE company_id = ? AND status = ?", [$company_id, 1])->total_booking;

        $pending_booking = DB::selectOne("SELECT COUNT(id) AS pending_booking FROM booking WHERE company_id = ? AND status = ? AND booking_status = ?", [$company_id, 1, 0])->pending_booking;

        $canclled_booking = DB::selectOne("SELECT COUNT(id) AS canclled_booking FROM booking WHERE company_id = ? AND status = ? AND booking_status = ?", [$company_id, 1, -1])->canclled_booking;

        $approved_booking = DB::selectOne("SELECT COUNT(id) AS approved_booking FROM booking WHERE company_id = ? AND status = ? AND booking_status = ?", [$company_id, 1, 1])->approved_booking;

        $this->s = 1;
        $this->m = "Success";
        $this->r = ["total_booking" => $total_booking, "pending_booking" => $pending_booking, "canclled_booking" => $canclled_booking, "approved_booking" => $approved_booking];
        return $this->response();
    }
}
