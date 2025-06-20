<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Vendor\Auth')->group(function () {
    Route::middleware('vendor.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/login', 'showLoginForm')->name('vendor.login');
            Route::post('/login', 'login')->name('login');
            Route::get('logout', 'logout')->middleware('vendor')->withoutMiddleware('vendor.guest')->name('vendor.logout');
        });

        Route::controller('RegisterController')->middleware(['guest'])->group(function () {
            Route::get('register', 'showRegistrationForm')->name('vendor.register');
            Route::post('register', 'register');
            Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
        });

        // vendor Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('vendor.password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change');
        });
    });
});


Route::middleware('vendor')->group(function () {
    Route::namespace('Vendor')->group(function () {
        Route::controller('VendorController')->group(function () {
            Route::get('dashboard', 'dashboard')->name('vendor.dashboard');
            Route::get('chart/deposit-withdraw', 'depositAndWithdrawReport')->name('chart.deposit.withdraw');
            Route::get('profile', 'profile')->name('vendor.profile');
            Route::post('profile', 'profileUpdate')->name('vendor.profile.update');
            Route::get('password', 'password')->name('vendor.password');
            Route::post('password', 'passwordUpdate')->name('vendor.password.update');
            //Notification
            Route::namespace('Vendor')->prefix('vendor')->name('vendor.')->group(function () {
                Route::get('notifications', 'notifications')->name('notifications');
                Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
                Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
                Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
                Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');
            });
            //Report Bugs
            Route::get('request-report', 'requestReport')->name('request.report');
            Route::post('request-report', 'reportSubmit');

            Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
        });
    

        Route::controller('ItemController')->name('vendor.item.')->group(function () {
            Route::get('video-item-status/{id}', 'status')->name('status');
            Route::get('video-items', 'items')->name('index');
            Route::get('video-items/single', 'singleItems')->name('single');
            Route::get('video-items/trailer', 'trailerItems')->name('trailer');
            Route::get('video-items/rent', 'rentItems')->name('rents');
            Route::get('video-items/episode', 'episodeItems')->name('episode');
            Route::get('video-item-create', 'create')->name('create');
            Route::post('video-item-store', 'store')->name('store');
            Route::get('video-item-edit/{id}', 'edit')->name('edit');
            Route::post('video-item-update/{id}', 'update')->name('update');
    
            Route::get('video-item-upload-video/{id}', 'uploadVideo')->name('uploadVideo');
            Route::post('video-item-upload-video/{id}', 'upload')->name('upload.video');
    
            Route::get('video-item-update-video/{id}', 'updateVideo')->name('updateVideo');
            Route::post('video-item-update-video/{id}', 'updateItemVideo');
            Route::get('single-section/{id}', 'singleSection')->name('single_section');
            Route::get('featured/{id}', 'featured')->name('featured');
            Route::get('trending/{id}', 'trending')->name('trending');
            Route::get('list', 'itemList')->name('list');
    
            Route::post('item/fetch', 'itemFetch')->name('fetch');
            Route::post('send/notification/{id}', 'sendNotification')->name('send.notification');
            Route::get('ads/duration/{id}/{episode_id?}', 'adsDuration')->name('ads.duration');
            Route::post('ads/duration/{id}/{episode_id?}', 'adsDurationStore')->name('ads.duration.store');
    
            Route::get('subtitle/list/{id}/{videoId?}', 'subtitles')->name('subtitle.list');
            Route::post('subtitle/store/{itemId}/{episodeId}/{videoId}/{id?}', 'subtitleStore')->name('subtitle.store');
            Route::post('subtitle/delete/{id}', 'subtitleDelete')->name('subtitle.delete');
    
            Route::get('item/report/{id}/{videoId?}', 'report')->name('report');
        });
    
        //EpisodeController
        Route::controller('EpisodeController')->name('vendor.item.')->group(function () {
            Route::get('episodes/{id}', 'episodes')->name('episodes');
            Route::post('add-episode/{id}', 'addEpisode')->name('addEpisode');
            Route::post('edit-episode/{id}', 'updateEpisode')->name('updateEpisode');
            Route::get('add-episode-video/{id}', 'addEpisodeVideo')->name('episode.addVideo');
            Route::post('add-episode-video/{id}', 'storeEpisodeVideo')->name('episode.upload');
            Route::get('update-episode-video/{id}', 'updateEpisodeVideo')->name('episode.updateVideo');
            Route::get('episode/subtitle/list/{id}/{videoId}', 'subtitles')->name('episode.subtitle.list');
        });

        Route::middleware('watch.party')->controller('WatchPartyController')->prefix('watch/party')->name('vendor.watch.party.')->group(function () {
            Route::get('/', 'all')->name('all');
            Route::get('running', 'running')->name('running');
            Route::get('canceled', 'canceled')->name('canceled');
            Route::get('joined/{id}', 'joined')->name('joined');
        });

        Route::controller('DepositController')->prefix('deposit')->name('vendor.deposit.')->group(function () {
            Route::get('all/{user_id?}', 'deposit')->name('list');
            Route::get('pending/{user_id?}', 'pending')->name('pending');
            Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
            Route::get('approved/{user_id?}', 'approved')->name('approved');
            Route::get('successful/{user_id?}', 'successful')->name('successful');
            Route::get('initiated/{user_id?}', 'initiated')->name('initiated');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('reject', 'reject')->name('reject');
            Route::post('approve/{id}', 'approve')->name('approve');
    
        });

    
    });


});