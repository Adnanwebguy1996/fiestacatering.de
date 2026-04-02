<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class ContentController extends Controller
{

    function add_faq(Request $r)
    {

        if ($this->varify_request($r, ["type", "question", "answer", "german_question", "german_answer"])) {
            return $this->response();
        }

        $type = $r->input("type", 1);
        $question = $r->question;
        $answer = $r->answer;
        $german_question = $r->german_question;
        $german_answer = $r->german_answer;

        $query = DB::insert("INSERT INTO faq (type,question,answer,german_question,german_answer) VALUES (?,?,?,?,?)", [$type, $question, $answer, $german_question, $german_answer]);

        $faq_id = $this->getId();

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM faq WHERE id = ?", [$faq_id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function update_faq(Request $r)
    {
        if ($this->varify_request($r, ["id"])) {
            return $this->response();
        }

        $id = $r->id;

        $query = "UPDATE faq SET";
        $perm = [];


        if ($r->has('type')) {

            $query .= " type = ?,";
            array_push($perm, $r->type);
        }

        if ($r->has('question')) {

            $query .= " question = ?,";
            array_push($perm, $r->question);
        }

        if ($r->has('answer')) {

            $query .= " answer = ?,";
            array_push($perm, $r->answer);
        }

        if ($r->has('german_question')) {

            $query .= " german_question = ?,";
            array_push($perm, $r->german_question);
        }

        if ($r->has('german_answer')) {

            $query .= " german_answer = ?,";
            array_push($perm, $r->german_answer);
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
        $this->r = DB::selectOne("SELECT * FROM faq WHERE id = ? AND status = ?", [$id, 1]);
        return $this->response();
    }

    function get_faq(Request $r)
    {

        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);


        $filter = "";
        if ($r->has("type")) {
            $filter .= " AND type = $r->type";
        }

        $query = DB::select("SELECT * FROM faq WHERE status = ? $filter ORDER BY id ASC LIMIT ?,?", [1, $count, $offset]);

        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM faq WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function update_privacy_policy(Request $r)
    {

        if ($this->varify_request($r, ["content", "german"])) {
            return $this->response();;
        }

        $id = 1;
        $content = $r->content;
        $german = $r->german;

        DB::update("UPDATE privacy_policy SET content = ?,german = ? WHERE id = ?", [
            $content,
            $german,
            $id,
        ]);

        $this->s = 1;
        $this->m = "Success";
        $this->r = DB::selectOne("SELECT * FROM privacy_policy WHERE id = ?", [1]);
        return $this->response();
    }

    function update_terms_condition(Request $r)
    {

        if ($this->varify_request($r, ["content", "german"])) {
            return $this->response();
        }

        $id = 1;
        $content = $r->content;
        $german = $r->german;

        DB::update("UPDATE terms_condition SET content = ?,german = ? WHERE id = ?", [
            $content,
            $german,
            $id,
        ]);

        $this->s = 1;
        $this->m = "Success";
        $this->r = DB::selectOne("SELECT * FROM terms_condition WHERE id = ?", [1]);
        return $this->response();
    }

    function get_privacy_policy(Request $r)
    {
        $this->s = 1;
        $this->m = "Success";
        $this->r = DB::selectOne("SELECT * FROM privacy_policy WHERE id = ?", [1]);
        return $this->response();
    }

    function get_terms_condition(Request $r)
    {
        $this->s = 1;
        $this->m = "Success";
        $this->r = DB::selectOne("SELECT * FROM terms_condition WHERE id = ?", [1]);
        return $this->response();
    }

    function update_footer_content(Request $r)
    {


        $id = 1;

        $query = "UPDATE footer_content SET";
        $perm = [];


        if ($r->has('email')) {

            $query .= " email = ?,";
            array_push($perm, $r->email);
        }

        if ($r->has('phno')) {

            $query .= " phno = ?,";
            array_push($perm, $r->phno);
        }

        if ($r->has('address')) {

            $query .= " address = ?,";
            array_push($perm, $r->address);
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

        if ($r->has('tiktok')) {

            $query .= " tiktok = ?,";
            array_push($perm, $r->tiktok);
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
        $this->r = DB::selectOne("SELECT * FROM footer_content WHERE id = ?", [$id]);
        return $this->response();
    }

    function get_footer_content(Request $r)
    {
        $this->s = 1;
        $this->m = "Success";
        $this->r = DB::selectOne("SELECT * FROM footer_content WHERE id = ?", [1]);
        return $this->response();
    }

    function update_statistics(Request $r)
    {
        if ($this->varify_request($r, ["event", "caterer", "food", "status"])) {
            return $this->response();
        }

        $id = 1;
        $event = $r->event;
        $caterer = $r->caterer;
        $food = $r->food;
        $status = $r->status;

        DB::update("UPDATE statistics SET event = ?,caterer = ?,food = ?,status = ? WHERE id = ?", [
            $event,
            $caterer,
            $food,
            $status,
            $id,
        ]);

        $this->s = 1;
        $this->m = "Success";
        $this->r = DB::selectOne("SELECT * FROM statistics WHERE id = ?", [$id]);
        return $this->response();
    }

    function get_statistics(Request $r)
    {
        $this->s = 1;
        $this->m = "Success";
        $this->r = DB::selectOne("SELECT * FROM statistics WHERE id = ?", [1]);
        return $this->response();
    }

    function request_newsletter_subscribe(Request $r)
    {

        if ($this->varify_request($r, ["email", "type", "language"])) {
            return $this->response();
        }

        $email = $r->email;
        $type = $r->type;
        $language = $r->language;

        if ($this->subscribe_newsletter_mail($email, $type, $language)) {
            $this->s = 1;
            $this->m = "Newsletter subscribe mail has been sent to" . " " . $email;
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function request_newsletter_unsubscribe(Request $r)
    {

        if ($this->varify_request($r, ["email", "type", "language"])) {
            return $this->response();
        }

        $email = $r->email;
        $type = $r->type;
        $language = $r->language;

        if ($this->unsubscribe_newsletter_mail($email, $type, $language)) {
            $this->s = 1;
            $this->m = "Newsletter unsubscribe mail has been sent to" . " " . $email;
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function subscribe_newsletter(Request $r)
    {

        if ($this->varify_request($r, ["email", "type"])) {
            return $this->response();
        }

        $email = $r->email;
        $type = $r->type;

        $check = DB::selectOne("SELECT * FROM newsletter WHERE email = ? AND status = ? AND type = ?", [$email, 1, $type]);
        if ($check) {
            $this->s = 0;
            $this->m = "you are already subscribed to our newsletter";
            return $this->response();
        } else {

            $check = DB::selectOne("SELECT * FROM newsletter WHERE email = ? AND type = ?", [$email, $type]);
            if ($check) {
                DB::update("UPDATE newsletter SET status = ? WHERE email = ? AND type = ?", [1, $email, $type]);
                $this->s = 1;
                $this->m = "Success";
                $this->r = DB::selectOne("SELECT * FROM newsletter WHERE id = ?", [$check->id]);
                return $this->response();
            } else {

                $query = DB::insert("INSERT INTO newsletter (email,type) VALUES (?,?)", [$email, $type]);
                $id = $this->getId();
                if ($query) {
                    $this->s = 1;
                    $this->m = "Success";
                    $this->r = DB::selectOne("SELECT * FROM newsletter WHERE id = ?", [$id]);
                    return $this->response();
                } else {
                    return $this->response();
                }
            }
        }
    }

    function unsubscribe_newsletter(Request $r)
    {
        if ($this->varify_request($r, ["email"])) {
            return $this->response();
        }

        $email = $r->email;

        DB::update("UPDATE newsletter SET status = ? WHERE email = ?", [0, $email]);

        $this->s = 1;
        $this->m = "Newsletter unsubscribe successfully";
        return $this->response();
    }

    function subscribed_newsletter(Request $r)
    {
        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);


        $filter = "";

        if ($r->has("type")) {
            $filter .= " AND type = $r->type";
        }

        $query = DB::select(
            "SELECT * FROM newsletter WHERE status = ? $filter ORDER BY created_at DESC LIMIT ?,?",
            [1, $count, $offset]
        );

        if ($query) {

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM newsletter WHERE status = ? $filter", [1])->count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }
}
