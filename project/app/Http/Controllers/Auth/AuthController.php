<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{



    function countries(Request $r)
    {
        $query = DB::select("SELECT * FROM countries WHERE status = ?", [1]);

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM countries WHERE status = ?", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "No Records Found";
            return $this->response(1);
        }
    }

    function signup(Request $r)
    {
        if ($this->varify_request($r, ["country_id", "full_name", "email", "password"])) {
            return $this->response();
        }

        $country_id = $r->country_id;
        $full_name = $r->full_name;
        if ($r->hasFile('profile_img')) {
            $profile_img = $this->upload_file($r->profile_img, $this->profile_image());
        } else {
            $profile_img = null;
        }
        $dob = $r->dob ?? null;
        $email = $r->email;
        $phno_cc = $r->phno_cc ?? null;
        $phno = $r->phno ?? null;
        $password =  Hash::make($r->password);
        $role = 1;

        $language = $r->input("language", "DE");


        $check = DB::select("SELECT * FROM user_details WHERE email = ?", [$email]);
        if ($check) {
            $this->s = 0;
            $this->m = "Email already exist";
            return $this->response();
        }

        $query = DB::insert("INSERT INTO user_details (country_id,full_name,profile_img,dob,email,phno_cc,phno,password,role) VALUES (?,?,?,?,?,?,?,?,?)", [$country_id, $full_name, $profile_img, $dob, $email, $phno_cc, $phno, $password, $role]);

        $user_id = $this->getId();

        if ($query) {

            $apikey = sha1($email . uniqid());
            $token = sha1($apikey . $email . uniqid());

            DB::insert("INSERT INTO user_auth (user_id,apikey,token) VALUES (?,?,?)", [$user_id, $apikey, $token]);
            $this->welcome_mail($email, $language);
            $this->s = 1;
            $this->m = "Signup Succesfully";
            $this->r = DB::selectOne("SELECT user_details.*,countries.country_name,user_auth.apikey,user_auth.token FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id INNER JOIN user_auth ON user_details.id = user_auth.user_id WHERE user_details.id = ?", [$user_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function login(Request $r)
    {

        if ($this->varify_request($r, ['email', 'password'])) {
            return $this->response();
        }


        $email = $r->email;
        $password = $r->password;


        $query = DB::selectOne("SELECT * FROM user_details where email = ?  AND status = ?", [$email, 1]);
        if ($query) {

            $check = Hash::check($password, $query->password);
            if ($check) {

                $get_details =  DB::selectOne("SELECT user_details.*,countries.country_name,user_auth.apikey,user_auth.token FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id INNER JOIN user_auth ON user_details.id = user_auth.user_id WHERE user_details.id = ?", [$query->id]);

                if ($get_details) {
                    $get_details->password = null;
                    $this->s = 1;
                    $this->m = "Login Succesfully";
                    $this->r = $get_details;
                    return $this->response();
                } else {
                    return $this->response();
                }
            } else {
                $this->s = 0;
                $this->m = "Email Password Dosen't Match";
                return $this->response();
            }
        } else {
            $this->s = 0;
            $this->m = "You Are Not Authincated";
            return $this->response();
        }
    }

    function reset_password(Request $r)
    {
        if ($this->varify_request($r, ["email"])) {
            return $this->response();
        }

        $email = $r->email;

        $query = DB::selectOne("SELECT * FROM user_details WHERE email = ?  AND status = ?", [$email,  1]);
        if ($query) {

            $fp_token = sha1($query->id . uniqid());

            DB::update("UPDATE user_auth SET fp_token = ? WHERE user_id = ?", [$fp_token, $query->id]);

            if ($this->reset_password_mail($email, $fp_token)) {
                $this->s = 1;
                $this->m = "Reset Password link has been sent to" . " " . $email;
                return $this->response();
            } else {
                return $this->response();
            }
        } else {
            $this->s = 0;
            $this->m = "Invalid Email Or Invalid Role";
            return $this->response();
        }
    }

    function check_fp_token(Request $r)
    {

        if ($this->varify_request($r, ['fp_token'])) {
            return $this->response();
        }

        $fp_token = $r->fp_token;

        $query = DB::select("SELECT * FROM user_auth WHERE  fp_token = ? ", [$fp_token]);

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "Token Expired";
            return $this->response();
        }
    }

    function change_password(Request $r)
    {

        if ($this->varify_request($r, ['fp_token', 'password'])) {
            return $this->response();
        }

        $fp_token = $r->fp_token;
        $password =  $r->password;

        $query = DB::selectOne("SELECT * FROM user_auth WHERE fp_token = ? ", [$fp_token]);

        if ($query) {

            $p = Hash::make($password);
            DB::update("UPDATE user_details SET password = ? WHERE id = ?", [$p, $query->user_id]);
            DB::update("UPDATE user_auth SET fp_token = null WHERE user_id = ?", [$query->user_id]);
            $this->s = 1;
            $this->m = "Password Changed Succesfully";
            $this->r =  DB::selectOne("SELECT user_details.*,countries.country_name,user_auth.apikey,user_auth.token FROM user_details LEFT JOIN countries ON user_details.country_id = countries.id INNER JOIN user_auth ON user_details.id = user_auth.user_id WHERE user_details.id = ?", [$query->user_id]);
            return $this->response();
        } else {

            $this->s = 0;
            $this->m = "Token Expired";
            return $this->response();
        }
    }
}
