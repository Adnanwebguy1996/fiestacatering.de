<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    function add(Request $r)
    {
        if ($this->varify_request($r, ["company_id", "type"], ["image"])) {
            return $this->response();
        }

        $company_id = $r->company_id;
        $type = $r->type; // 1:Company Image 2:Certificate
        $name = $r->input("name", null);
        $image = $this->upload_file($r->image, $this->company_image());

        $query = DB::insert("INSERT INTO company_image (company_id,type,name,image) VALUES (?,?,?,?)", [$company_id, $type, $name, $image]);

        $image_id = $this->getId();

        $user_id = DB::selectOne("SELECT * FROM company WHERE id = ?", [$company_id]);
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM company_image WHERE id = ?", [$image_id]);
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

        $query = "UPDATE company_image SET";
        $perm = [];


        if ($r->has('type')) {

            $query .= " type = ?,";
            array_push($perm, $r->type);
        }

        if ($r->has('name')) {

            $query .= " name = ?,";
            array_push($perm, $r->name);
        }

        if ($r->hasFile('image')) {

            $image = $this->upload_file($r->image, $this->company_image());

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
}
