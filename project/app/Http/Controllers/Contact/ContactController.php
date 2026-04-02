<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class ContactController extends Controller
{

  function send(Request $r)
  {

    if ($this->varify_request($r, ["full_name", "email", "phno", "subject", "message"])) {
      return $this->response();
    }


    $full_name = $r->full_name;
    $email = $r->email;
    $phno = $r->phno;
    $subject = $r->subject;
    $message = $r->message;
    $user_id = $r->user_id ?? 0;

    $query = DB::insert(
      "INSERT INTO contact (user_id,full_name,email,phno,subject,message) VALUES (?,?,?,?,?,?)",
      [$user_id, $full_name, $email, $phno, $subject, $message]
    );

    $contact_id = $this->getId();

    if ($query) {
      $this->s = 1;
      $this->m = "Success";
      $this->r = DB::selectOne("SELECT * FROM contact WHERE id = ?", [
        $contact_id,
      ]);
      return $this->response();
    } else {
      return $this->response();
    }
  }

  function action(Request $r)
  {
    if ($this->varify_request($r, ["id"])) {
      return $this->response();
    }

    $id = $r->id;

    $query = "UPDATE contact SET";
    $perm = [];


    if ($r->has('is_followup')) {

      $query .= " is_followup = ?,";
      array_push($perm, $r->is_followup);
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
    $this->r = DB::selectOne("SELECT * FROM contact WHERE id = ? AND status = ?", [$id, 1]);
    return $this->response();
  }


  function get(Request $r)
  {

    $count = $r->input("count", 0);
    $offset = $r->input("offset", 30);

    $query = DB::select(
      "SELECT * FROM contact WHERE status = ? ORDER BY created_at DESC LIMIT ?,?",
      [1, $count, $offset]
    );

    if ($query) {
      $this->s = 1;
      $this->m = "Success";
      $this->r = $query;
      $this->c = DB::selectOne("SELECT COUNT(id) AS count FROM contact WHERE status = ?", [1])->count;
      return $this->response();
    } else {
      $this->s = 1;
      $this->m = "Success";
      return $this->response();
    }
  }
}
