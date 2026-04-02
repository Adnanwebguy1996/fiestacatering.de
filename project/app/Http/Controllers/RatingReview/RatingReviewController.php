<?php

namespace App\Http\Controllers\RatingReview;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class RatingReviewController extends Controller
{


    function add_update(Request $r)
    {


        if ($this->varify_request($r, ["user_id", "company_id", "rating", "review"])) {
            return $this->response();
        }


        $user_id = $r->user_id;
        $company_id = $r->company_id;
        $rating = $r->rating;
        $review = $r->review;

        $check = DB::selectOne("SELECT * FROM rating WHERE user_id = ? AND company_id = ? AND status = ?", [$user_id, $company_id, 1]);
        if ($check) {

            DB::update("UPDATE rating SET rating = ?,review = ?,is_display = ? WHERE id = ?", [$rating, $review, 0, $check->id]);
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM rating WHERE id = ?", [$check->id]);
            return $this->response();
        } else {
            $query = DB::insert("INSERT INTO rating (user_id,company_id,rating,review) VALUES (?,?,?,?)", [$user_id, $company_id, $rating, $review]);

            $rating_id = $this->getId();

            if ($query) {
                $this->s = 1;
                $this->m = "Success";
                $this->r = DB::selectOne("SELECT * FROM rating WHERE id = ?", [$rating_id]);
                return $this->response();
            } else {
                return $this->response();
            }
        }
    }

    function get_all(Request $r)
    {
        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);

        $filter = "";
        if ($r->has("company_id")) {
            $filter .= " AND rating.company_id = $r->company_id";
        }

        if ($r->has("user_id")) {
            $filter .= " AND rating.user_id = $r->user_id";
        }

        if ($r->has("is_display")) {
            $filter .= " AND rating.is_display = $r->is_display";
        }

        $query = DB::select("SELECT rating.*,company.company_name FROM rating LEFT JOIN company on company.id = rating.company_id WHERE rating.status = ? $filter ORDER BY created_at DESC LIMIT ?,?", [1, $count, $offset]);
        if ($query) {

            foreach ($query as $key => $val) {
                $query[$key]->user_details = DB::selectOne("SELECT user_details.id,user_details.country_id,countries.country_name,user_details.full_name,user_details.profile_img,user_details.role FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id WHERE user_details.id = ?", [$val->user_id]);
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM rating WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function action(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE rating SET";
        $perm = [];


        if ($r->has('is_display')) {

            $query .= " is_display = ?,";
            array_push($perm, $r->is_display);
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
        $this->r = DB::selectOne("SELECT * FROM rating WHERE id = ? AND status = ?", [$id, 1]);
        return $this->response();
    }
}
