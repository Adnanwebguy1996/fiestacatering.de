<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Authincate
{
    public function handle(Request $request, Closure $next)
    {
        $apikey = $request->hasHeader("apikey") ? $request->header("apikey") : $request->apikey;
        $token = $request->hasHeader("token") ? $request->header("token") : $request->token;

        if ($apikey == null) {
            return response()->json(["s" => 0, 'm' => 'apikey is not provided'], 401);
        }

        if ($token == null) {
            return response()->json(["s" => 0, 'm' => 'token is not provided'], 401);
        }

        $check = DB::selectOne("SELECT user_auth.user_id,user_details.status FROM `user_auth` INNER JOIN user_details ON user_details.id = user_auth.user_id WHERE user_auth.apikey = ? AND user_auth.token = ?", [$apikey, $token]);

        if (!$check) {
            return response()->json(["s" => 0, "m" => "You are not authenticated"], 401);
        } else if ($check->status == 0) {
            return response()->json(["s" => 0, "m" => "Account has been deleted"], 401);
        } else if ($check->status == -1) {
            return response()->json(["s" => 0, "m" => "Account has been deactivated by admin"], 401);
        } else {
            $request->merge(['_id' => $check->user_id]);
        }


        return $next($request);
    }
}
