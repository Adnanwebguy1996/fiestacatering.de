<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Partner\CompanyController;
use App\Http\Controllers\Partner\CatererController;
use App\Http\Controllers\Partner\FoodTruckController;
use App\Http\Controllers\Partner\MealDietController;
use App\Http\Controllers\Partner\RequirementController;
use App\Http\Controllers\Partner\ImageController;
use App\Http\Controllers\Paypal\PaypalController;
use App\Http\Controllers\Paypal\DiscountController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\Content\ContentController;
use App\Http\Controllers\RatingReview\RatingReviewController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Inquiry\InquiryController;
use App\Http\Controllers\Partner\StateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route Check
Route::get('/test', function () {
    return 'API is working!';
});

//Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

Route::prefix('auth')->group(function () {

    Route::get('countries', [AuthController::class, 'countries']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reset-password', [AuthController::class, 'reset_password']);
    Route::post('check-fp-token', [AuthController::class, 'check_fp_token']);
    Route::post('change-password', [AuthController::class, 'change_password']);
});

Route::prefix('v1')->group(function () {
    Route::middleware('Authincate')->group(function () {
        Route::prefix('user')->group(function () {

            Route::get('get-all', [UserController::class, 'get_all']);
            Route::get('get-by-id', [UserController::class, 'get_by_id']);
            Route::get('get-light-details', [UserController::class, 'get_light_details']);
            Route::post('update', [UserController::class, 'update']);
            Route::post('change-password', [UserController::class, 'change_password']);
            Route::post('account-status', [UserController::class, 'account_status']);
        });

        Route::prefix('partner')->group(function () {

            Route::prefix('company')->group(function () {

                Route::post('add', [CompanyController::class, 'add']);
                Route::post('update', [CompanyController::class, 'update']);
                // Route::get('get-details', [CompanyController::class, 'get_details']);


                Route::prefix('caterer')->group(function () {

                    Route::post('add', [CatererController::class, 'add']);
                    Route::post('update', [CatererController::class, 'update']);
                    // Route::get('get-all', [CatererController::class, 'get_all']);
                    // Route::get('get-details', [CatererController::class, 'get_details']);
                });

                Route::prefix('food-truck')->group(function () {

                    Route::post('add-category', [FoodTruckController::class, 'add_category']);
                    Route::post('update-category', [FoodTruckController::class, 'update_category']);
                    // Route::get('get-category', [FoodTruckController::class, 'get_category']);
                    Route::post('add-truck', [FoodTruckController::class, 'add_truck']);
                    Route::post('update-truck', [FoodTruckController::class, 'update_truck']);
                    Route::post('add-truck-image', [FoodTruckController::class, 'add_truck_image']);
                    Route::post('update-truck-image', [FoodTruckController::class, 'update_truck_image']);
                    // Route::get('get-all', [FoodTruckController::class, 'get_all']);
                    // Route::get('get-details', [FoodTruckController::class, 'get_details']);
                });

                Route::prefix('requirement')->group(function () {

                    Route::post('add-requirement-category', [RequirementController::class, 'add_requirement_category']);
                    Route::post('update-requirement-category', [RequirementController::class, 'update_requirement_category']);
                    // Route::get('get-requirement-category', [RequirementController::class, 'get_requirement_category']);
                    Route::post('add-update-food-truck-requirement', [RequirementController::class, 'add_update_food_truck_requirement']);
                });

                Route::prefix('meal-diet')->group(function () {

                    Route::post('add-meal-category', [MealDietController::class, 'add_meal_category']);
                    Route::post('update-meal-category', [MealDietController::class, 'update_meal_category']);
                    // Route::get('get-meal-category', [MealDietController::class, 'get_meal_category']);
                    Route::post('add-diet-category', [MealDietController::class, 'add_diet_category']);
                    Route::post('update-diet-category', [MealDietController::class, 'update_diet_category']);
                    // Route::get('get-diet-category', [MealDietController::class, 'get_diet_category']);
                    Route::post('add-update-company-meal', [MealDietController::class, 'add_update_company_meal']);
                    Route::post('add-update-company-diet', [MealDietController::class, 'add_update_company_diet']);
                    Route::post('add-update-food-truck-meal', [MealDietController::class, 'add_update_food_truck_meal']);
                    Route::post('add-update-food-truck-diet', [MealDietController::class, 'add_update_food_truck_diet']);
                });

                Route::prefix('state')->group(function () {

                    Route::post('add-state', [StateController::class, 'add_state']);
                    Route::post('update-state', [StateController::class, 'update_state']);
                    // Route::get('get-state', [StateController::class, 'get_state']);
                    Route::post('add-update-company-state', [StateController::class, 'add_update_company_state']);
                    Route::post('add-update-food-truck-state', [StateController::class, 'add_update_food_truck_state']);
                });

                Route::prefix('image')->group(function () {

                    Route::post('add', [ImageController::class, 'add']);
                    Route::post('update', [ImageController::class, 'update']);
                });
            });
        });

        Route::prefix('paypal')->group(function () {

            // Route::get('plan-list', [PaypalController::class, 'plan_list']);
            Route::post('subscribe-plan', [PaypalController::class, 'subscribe_plan']);
            Route::post('subscribe-plan-free', [PaypalController::class, 'subscribe_plan_free']);
            Route::post('subscribe-trial-plan', [PaypalController::class, 'subscribe_trial_plan']);
            Route::post('extend-trial-plan', [PaypalController::class, 'extend_trial_plan']);
            Route::post('cancel-subscription', [PaypalController::class, 'cancel_subscription']);
            Route::get('verify-subscription', [PaypalController::class, 'verify_subscription']);
            Route::get('transaction', [PaypalController::class, 'transaction']);
            // Route::get('check-trial-expiry', [PaypalController::class, 'check_trial_expiry']);
            // Route::get('check-order-expiry', [PaypalController::class, 'check_order_expiry']);
            Route::get('check-expiry', [PaypalController::class, 'check_expiry']);
            Route::post('create-order', [PaypalController::class, 'create_order']);
            Route::get('verify-order', [PaypalController::class, 'verify_order']);

            Route::prefix('discount')->group(function () {

                Route::post('add', [DiscountController::class, 'add']);
                Route::post('update', [DiscountController::class, 'update']);
                Route::get('get', [DiscountController::class, 'get']);
                Route::post('verify', [DiscountController::class, 'verify']);
            });
        });

        Route::prefix('booking')->group(function () {

            Route::post('check-availability', [BookingController::class, 'check_availability']);
            Route::post('book', [BookingController::class, 'book']);
            Route::get('get-all', [BookingController::class, 'get_all']);
            Route::get('get-details', [BookingController::class, 'get_details']);
            Route::post('approve', [BookingController::class, 'approve']);
            Route::post('cancel', [BookingController::class, 'cancel']);
            Route::get('booking-analytics', [BookingController::class, 'booking_analytics']);
        });

        Route::prefix('contact')->group(function () {

            // Route::post('send', [ContactController::class, 'send']);
            Route::get('get', [ContactController::class, 'get']);
            Route::post('action', [ContactController::class, 'action']);
        });


        Route::prefix('inquiry')->group(function () {

            // Route::post('send', [InquiryController::class, 'send']);
            Route::get('get', [InquiryController::class, 'get']);
            Route::post('action', [InquiryController::class, 'action']);
        });

        Route::prefix('content')->group(function () {

            Route::post('add-faq', [ContentController::class, 'add_faq']);
            Route::post('update-faq', [ContentController::class, 'update_faq']);
            // Route::get('get-faq', [ContentController::class, 'get_faq']);
            Route::post('update-privacy-policy', [ContentController::class, 'update_privacy_policy']);
            // Route::get('get-privacy-policy', [ContentController::class, 'get_privacy_policy']);
            Route::post('update-terms-condition', [ContentController::class, 'update_terms_condition']);
            // Route::get('get-terms-condition', [ContentController::class, 'get_terms_condition']);
            Route::post('update-footer-content', [ContentController::class, 'update_footer_content']);
            // Route::get('get-footer-content', [ContentController::class, 'get_footer_content']);
            Route::post('update-statistics', [ContentController::class, 'update_statistics']);
            // Route::get('get-statistics', [ContentController::class, 'get_statistics']);
            // Route::post('request-newsletter-subscribe', [ContentController::class, 'request_newsletter_subscribe']);
            // Route::post('request-newsletter-unsubscribe', [ContentController::class, 'request_newsletter_unsubscribe']);
            // Route::post('subscribe-newsletter', [ContentController::class, 'subscribe_newsletter']);
            // Route::post('unsubscribe-newsletter', [ContentController::class, 'unsubscribe_newsletter']);
            Route::get('subscribed-newsletter', [ContentController::class, 'subscribed_newsletter']);
        });

        Route::prefix('rating-review')->group(function () {

            Route::post('add-update', [RatingReviewController::class, 'add_update']);
            // Route::get('get-all', [RatingReviewController::class, 'get_all']);
            Route::post('action', [RatingReviewController::class, 'action']);
        });


        Route::prefix('admin')->group(function () {

            Route::get('analytics', [AdminController::class, 'analytics']);
        });
    });

    // Without Middleware Route
    Route::get('partner/company/global-listing', [CompanyController::class, 'global_listing']);
    Route::get('partner/company/get-details', [CompanyController::class, 'get_details']);
    Route::get('partner/company/meal-diet/get-meal-category', [MealDietController::class, 'get_meal_category']);
    Route::get('partner/company/meal-diet/get-diet-category', [MealDietController::class, 'get_diet_category']);
    Route::get('partner/company/state/get-state', [StateController::class, 'get_state']);
    Route::get('partner/company/requirement/get-requirement-category', [RequirementController::class, 'get_requirement_category']);
    Route::get('partner/company/food-truck/get-all', [FoodTruckController::class, 'get_all']);
    Route::get('partner/company/food-truck/get-details', [FoodTruckController::class, 'get_details']);
    Route::get('partner/company/food-truck/get-category', [FoodTruckController::class, 'get_category']);
    Route::get('partner/company/caterer/get-all', [CatererController::class, 'get_all']);
    Route::get('partner/company/caterer/get-details', [CatererController::class, 'get_details']);
    Route::post('contact/send', [ContactController::class, 'send']);
    Route::post('inquiry/send', [InquiryController::class, 'send']);
    Route::get('content/get-faq', [ContentController::class, 'get_faq']);
    Route::get('content/get-privacy-policy', [ContentController::class, 'get_privacy_policy']);
    Route::get('content/get-terms-condition', [ContentController::class, 'get_terms_condition']);
    Route::get('content/get-footer-content', [ContentController::class, 'get_footer_content']);
    Route::get('content/get-statistics', [ContentController::class, 'get_statistics']);
    Route::post('content/request-newsletter-subscribe', [ContentController::class, 'request_newsletter_subscribe']);
    Route::post('content/request-newsletter-unsubscribe', [ContentController::class, 'request_newsletter_unsubscribe']);
    Route::post('content/subscribe-newsletter', [ContentController::class, 'subscribe_newsletter']);
    Route::post('content/unsubscribe-newsletter', [ContentController::class, 'unsubscribe_newsletter']);
    Route::get('rating-review/get-all', [RatingReviewController::class, 'get_all']);
    Route::get('paypal/plan-list', [PaypalController::class, 'plan_list']);
    Route::get('paypal/check-trial-expiry', [PaypalController::class, 'check_trial_expiry']);
    Route::get('paypal/check-order-expiry', [PaypalController::class, 'check_order_expiry']);
    Route::get('paypal/check-expiry', [PaypalController::class, 'check_expiry']);
    Route::post('paypal/handle-webhook', [PaypalController::class, 'handle_webhook']);
});
