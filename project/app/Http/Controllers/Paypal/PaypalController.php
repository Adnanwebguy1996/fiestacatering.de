<?php

namespace App\Http\Controllers\Paypal;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PaypalController extends Controller
{

    function plan_list(Request $r)
    {

        $query = DB::select("SELECT * FROM subscription WHERE status = ?", [1]);
        if ($query) {
            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function subscribe_plan(Request $r)
    {

        if ($this->varify_request($r, ["user_id", "plan_id"])) {
            return $this->response();
        }


        $user_id = $r->user_id;
        $plan_id = $r->plan_id;

        $query = DB::selectOne("SELECT * FROM user_details WHERE id = ? AND status = ?", [$user_id, 1]);
        if ($query) {


            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND status = ?", [$user_id, "ACTIVE"]);

            if ($check) {
                $this->s = 0;
                $this->m = "You have already active subscription";
                return $this->response();
            }


            $plan_details = DB::selectOne("SELECT * FROM subscription WHERE plan_id = ? ", [$plan_id]);

            if ($plan_details->is_trial == 1) {
                $this->s = 0;
                $this->m = "Invalid Plan";
                return $this->response();
            }

            DB::update("UPDATE user_details SET role = ? WHERE id = ?", [2, $user_id]);

            $access_token = $this->get_paypal_access_token();
            $data = [
                'plan_id' => $plan_id,
                'subscriber' => [
                    'name' => [
                        'given_name' => $query->full_name,
                    ],
                    'email_address' => $query->email,
                ],
                'application_context' => [
                    'brand_name' => 'Fiesta Catering',
                    'locale' => 'en-US',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'payment_method' => [
                        'payer_selected' => 'PAYPAL',
                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                    ],
                    'return_url' => 'https://fiestacatering.de/mycompany/?type=1',
                    'cancel_url' => 'https://fiestacatering.de/mycompany/?type=1',
                ],
            ];

            $coupon_code = null;
            $discounted_price = 0;
            if ($r->has('coupon_code')) {

                $check_used_discount = DB::selectOne("SELECT * FROM used_discount WHERE BINARY coupon_code = ? AND user_id = ?", [$coupon_code, $user_id]);
                if ($check_used_discount) {
                    $this->s = 0;
                    $this->m = "You have already used this coupon code";
                    return $this->response();
                } else {
                    $current_date = Carbon::now();
                    $coupon_code = $r->coupon_code;
                    $discount = DB::selectOne("SELECT * FROM discounts WHERE BINARY coupon_code = ? AND start_date <= '$current_date' AND end_date >= '$current_date' AND status = 1", [$coupon_code]);

                    if ($discount) {
                        // Check subscription type compatibility
                        if (($discount->subscription_type == $plan_details->id) || ($discount->subscription_type == -1)) {
                            // Check usage limits
                            if ($discount->total_usage_limit == -1 || $discount->used_usage_count < $discount->total_usage_limit) {

                                // Apply discount based on type
                                if ($discount->discount_type == 1) { // Percentage
                                    $discounted_price = floor(($plan_details->price - ($plan_details->price * ($discount->discount_amount / 100))) * 100) / 100;
                                } elseif ($discount->discount_type == 2) { // Direct amount
                                    $discounted_price = floor(($plan_details->price - $discount->discount_amount) * 100) / 100;
                                }


                                $data['plan'] = [
                                    'billing_cycles' => [
                                        [
                                            'sequence' => 1,
                                            'total_cycles' => 0,
                                            'pricing_scheme' => [
                                                'fixed_price' => [
                                                    'value' => $discounted_price,
                                                    'currency_code' => 'EUR',
                                                ],
                                            ],
                                        ],
                                    ],
                                ];
                            } else {
                                $this->s = 0;
                                $this->m = "The coupon code has reached its maximum usage limit";
                                return $this->response();
                            }
                        } else {
                            $this->s = 0;
                            $this->m = "Discount code is not applicable for this subscription type";
                            return $this->response();
                        }
                    } else {
                        $this->s = 0;
                        $this->m = "Coupon code is invalid or expired";
                        return $this->response();
                    }
                }
            }

            $data = json_encode($data);

            $paypal_url = config('paypal.url');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt_array($curl, [
                CURLOPT_URL => $paypal_url . '/v1/billing/subscriptions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Prefer: return=representation',
                ],
            ]);

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result);

            if ($response->id != null) {
                $transaction = DB::insert(
                    "INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)",
                    [
                        $user_id,
                        1,
                        $response->plan_id,
                        $response->id,
                        $response->status,
                    ]
                );

                $transaction_id = $this->getId();

                $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$user_id, 1]);

                if ($check) {
                    DB::update("UPDATE user_subscription SET is_current = ? WHERE id = ?", [0, $check->id]);
                }

                DB::insert("INSERT INTO user_subscription (user_id,type,transaction_id,subscription_id,plan_id,start_time,end_time,name,german_name,month,price,description,german_description,truck_limit,status,coupon_code,discounted_price) VALUES (?,?,?,?,?,STR_TO_DATE(?, '%Y-%m-%dT%H:%i:%sZ'),?,?,?,?,?,?,?,?,?,?,?)", [$user_id, 1, $transaction_id, $response->id, $plan_details->plan_id, $response->start_time, null, $plan_details->name, $plan_details->german_name, $plan_details->month, $plan_details->price, $plan_details->description, $plan_details->german_description, $plan_details->truck_limit, $response->status, $coupon_code, $discounted_price]);

                $this->s = 1;
                $this->m = "Success";
                $this->r = $response;
                return $this->response();
            } else {

                $this->s = 0;
                $this->m = "Invalid Request";
                return $this->response();
            }
        } else {
            return $this->response();
        }
    }

    function subscribe_plan_free(Request $r)
    {
        // this function is used to subscribe any plan if 100% discount is applied
        if ($this->varify_request($r, ["user_id", "type", "plan_id", "coupon_code"])) {
            return $this->response();
        }

        $user_id = $r->user_id;
        $plan_id = $r->plan_id;
        $coupon_code = $r->coupon_code;
        $type = $r->type; // 0:Custom Trial | 1:Subscription | 2:Order Subscription

        $plan_details = DB::selectOne("SELECT * FROM subscription WHERE plan_id = ? ", [$plan_id]);

        if ($plan_details->is_trial != 0) {
            $this->s = 0;
            $this->m = "Please select valid trial plan";
            return $this->response();
        }

        $start_time = Carbon::now();
        $end_time = Carbon::now()->addMonth($plan_details->month);

        $query = DB::selectOne("SELECT * FROM user_details WHERE id = ? AND status = ?", [$user_id, 1]);
        if ($query) {

            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND status = ?", [$user_id, "ACTIVE"]);

            if ($check) {
                $this->s = 0;
                $this->m = "You have already active subscription";
                return $this->response();
            }

            $check_used_discount = DB::selectOne("SELECT * FROM used_discount WHERE BINARY coupon_code = ? AND user_id = ?", [$coupon_code, $user_id]);
            if ($check_used_discount) {
                $this->s = 0;
                $this->m = "You have already used this coupon code";
                return $this->response();
            } else {

                $discount = DB::selectOne("SELECT * FROM discounts WHERE BINARY coupon_code = ? AND start_date <= '$start_time' AND end_date >= '$start_time' AND status = 1", [$coupon_code]);

                if ($discount) {
                    // Check subscription type compatibility
                    if (($discount->subscription_type == $plan_details->id) || ($discount->subscription_type == -1)) {

                        // Check usage limits
                        if ($discount->total_usage_limit == -1 || $discount->used_usage_count < $discount->total_usage_limit) {

                            // Apply discount based on type
                            if ($discount->discount_type == 1) { // Percentage
                                $discounted_price = floor(($plan_details->price - ($plan_details->price * ($discount->discount_amount / 100))) * 100) / 100;
                            } elseif ($discount->discount_type == 2) { // Direct amount
                                $discounted_price = floor(($plan_details->price - $discount->discount_amount) * 100) / 100;
                            }

                            if ($discounted_price != 0) {
                                $this->s = 0;
                                $this->m = "Discounted price is not zero, please check the discount code";
                                return $this->response();
                            }

                            DB::update("UPDATE user_details SET role = ? WHERE id = ?", [2, $user_id]);

                            $subscription_id = "D" . "-" . $user_id . rand();

                            $transaction = DB::insert(
                                "INSERT INTO transaction (user_id,type,plan_id,subscription_id,status,free_by_coupon) VALUES (?,?,?,?,?,?)",
                                [
                                    $user_id,
                                    $type,
                                    $plan_id,
                                    $subscription_id,
                                    "ACTIVE",
                                    1
                                ]
                            );

                            $transaction_id = $this->getId();

                            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$user_id, 1]);

                            if ($check) {
                                DB::update("UPDATE user_subscription SET is_current = ? WHERE id = ?", [0, $check->id]);
                            }

                            DB::insert("INSERT INTO user_subscription (user_id,type,transaction_id,subscription_id,plan_id,start_time,end_time,name,german_name,month,price,description,german_description,truck_limit,status,coupon_code,discounted_price,free_by_coupon) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$user_id, $type, $transaction_id, $subscription_id, $plan_details->plan_id, $start_time, $end_time, $plan_details->name, $plan_details->german_name, $plan_details->month, $plan_details->price, $plan_details->description, $plan_details->german_description, $plan_details->truck_limit, 'ACTIVE', $coupon_code, $discounted_price, 1]);

                            $id = $this->getId();

                            DB::update("UPDATE discounts SET used_usage_count = used_usage_count + 1 WHERE BINARY coupon_code = ?", [$coupon_code]);

                            DB::insert("INSERT INTO used_discount (user_id,coupon_code) VALUES (?,?)", [$user_id, $coupon_code]);


                            $this->s = 1;
                            $this->m = "Success";
                            $this->r = DB::selectOne("SELECT * FROM user_subscription WHERE id = ?", [$id]);
                            return $this->response();
                        } else {
                            $this->s = 0;
                            $this->m = "The coupon code has reached its maximum usage limit";
                            return $this->response();
                        }
                    } else {
                        $this->s = 0;
                        $this->m = "Discount code is not applicable for this subscription type";
                        return $this->response();
                    }
                } else {
                    $this->s = 0;
                    $this->m = "Coupon code is invalid or expired";
                    return $this->response();
                }
            }
        } else {
            $this->s = 0;
            $this->m = "User not found or inactive";
            return $this->response();
        }
    }

    function subscribe_trial_plan(Request $r)
    {
        // this function is used to subscribe a 1 month free trial plan
        if ($this->varify_request($r, ["user_id", "plan_id"])) {
            return $this->response();
        }


        $user_id = $r->user_id;
        $plan_id = $r->plan_id;


        $plan_details = DB::selectOne("SELECT * FROM subscription WHERE plan_id = ? ", [$plan_id]);

        if ($plan_details->is_trial == 0) {
            $this->s = 0;
            $this->m = "Please select valid trial plan";
            return $this->response();
        }


        $start_time = Carbon::now();
        $end_time = Carbon::now()->addMonth(3);
        // $trial_end = "2025-01-31 23:59:00";

        // if ($start_time > $trial_end) {
        //     $this->s = 0;
        //     $this->m = "Trial plan validity has been expired";
        //     return $this->response();
        // }

        $query = DB::selectOne("SELECT * FROM user_details WHERE id = ? AND status = ?", [$user_id, 1]);
        if ($query) {

            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND status = ?", [$user_id, "ACTIVE"]);

            if ($check) {
                $this->s = 0;
                $this->m = "You have already active subscription";
                return $this->response();
            }

            DB::update("UPDATE user_details SET role = ? WHERE id = ?", [2, $user_id]);


            $subscription_id = "F" . "-" . $user_id . rand();

            $transaction = DB::insert(
                "INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)",
                [
                    $user_id,
                    0,
                    $plan_id,
                    $subscription_id,
                    "ACTIVE",
                ]
            );

            $transaction_id = $this->getId();

            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$user_id, 1]);

            if ($check) {
                DB::update("UPDATE user_subscription SET is_current = ? WHERE id = ?", [0, $check->id]);
            }


            DB::insert("INSERT INTO user_subscription (user_id,type,transaction_id,subscription_id,plan_id,start_time,end_time,name,german_name,month,price,description,german_description,truck_limit,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$user_id, 0, $transaction_id, $subscription_id, $plan_details->plan_id, $start_time, $end_time, $plan_details->name, $plan_details->german_name, $plan_details->month, $plan_details->price, $plan_details->description, $plan_details->german_description, $plan_details->truck_limit, "ACTIVE"]);


            $id = $this->getId();


            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM user_subscription WHERE id = ?", [$id]);
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "User not found or inactive";
            return $this->response();
        }
    }


    function extend_trial_plan(Request $r)
    {
        if ($this->varify_request($r, ["user_id"])) {
            return $this->response();
        }


        $user_id = $r->user_id;

        $plan_details = DB::selectOne("SELECT * FROM subscription WHERE id = ? ", [7]);

        $query = DB::selectOne("SELECT * FROM user_details WHERE id = ? AND status = ?", [$user_id, 1]);
        if ($query) {

            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND status = ?", [$user_id, "ACTIVE"]);

            if ($check) {
                $this->s = 0;
                $this->m = "already active subscription";
                return $this->response();
            }


            $subscription_id = "F" . "-" . $user_id . rand();

            $transaction = DB::insert(
                "INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)",
                [
                    $user_id,
                    0,
                    $plan_details->plan_id,
                    $subscription_id,
                    "ACTIVE",
                ]
            );

            $transaction_id = $this->getId();

            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$user_id, 1]);

            if ($check) {
                DB::update("UPDATE user_subscription SET is_current = ? WHERE id = ?", [0, $check->id]);
            }

            $start_time = Carbon::now();
            $end_time = Carbon::now()->addMonth();

            DB::insert("INSERT INTO user_subscription (user_id,type,transaction_id,subscription_id,plan_id,start_time,end_time,name,german_name,month,price,description,german_description,truck_limit,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$user_id, 0, $transaction_id, $subscription_id, $plan_details->plan_id, $start_time, $end_time, $plan_details->name, $plan_details->german_name, $plan_details->month, $plan_details->price, $plan_details->description, $plan_details->german_description, $plan_details->truck_limit, "ACTIVE"]);


            $id = $this->getId();

            $this->subscription_extend_mail($query->email);
            $this->s = 1;
            $this->m = "Success";
            $this->r = DB::selectOne("SELECT * FROM user_subscription WHERE id = ?", [$id]);
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function create_order(Request $r)
    {
        if ($this->varify_request($r, ["user_id", "plan_id"])) {
            return $this->response();
        }


        $user_id = $r->user_id;
        $plan_id = $r->plan_id;


        $query = DB::selectOne("SELECT * FROM user_details WHERE id = ? AND status = ?", [$user_id, 1]);
        if ($query) {

            $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND status = ?", [$user_id, "ACTIVE"]);

            if ($check) {
                $this->s = 0;
                $this->m = "You have already active subscription";
                return $this->response();
            }

            DB::update("UPDATE user_details SET role = ? WHERE id = ?", [2, $user_id]);
            $plan_details = DB::selectOne("SELECT * FROM subscription WHERE plan_id = ? ", [$plan_id]);

            if ($plan_details->is_trial == 1) {
                $this->s = 0;
                $this->m = "Invalid Plan";
                return $this->response();
            }


            $access_token = $this->get_paypal_access_token();
            $data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => $plan_details->price,
                        ],
                    ],
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            'brand_name' => 'Fiesta Catering',
                            'user_action' => 'PAY_NOW',
                            'return_url' => "https://fiestacatering.de/mycompany/?type=2",
                            'cancel_url' => "https://fiestacatering.de/mycompany/?type=2",
                        ],
                    ],
                ],
            ];

            $coupon_code = null;
            $discounted_price = 0;
            if ($r->has('coupon_code')) {


                $check_used_discount = DB::selectOne("SELECT * FROM used_discount WHERE BINARY coupon_code = ? AND user_id = ?", [$coupon_code, $user_id]);
                if ($check_used_discount) {
                    $this->s = 0;
                    $this->m = "You have already used this coupon code";
                    return $this->response();
                } else {
                    $current_date = Carbon::now();
                    $coupon_code = $r->coupon_code;
                    $discount = DB::selectOne("SELECT * FROM discounts WHERE BINARY coupon_code = ? AND start_date <= '$current_date' AND end_date >= '$current_date' AND status = 1", [$coupon_code]);

                    if ($discount) {
                        // Check subscription type compatibility
                        if (($discount->subscription_type == $plan_details->id) || ($discount->subscription_type == -1)) {
                            // Check usage limits
                            if ($discount->total_usage_limit == -1 || $discount->used_usage_count < $discount->total_usage_limit) {

                                // Apply discount based on type
                                if ($discount->discount_type == 1) { // Percentage
                                    $discounted_price = floor(($plan_details->price - ($plan_details->price * ($discount->discount_amount / 100))) * 100) / 100;
                                } elseif ($discount->discount_type == 2) { // Direct amount
                                    $discounted_price = floor(($plan_details->price - $discount->discount_amount) * 100) / 100;
                                }

                                // Update the purchase_units with the discounted price
                                $data['purchase_units'][0]['amount']['value'] = $discounted_price;
                            } else {
                                $this->s = 0;
                                $this->m = "The coupon code has reached its maximum usage limit.";
                                return $this->response();
                            }
                        } else {
                            $this->s = 0;
                            $this->m = "Discount code is not applicable for this subscription type";
                            return $this->response();
                        }
                    } else {
                        $this->s = 0;
                        $this->m = "Coupon code is invalid or expired";
                        return $this->response();
                    }
                }
            }


            $data = json_encode($data);

            $paypal_url = config('paypal.url');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt_array($curl, [
                CURLOPT_URL => $paypal_url . '/v2/checkout/orders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Prefer: return=representation',
                ],
            ]);

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result);

            if ($response->id != null) {

                if ($response->status == "PAYER_ACTION_REQUIRED") {
                    $response->status = "APPROVAL_PENDING";
                }

                $transaction = DB::insert(
                    "INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)",
                    [
                        $user_id,
                        2,
                        $plan_id,
                        $response->id,
                        $response->status,
                    ]
                );

                $transaction_id = $this->getId();

                $check = DB::selectOne("SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?", [$user_id, 1]);

                if ($check) {
                    DB::update("UPDATE user_subscription SET is_current = ? WHERE id = ?", [0, $check->id]);
                }


                $start_time = Carbon::now();
                // $end_time = Carbon::now()->addMonth($plan_details->month);

                DB::insert("INSERT INTO user_subscription (user_id,type,transaction_id,subscription_id,plan_id,start_time,end_time,name,german_name,month,price,description,german_description,truck_limit,status,coupon_code,discounted_price) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$user_id, 2, $transaction_id, $response->id, $plan_details->plan_id, $start_time, null, $plan_details->name, $plan_details->german_name, $plan_details->month, $plan_details->price, $plan_details->description, $plan_details->german_description, $plan_details->truck_limit, $response->status, $coupon_code, $discounted_price]);

                $this->s = 1;
                $this->m = "Success";
                $this->r = $response;
                return $this->response();
            } else {

                $this->s = 0;
                $this->m = "Invalid Request";
                return $this->response();
            }
        } else {
            return $this->response();
        }
    }

    function cancel_subscription(Request $r)
    {
        if ($this->varify_request($r, ["subscription_id"])) {
            return $this->response();
        }

        $subscription_id = $r->subscription_id;
        $user_subscription = DB::selectOne("SELECT * FROM user_subscription WHERE subscription_id = ?", [$subscription_id]);

        if ($user_subscription->status != "ACTIVE") {
            $this->s = 0;
            $this->m = " only active subscriptions can be canceled. If your subscription is already canceled, expired, or suspended, you will not be able to cancel it again.";
            return $this->response();
        } else if ($user_subscription->type == 0) {
            $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_subscription->user_id, 0, $user_subscription->plan_id, $user_subscription->subscription_id, "CANCELLED"]);

            $transaction_id = $this->getId();

            DB::update(
                "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ? ",
                [$transaction_id, "CANCELLED", $user_subscription->user_id, 1]
            );

            $this->s = 1;
            $this->m = "Success";
            return $this->response();
        } else if ($user_subscription->type == 1) {
            $access_token = $this->get_paypal_access_token();

            $paypal_url = config('paypal.url');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $paypal_url . '/v1/billing/subscriptions/$subscription_id/cancel',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                                        "reason": "Not satisfied with the service"
                                }',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Prefer: return=representation',
                ],
            ));

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result);


            // $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_subscription->user_id, 1,$user_subscription->plan_id,$user_subscription->subscription_id, "CANCELLED"]);

            // $transaction_id = $this->getId();

            // DB::update(
            //     "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? ",
            //     [$transaction_id, "CANCELLED", $user_subscription->user_id]
            // );

            $this->s = 1;
            $this->m = "Success";
            return $this->response();
        } else if ($user_subscription->type == 2) {
            $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_subscription->user_id, 2, $user_subscription->plan_id, $user_subscription->subscription_id, "CANCELLED"]);

            $transaction_id = $this->getId();

            DB::update(
                "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ? ",
                [$transaction_id, "CANCELLED", $user_subscription->user_id, 1]
            );

            $this->s = 1;
            $this->m = "Success";
            return $this->response();
        } else {
            return $this->response();
        }
    }

    function verify_subscription(Request $r)
    {

        if ($this->varify_request($r, ["subscription_id"])) {
            return $this->response();
        }

        $subscription_id = $r->subscription_id;

        $user_subscription = DB::selectOne("SELECT * FROM user_subscription WHERE subscription_id = ?", [$subscription_id]);
        if ($user_subscription && $user_subscription->type == 1) {
            $user_id = $user_subscription->user_id;
            $access_token = $this->get_paypal_access_token();

            $paypal_url = config('paypal.url');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $paypal_url . '/v1/billing/subscriptions/' . $subscription_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Prefer: return=representation',
                ],
            ));

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result);

            if (!isset($response->id) || $response->id === null) {
                $this->s = 0;
                $this->m = "The resource ID does not exist. Please subscribe to the plan again.";
                return $this->response();
            }

            $check = DB::selectOne(
                "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                [$user_id, 1]
            );

            if ($check) {
                if ($response->status === "ACTIVE") {

                    if (abs(strtotime($check->end_time) - strtotime($response->billing_info->next_billing_time)) <= 5) {
                        $this->s = 1;
                        $this->m = "Success";
                        $this->r = DB::selectOne(
                            "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                            [$user_id, 1]
                        );
                        return $this->response();
                    } else {

                        if ($user_subscription->coupon_code != null) {
                            $used_discount = DB::selectOne("SELECT * FROM used_discount WHERE user_id = ? AND BINARY coupon_code = ?", [$user_id, $user_subscription->coupon_code]);
                            if (!$used_discount) {

                                DB::update("UPDATE discounts SET used_usage_count = used_usage_count + 1 WHERE BINARY coupon_code = ?", [$user_subscription->coupon_code]);

                                DB::insert("INSERT INTO used_discount (user_id,coupon_code) VALUES (?,?)", [$user_id, $user_subscription->coupon_code]);
                            }
                        }

                        $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 1, $response->plan_id, $response->id, $response->status]);

                        $transaction_id = $this->getId();

                        DB::update(
                            "UPDATE user_subscription SET transaction_id = ?, subscription_id = ?,plan_id = ?,start_time = STR_TO_DATE(?, '%Y-%m-%dT%H:%i:%sZ'),end_time = STR_TO_DATE(?, '%Y-%m-%dT%H:%i:%sZ'),status = ? WHERE user_id = ? AND is_current = ?",
                            [
                                $transaction_id,
                                $response->id,
                                $response->plan_id,
                                $response->start_time,
                                $response->billing_info->next_billing_time,
                                $response->status,
                                $user_id,
                                1
                            ]
                        );

                        $this->s = 1;
                        $this->m = "Success";
                        $this->r = DB::selectOne(
                            "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                            [$user_id, 1]
                        );
                        return $this->response();
                    }
                } else if ($response->status === "EXPIRED") {

                    $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 1, $response->plan_id, $response->id, $response->status]);

                    $transaction_id = $this->getId();

                    DB::update(
                        "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ?",
                        [$transaction_id, $response->status, $user_id, 1]
                    );

                    $this->s = 1;
                    $this->m = "Success";
                    $this->r = DB::selectOne(
                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                        [$user_id, 1]
                    );
                    return $this->response();
                } else if ($response->status === "CANCELLED") {
                    $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 1, $response->plan_id, $response->id, $response->status]);

                    $transaction_id = $this->getId();

                    DB::update(
                        "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ?",
                        [$transaction_id, $response->status, $user_id, 1]
                    );

                    $this->s = 1;
                    $this->m = "Success";
                    $this->r = DB::selectOne(
                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                        [$user_id, 1]
                    );
                    return $this->response();
                } else if ($response->status === "SUSPENDED") {
                    $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 1, $response->plan_id, $response->id, $response->status]);

                    $transaction_id = $this->getId();

                    DB::update(
                        "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ?",
                        [$transaction_id, $response->status, $user_id, 1]
                    );

                    $this->s = 1;
                    $this->m = "Success";
                    $this->r = DB::selectOne(
                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                        [$user_id, 1]
                    );
                    return $this->response();
                } else {
                    $this->s = 1;
                    $this->m = "Sucees";
                    $this->r = $response;
                    return $this->response();
                }
            } else {
                $this->s = 0;
                $this->m = "Subscription Not Found";
                return $this->response();
            }
        } else if ($user_subscription && $user_subscription->type == 2) {
            $user_id = $user_subscription->user_id;
            $access_token = $this->get_paypal_access_token();

            $paypal_url = config('paypal.url');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $paypal_url . '/v2/checkout/orders/' . $subscription_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Prefer: return=representation',
                ],
            ));

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result);

            if (!isset($response->id) || $response->id === null) {
                $this->s = 0;
                $this->m = "The resource ID does not exist. Please subscribe to the plan again.";
                return $this->response();
            }

            if ($response->status == "PAYER_ACTION_REQUIRED") {
                $response->status = "APPROVAL_PENDING";
            } else if ($response->status == "APPROVED") {
                $response->status = "ACTIVE";
            } else if ($response->status ==  "COMPLETED") {
                $response->status = "ACTIVE";
            }

            if ($response->status == "ACTIVE") {
                $paypal_url = config('paypal.url');
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $paypal_url . '/v2/checkout/orders/' . $subscription_id . '/capture',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Prefer: return=representation',
                    ],
                ));

                $result = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($result);


                if (isset($response->id) && $response->id !== null) {
                    if ($response->status == "PAYER_ACTION_REQUIRED") {
                        $response->status = "APPROVAL_PENDING";
                    } else if ($response->status == "APPROVED") {
                        $response->status = "ACTIVE";
                    } else if ($response->status ==  "COMPLETED") {
                        $response->status = "ACTIVE";
                    }

                    $check = DB::selectOne(
                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                        [$user_id, 1]
                    );

                    if ($check) {

                        if ($check->status == $response->status) {
                            $this->s = 1;
                            $this->m = "Success";
                            $this->r = DB::selectOne(
                                "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                                [$user_id, 1]
                            );
                            return $this->response();
                        } else {

                            if ($user_subscription->coupon_code != null) {
                                $used_discount = DB::selectOne("SELECT * FROM used_discount WHERE user_id = ? AND BINARY coupon_code = ?", [$user_id, $user_subscription->coupon_code]);
                                if (!$used_discount) {

                                    DB::update("UPDATE discounts SET used_usage_count = used_usage_count + 1 WHERE BINARY coupon_code = ?", [$user_subscription->coupon_code]);

                                    DB::insert("INSERT INTO used_discount (user_id,coupon_code) VALUES (?,?)", [$user_id, $user_subscription->coupon_code]);
                                }
                            }

                            $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 2, $user_subscription->plan_id, $response->id, $response->status]);
                            $transaction_id = $this->getId();

                            $plan_details = DB::selectOne("SELECT * FROM subscription WHERE plan_id = ? ", [$user_subscription->plan_id]);


                            $start_time = Carbon::parse($user_subscription->start_time);

                            $end_time = $start_time->addMonth($plan_details->month);

                            DB::update(
                                "UPDATE user_subscription SET transaction_id = ?,end_time = ?,status = ? WHERE user_id = ? AND is_current = ?",
                                [$transaction_id, $end_time, $response->status, $user_id, 1]
                            );

                            $this->s = 1;
                            $this->m = "Success";
                            $this->r = DB::selectOne(
                                "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                                [$user_id, 1]
                            );
                            return $this->response();
                        }
                    }
                } else {
                    $this->s = 1;
                    $this->m = "Success";
                    $this->r = DB::selectOne(
                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                        [$user_id, 1]
                    );
                    return $this->response();
                }
            } else {
                $this->s = 1;
                $this->m = "Success";
                $this->r = $response;
                return $this->response();
            }
        } else {
            $this->s = 0;
            $this->m = "Invalid request";
            return $this->response();
        }
    }

    function transaction(Request $r)
    {

        $count = $r->input("count", 0);
        $offset = $r->input("offset", 30);


        $filter = "";
        if ($r->has("user_id")) {
            $filter .= " AND user_id = $r->user_id ";
        }

        $query = DB::select("SELECT * FROM transaction WHERE is_delete = ? $filter ORDER BY created_at DESC LIMIT ?,?", [0, $count, $offset]);

        if ($query) {

            $count = DB::selectOne("SELECT COUNT(id) AS count FROM transaction WHERE is_delete = ? $filter", [0])->count;

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            $this->c = $count;
            return $this->response();
        } else {
            $this->s = 1;
            $this->m = "Success";
            return $this->response(1);
        }
    }

    function handle_webhook(Request $r)
    {

        try {
            // Log entry into the webhook handler
            DB::insert("INSERT INTO webhook (data) VALUES ('Entered into webhook handler')");

            // Get raw JSON content from the request
            $raw_content = $r->getContent();

            // Log the raw content for debugging
            DB::insert("INSERT INTO webhook (data) VALUES (?)", [$raw_content]);

            // Decode the JSON content
            $data = json_decode($raw_content, true); // Decode as associative array

            $event = $data['event_type'] ?? null;

            if ($event != null) {

                DB::insert("INSERT INTO webhook (data) VALUES (?)", [$data['event_type']]);

                // Initialize subscription_id variable
                $subscription_id = null;

                // Check event type and extract subscription ID accordingly
                switch ($event) {
                    // Payment-related events
                    case 'PAYMENT.SALE.COMPLETED':
                    case 'PAYMENT.SALE.DENIED':
                    case 'PAYMENT.SALE.PENDING':
                    case 'PAYMENT.SALE.REFUNDED':
                    case 'PAYMENT.SALE.REVERSED':
                        // For payment-related events, get from resource -> billing_agreement_id
                        $subscription_id = $data['resource']['billing_agreement_id'] ?? null;
                        break;

                    // Billing-related events
                    case 'BILLING.SUBSCRIPTION.CREATED':
                    case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    case 'BILLING.SUBSCRIPTION.CANCELLED':
                    case 'BILLING.SUBSCRIPTION.EXPIRED':
                    case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                    case 'BILLING.SUBSCRIPTION.REACTIVATED':
                    case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    case 'BILLING.SUBSCRIPTION.UPDATED':
                        // For billing-related events, get from resource -> id
                        $subscription_id = $data['resource']['id'] ?? null;
                        break;

                    default:
                        DB::insert("INSERT INTO webhook (data) VALUES ('Unhandled event type: $event')");
                        return response()->json(['status' => 'ignored'], 200);
                }

                if ($subscription_id != null) {
                    $user_subscription = DB::selectOne("SELECT * FROM user_subscription WHERE subscription_id = ?", [$subscription_id]);

                    if ($user_subscription) {
                        $user_id = $user_subscription->user_id;
                        $access_token = $this->get_paypal_access_token();

                        $paypal_url = config('paypal.url');
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $paypal_url . '/v1/billing/subscriptions/' . $subscription_id,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => [
                                'Authorization: Bearer ' . $access_token,
                                'Content-Type: application/json',
                                'Accept: application/json',
                                'Prefer: return=representation',
                            ],
                        ));

                        $result = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($result);


                        $check = DB::selectOne(
                            "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                            [$user_id, 1]
                        );

                        if ($check) {

                            if ($response->status === "ACTIVE") {

                                if (abs(strtotime($check->end_time) - strtotime($response->billing_info->next_billing_time)) <= 5) {
                                    $this->s = 1;
                                    $this->m = "Success";
                                    $this->r = DB::selectOne(
                                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                                        [$user_id, 1]
                                    );
                                    return $this->response();
                                } else {


                                    if ($user_subscription->coupon_code != null) {
                                        $used_discount = DB::selectOne("SELECT * FROM used_discount WHERE user_id = ? AND BINARY coupon_code = ?", [$user_id, $user_subscription->coupon_code]);
                                        if (!$used_discount) {

                                            DB::update("UPDATE discounts SET used_usage_count = used_usage_count + 1 WHERE BINARY coupon_code = ?", [$user_subscription->coupon_code]);

                                            DB::insert("INSERT INTO used_discount (user_id,coupon_code) VALUES (?,?)", [$user_id, $user_subscription->coupon_code]);
                                        }
                                    }

                                    $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 1, $response->plan_id, $response->id, $response->status]);

                                    $transaction_id = $this->getId();

                                    DB::update(
                                        "UPDATE user_subscription SET transaction_id = ?, subscription_id = ?,plan_id = ?,start_time = STR_TO_DATE(?, '%Y-%m-%dT%H:%i:%sZ'),end_time = STR_TO_DATE(?, '%Y-%m-%dT%H:%i:%sZ'),status = ? WHERE user_id = ? AND is_current = ?",
                                        [
                                            $transaction_id,
                                            $response->id,
                                            $response->plan_id,
                                            $response->start_time,
                                            $response->billing_info->next_billing_time,
                                            $response->status,
                                            $user_id,
                                            1
                                        ]
                                    );

                                    $this->s = 1;
                                    $this->m = "Success";
                                    $this->r = DB::selectOne(
                                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                                        [$user_id, 1]
                                    );
                                    return $this->response();
                                }
                            } else {

                                if ($check->status != $response->status || $check->subscription_id != $response->id) {
                                    $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 1, $response->plan_id, $response->id, $response->status]);

                                    $transaction_id = $this->getId();

                                    DB::update(
                                        "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ?",
                                        [$transaction_id, $response->status, $user_id, 1]
                                    );

                                    $this->s = 1;
                                    $this->m = "Success";
                                    $this->r = DB::selectOne(
                                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                                        [$user_id, 1]
                                    );
                                    return $this->response();
                                } else {
                                    DB::insert("INSERT INTO webhook (data) VALUES ('Already record in transactions')");
                                }
                            }
                        } else {

                            DB::insert("INSERT INTO webhook (data) VALUES ('No user found with this subscription id in webhook handler')");

                            $this->s = 0;
                            $this->m = "Subscription Not Found";
                            return $this->response();
                        }
                    } else {
                        DB::insert("INSERT INTO webhook (data) VALUES ('No subscription found for user')");
                    }
                } else {
                    DB::insert("INSERT INTO webhook (data) VALUES ('No subscription id in webhook handler')");
                }
            } else {
                DB::insert("INSERT INTO webhook (data) VALUES ('No event in webhook handler')");
            }
        } catch (Exception $e) {
            DB::insert("INSERT INTO webhook (data) VALUES (?)", ["Error: " . $e]);

            $this->s = 0;
            $this->m = "Error occurred: " . $e->getMessage();
            return $this->response();
        }
    }

    // For execute CRON in https://cron-job.org/en/
    function check_trial_expiry(Request $r)
    {

        $query = DB::select("SELECT * FROM user_subscription WHERE type = ? AND status = ?", [0, "ACTIVE"]);
        $current_datetime = Carbon::now();

        if ($query) {
            foreach ($query as $key => $val) {
                if ($val->end_time < $current_datetime) {


                    $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status,free_by_coupon) VALUES (?,?,?,?,?,?)", [$val->user_id, 0, $val->plan_id, $val->subscription_id, "EXPIRED", $val->free_by_coupon]);

                    $transaction_id = $this->getId();

                    DB::update(
                        "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ? ",
                        [$transaction_id, "EXPIRED", $val->user_id, 1]
                    );

                    $user = DB::selectOne("SELECT * FROM user_details WHERE id = ?", [$val->user_id]);

                    $this->trial_expire_mail($user->email);
                }
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "No Trial Subscriptions";
            return $this->response();
        }
    }

    // For execute CRON in https://cron-job.org/en/
    function check_order_expiry(Request $r)
    {

        $query = DB::select("SELECT * FROM user_subscription WHERE type = ? AND status = ?", [2, "ACTIVE"]);
        $current_datetime = Carbon::now();

        if ($query) {
            foreach ($query as $key => $val) {
                if ($val->end_time < $current_datetime) {


                    $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status,free_by_coupon) VALUES (?,?,?,?,?,?)", [$val->user_id, 2, $val->plan_id, $val->subscription_id, "EXPIRED", $val->free_by_coupon]);

                    $transaction_id = $this->getId();

                    DB::update(
                        "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ? ",
                        [$transaction_id, "EXPIRED", $val->user_id, 1]
                    );

                    $user = DB::selectOne("SELECT * FROM user_details WHERE id = ?", [$val->user_id]);

                    $this->subscription_expire_mail($user->email);
                }
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "No Order Subscriptions";
            return $this->response();
        }
    }

    function check_expiry(Request $r)
    {
        // This function is used to check the expiry of all subscriptions 3 types: Trial (type 0), Order (type 2), and 100% Discounted Subscriptions that is free by 100% disocunt coupon but (type 1) is payapl subscription that expiry manage by the paypal webhook check hee only if 100% discount coupon is used then it will be type 1 and it will be managed by this function

        $current_datetime = Carbon::now();
        $query = DB::select(" SELECT * FROM user_subscription WHERE status = 'ACTIVE' AND is_current = 1 AND end_time < ? AND ( type = 0 OR type = 2 OR (type = 1 AND free_by_coupon = 1)) ", [$current_datetime]);

        if ($query) {
            foreach ($query as $val) {
                $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status,free_by_coupon) VALUES (?,?,?,?,?,?)", [$val->user_id, $val->type, $val->plan_id, $val->subscription_id, "EXPIRED", $val->free_by_coupon]);

                $transaction_id = $this->getId();
                DB::update(
                    "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ? ",
                    [$transaction_id, "EXPIRED", $val->user_id, 1]
                );

                $user = DB::selectOne("SELECT * FROM user_details WHERE id = ?", [$val->user_id]);

                if ($val->type == 0) {
                    $this->trial_expire_mail($user->email);
                } else if ($val->type == 2) {
                    $this->subscription_expire_mail($user->email);
                } else if ($val->type == 1 && $val->free_by_coupon == 1) {
                    $this->subscription_expire_mail($user->email);
                }
            }

            $this->s = 1;
            $this->m = "Success";
            $this->r = $query;
            return $this->response();
        } else {
            $this->s = 0;
            $this->m = "No Active Subscriptions Found";
            return $this->response();
        }
    }






    // This functions is not in use now make it dyamic with verify subscription & cancel subscription 
    function verify_order(Request $r)
    {
        if ($this->varify_request($r, ["order_id"])) {
            return $this->response();
        }

        $order_id = $r->order_id;
        $user_subscription = DB::selectOne("SELECT * FROM user_subscription WHERE subscription_id = ? AND type = ?", [$order_id, 2]);

        if ($user_subscription) {
            $user_id = $user_subscription->user_id;
            $access_token = $this->get_paypal_access_token();

            $paypal_url = config('paypal.url');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $paypal_url . '/v2/checkout/orders/' . $order_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Prefer: return=representation',
                ],
            ));

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result);

            if ($response->status == "PAYER_ACTION_REQUIRED") {
                $response->status = "APPROVAL_PENDING";
            } else if ($response->status == "APPROVED") {
                $response->status = "ACTIVE";
            } else if ($response->status ==  "COMPLETED") {
                $response->status = "ACTIVE";
            }

            if ($response->status == "ACTIVE") {
                $paypal_url = config('paypal.url');
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $paypal_url . '/v2/checkout/orders/' . $order_id . '/capture',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Prefer: return=representation',
                    ],
                ));

                $result = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($result);


                if (isset($response->id) && $response->id !== null) {
                    if ($response->status == "PAYER_ACTION_REQUIRED") {
                        $response->status = "APPROVAL_PENDING";
                    } else if ($response->status == "APPROVED") {
                        $response->status = "ACTIVE";
                    } else if ($response->status ==  "COMPLETED") {
                        $response->status = "ACTIVE";
                    }

                    $check = DB::selectOne(
                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                        [$user_id, 1]
                    );

                    if ($check) {

                        if ($check->status == $response->status) {
                            $this->s = 1;
                            $this->m = "Success";
                            $this->r = DB::selectOne(
                                "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                                [$user_id, 1]
                            );
                            return $this->response();
                        } else {
                            $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_id, 2, $user_subscription->plan_id, $response->id, $response->status]);
                            $transaction_id = $this->getId();

                            $plan_details = DB::selectOne("SELECT * FROM subscription WHERE plan_id = ? ", [$user_subscription->plan_id]);


                            $start_time = Carbon::parse($user_subscription->start_time);

                            $end_time = $start_time->addMonth($plan_details->month);

                            DB::update(
                                "UPDATE user_subscription SET transaction_id = ?,end_time = ?,status = ? WHERE user_id = ? AND is_current = ?",
                                [$transaction_id, $end_time, $response->status, $user_id, 1]
                            );

                            $this->s = 1;
                            $this->m = "Success";
                            $this->r = DB::selectOne(
                                "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                                [$user_id, 1]
                            );
                            return $this->response();
                        }
                    }
                } else {
                    $this->s = 1;
                    $this->m = "Success";
                    $this->r = DB::selectOne(
                        "SELECT * FROM user_subscription WHERE user_id = ? AND is_current = ?",
                        [$user_id, 1]
                    );
                    return $this->response();
                }
            } else {
                $this->s = 1;
                $this->m = "Success";
                $this->r = $response;
                return $this->response();
            }
        } else {
            $this->s = 0;
            $this->m = "Invalid request";
            return $this->response();
        }
    }

    function cancel_trial_plan(Request $r)
    {
        if ($this->varify_request($r, ["subscription_id"])) {
            return $this->response();
        }

        $subscription_id = $r->subscription_id;
        $user_subscription = DB::selectOne("SELECT * FROM user_subscription WHERE subscription_id = ?", [$subscription_id]);

        if ($user_subscription->status != "ACTIVE") {
            $this->s = 0;
            $this->m = " only active subscriptions can be canceled. If your subscription is already canceled, expired, or suspended, you will not be able to cancel it again.";
            return $this->response();
        }

        $transaction = DB::insert("INSERT INTO transaction (user_id,type,plan_id,subscription_id,status) VALUES (?,?,?,?,?)", [$user_subscription->user_id, 0, $user_subscription->plan_id, $user_subscription->subscription_id, "CANCELLED"]);

        $transaction_id = $this->getId();

        DB::update(
            "UPDATE user_subscription SET transaction_id = ?,status = ? WHERE user_id = ? AND is_current = ? ",
            [$transaction_id, "CANCELLED", $user_subscription->user_id, 1]
        );

        $this->s = 1;
        $this->m = "Success";
        return $this->response();
    }
}
