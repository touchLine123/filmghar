<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::namespace('Api')->name('api.')->group(function () {

    Route::controller('AppController')->group(function () {
        Route::get('general-setting', 'generalSetting');
        Route::get('get-countries', 'getCountries');
        Route::get('language/{key}', 'getLanguage');
        Route::get('policies', 'policies');
        Route::get('faq', 'faq');
    });

    Route::namespace('Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('login', 'login');
            Route::post('check-token', 'checkToken');
            Route::post('social-login', 'socialLogin');
        });
        Route::post('register', 'RegisterController@register');

        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/email', 'sendResetCodeEmail');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });
    
    Route::get('dashboard', 'UserController@dashboard');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('user-data-submit', 'UserController@userDataSubmit');

        //authorization
        Route::middleware('registration.complete')->controller('AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode');
            Route::post('verify-email', 'emailVerification');
            Route::post('verify-mobile', 'mobileVerification');
        });

        Route::middleware(['check.status'])->group(function () {
            
            Route::get('user-info', 'UserController@userInfo');
            Route::post('deactivate-account', 'UserController@deleteAccount');
 
            Route::middleware('registration.complete')->group(function () {


                Route::controller('UserController')->group(function () {
                    Route::get('subscribe', 'subscribe');
                    Route::get('plans/{type?}', 'plans');

                    Route::post('subscribe-plan', 'subscribePlan');
                    Route::post('purchase-plan', 'purchasePlan');
                    Route::post('purchase-app', 'purchaseFromApp');

                    Route::post('add-wishlist', 'addWishlist');
                    Route::post('remove-wishlist', 'removeWishlist');
                    Route::get('check-wishlist', 'checkWishlist');
                    Route::get('wishlists', 'wishlists');
                    Route::get('history', 'history');
                    Route::get('watch', 'watchVideo');
                    Route::get('play', 'playVideo');
                    Route::post('status', 'status');

                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');

                    //KYC
                    Route::get('kyc-form', 'kycForm');
                    Route::post('kyc-submit', 'kycSubmit');

                    //Report
                    Route::any('deposit/history', 'depositHistory');
                    Route::get('rented/items', 'rentedItem');

                    Route::get('transactions', 'transactions');

                    Route::post('add-device-token', 'addDeviceToken');
                    Route::get('push-notifications', 'pushNotifications');
                    Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                    //2FA
                    Route::get('twofactor', 'show2faForm');
                    Route::post('twofactor/enable', 'create2fa');
                    Route::post('twofactor/disable', 'disable2fa');

                    Route::post('delete-account', 'deleteAccount');

                });

                Route::controller('PusherController')->group(function () {
                    Route::post('authenticationApp', 'authenticationApp');
                });

                Route::controller('WatchPartyController')->prefix('party')->group(function () {
                    Route::post('create', 'create');
                    Route::get('room/{code}/{guestId?}', 'room');
                    Route::post('join/request', 'joinRequest');
                    Route::post('request/accept/{id?}', 'requestAccept');
                    Route::post('request/reject/{id?}', 'requestReject');
                    Route::post('send/message', 'sendMessage');
                    Route::post('player/setting', 'playerSetting');
                    Route::post('status/{id}', 'status');
                    Route::post('cancel/{id}', 'cancel');
                    Route::post('leave/{id}/{user_id}', 'leave');
                    Route::post('disabled/{id}', 'disabled');
                    Route::get('history', 'history');
                    Route::post('reload', 'reload');
                });

                // Payment
                Route::controller('PaymentController')->group(function () {
                    Route::get('deposit/methods', 'methods');
                    Route::post('deposit/insert', 'depositInsert');
                    Route::post('app/payment/confirm', 'appPaymentConfirm');
                    Route::get('membership', 'membership');
                });

                Route::controller('TicketController')->prefix('ticket')->group(function () {
                    Route::get('/', 'supportTicket');
                    Route::post('create', 'storeSupportTicket');
                    Route::get('view/{ticket}', 'viewTicket');
                    Route::post('reply/{id}', 'replyTicket');
                    Route::post('close/{id}', 'closeTicket');
                    Route::get('download/{attachment_id}', 'ticketDownload');
                });

            });
        });

        Route::get('logout', 'Auth\LoginController@logout');
    });
    
     Route::controller('FrontendController')->group(function () {
        Route::get('logo', 'logo');
        Route::get('welcome-info', 'welcomeInfo');
        Route::get('sliders', 'sliders');
        Route::get('live-television', 'liveTelevision');
        Route::get('live-tv/{id?}', 'watchTelevision');

        Route::get('section/featured', 'featured');
        Route::get('section/recent', 'recentlyAdded');
        Route::get('section/latest', 'latestSeries');
        Route::get('section/single', 'single');
        Route::get('section/trailer', 'trailer');
        Route::get('section/free-zone', 'freeZone');
        Route::get('section/rent', 'rent');

        Route::get('movies', 'movies');
        Route::get('episodes', 'episodes');

        Route::get('categories', 'categories');
        Route::get('subcategories', 'subcategories');
        Route::get('sub-category/{id}', 'subCategory')->name('subCategory');

        Route::get('search', 'search');

        Route::get('watch-video', 'watchVideo');
        Route::get('play-video', 'playVideo');
        Route::get('policy-pages', 'policyPages');
        Route::get('language/{code?}', 'language');
        Route::get('pop-up/ads', 'popUpAds');
        Route::post('common', 'common');
    });
});
