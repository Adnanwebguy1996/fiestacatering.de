<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $s = 0;
    public $m = "Something went wrong please try again.";
    public $r = null;
    public $c = null;

    public function response($isArray = 0)
    {
        if ($isArray) {
            return response()->json(["s" => $this->s, "m" => $this->m, "r" => $this->r == null ? []  : $this->r, "c" => $this->c]);
        }
        return response()->json(["s" => $this->s, "m" => $this->m, "r" => $this->r, "c" => $this->c]);
    }

    public function varify_request($req = null, $perm = [], $media = [])
    {

        $check = [];

        foreach ($perm as $v) {

            if (!$req->has($v)) {
                array_push($check, $v);
            }
        }

        foreach ($media as $val) {
            if (!$req->hasFile($val)) {
                array_push($check, $val);
            }
        }

        if (count($check) != 0) {
            $this->s = 0;
            $this->m = "Required :" . implode(" ", $check);
            $this->r = null;
            return true;
        }

        return false;
    }

    public function getId()
    {
        return (int) DB::getPdo()->lastInsertId();
    }

    function upload_file($file, $path)
    {
        $file_name = uniqid() . '_' . str_replace(" ", "", time()) . '.' . $file->extension();
        $file->move(base_path('public') . $path, $file_name);
        return $path . '/' . $file_name;
    }

    public function profile_image()
    {
        return "/uploads/profile_images";
    }

    public function truck_image()
    {
        return "/uploads/truck_images";
    }

    public function meal_image()
    {
        return "/uploads/meal_images";
    }

    public function diet_image()
    {
        return "/uploads/diet_images";
    }

    public function company_image()
    {
        return "/uploads/company_images";
    }

    public function get_paypal_access_token()
    {

        $paypal_url = config('paypal.url');
        $paypal_client_id = config('paypal.client_id');
        $paypal_client_secret = config('paypal.client_secret');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $paypal_url . "/v1/oauth2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            CURLOPT_USERPWD => $paypal_client_id . ":" . $paypal_client_secret
        ));


        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        if ($http_code == 200) {
            $response_data = json_decode($response, true);
            if (isset($response_data['access_token'])) {
                // echo($response_data['access_token']);
                return $response_data['access_token'];
            }
        } else {
            // Failed to get PayPal access token
            return null;
        }
    }


    public function welcome_mail($email, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.german.Welcome',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Willkommen - Fiesta Catering');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.Welcome',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Welcome - Fiesta Catering');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function reset_password_mail($email, $fp_token, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email, "fp_token" => $fp_token);
                Mail::send(
                    'emails.german.ResetPassword',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Passwort zurücksetzen');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email, "fp_token" => $fp_token);
                Mail::send(
                    'emails.ResetPassword',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Reset Passsword');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function booking_mail($email, $customer_name, $from_date, $to_date, $no_of_person, $budget_per_person, $total_budget, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email, "customer_name" => $customer_name, "from_date" => $from_date, "to_date" => $to_date, "no_of_person" => $no_of_person, "budget_per_person" => $budget_per_person, "total_budget" => $total_budget);
                Mail::send(
                    'emails.german.Booking',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Neue Buchung');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email, "customer_name" => $customer_name, "from_date" => $from_date, "to_date" => $to_date, "no_of_person" => $no_of_person, "budget_per_person" => $budget_per_person, "total_budget" => $total_budget);
                Mail::send(
                    'emails.Booking',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - New Booking');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function booking_approve_mail($email, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.german.BookingApproved',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Buchung genehmigt');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {

                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.BookingApproved',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Booking Approved');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function booking_cancle_user_mail($email, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.german.BookingCancleUser',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Buchung stornieren');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {

                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.BookingCancleUser',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Booking Cancled');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function booking_cancle_company_mail($email, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.german.BookingCancleCompany',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Buchung stornieren');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.BookingCancleCompany',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Booking Cancled');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function subscribe_newsletter_mail($email, $type, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email, "type" => $type);
                Mail::send(
                    'emails.german.SubscribeNewsLetter',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Willkommen bei Fiesta Catering - Bitte bestätige deine Anmeldung!');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email, "type" => $type);
                Mail::send(
                    'emails.SubscribeNewsLetter',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Subscribe Newsletter');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function unsubscribe_newsletter_mail($email, $type, $language = "DE")
    {
        try {


            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email, "type" => $type);
                Mail::send(
                    'emails.german.UnsubscribeNewsLetter',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Schade, dass du gehen möchtest - Bitte bestätige deine Abmeldung');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {

                $e = $email;

                $data = array("email" => $email, "type" => $type);
                Mail::send(
                    'emails.UnsubscribeNewsLetter',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Unsubscribe Newsletter');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function new_inquiry_mail($inquiry, $language = "DE")
    {
        try {
            $data = array("inquiry" => $inquiry);

            if ($language === "DE") {
                Mail::send(
                    'emails.german.Inquiry',
                    $data,
                    function ($message) {
                        $message->to('fiestabooking@online.de')
                            ->subject('Fiesta Catering - Neue Anfrage');
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );
            } else {
                Mail::send(
                    'emails.Inquiry',
                    $data,
                    function ($message) {
                        $message->to('fiestabooking@online.de')
                            ->subject('Fiesta Catering - New Inquiry');
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function subscription_extend_mail($email, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email,);
                Mail::send(
                    'emails.german.SubscriptionExtend',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Abonnement verlängert');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.SubscriptionExtend',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Subscription Extended');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function subscription_expire_mail($email, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email,);
                Mail::send(
                    'emails.german.SubscriptionExpire',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Abonnement läuft ab');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.SubscriptionExpire',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Subscription Expire');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function trial_expire_mail($email, $language = "DE")
    {
        try {

            if ($language == "DE") {
                $e = $email;

                $data = array("email" => $email,);
                Mail::send(
                    'emails.german.TrialExpire',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Testversion abgelaufen');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            } else {
                $e = $email;

                $data = array("email" => $email);
                Mail::send(
                    'emails.TrialExpire',
                    $data,
                    function ($message) use ($e) {
                        $message->to($e)->subject('Fiesta Catering - Trial Expire');;
                        $message->from("noreply@fiestacatering.de", "Fiesta Catering");
                    }
                );

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
