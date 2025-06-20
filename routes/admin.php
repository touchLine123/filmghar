<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login');
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });

        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
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

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('chart/deposit-withdraw', 'depositAndWithdrawReport')->name('chart.deposit.withdraw');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
        Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
        Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with-balance', 'usersWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('list', 'list')->name('list');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });

    //Category
    Route::controller('CategoryController')->name('category.')->prefix('category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'CategoryController@status')->name('status');
    });

    
     Route::controller('PayoutRequestController')->name('payout.')->prefix('payout')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('payout-requests/{id}/approve', 'PayoutRequestController')->name('approve');
        Route::post('payout-requests/{id}/reject', 'PayoutRequestController@status')->name('reject');
    });


    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        Route::get('payout-requests', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'index'])->name('admin.payout_requests.index');
        Route::post('payout-requests/{id}/approve', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'approve'])->name('admin.payout_requests.approve');
        Route::post('payout-requests/{id}/reject', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'reject'])->name('admin.payout_requests.reject');
    });


    //Vendor
    Route::controller('VendorController')->name('vendors.')->prefix('vendor')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });

    //Sub Category
    Route::controller('SubcategoryController')->name('subcategory.')->prefix('subcategory')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
    });

    //ItemController
    Route::controller('ItemController')->name('item.')->group(function () {
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
    Route::controller('EpisodeController')->name('item.')->group(function () {
        Route::get('episodes/{id}', 'episodes')->name('episodes');
        Route::post('add-episode/{id}', 'addEpisode')->name('addEpisode');
        Route::post('edit-episode/{id}', 'updateEpisode')->name('updateEpisode');
        Route::get('add-episode-video/{id}', 'addEpisodeVideo')->name('episode.addVideo');
        Route::post('add-episode-video/{id}', 'storeEpisodeVideo')->name('episode.upload');
        Route::get('update-episode-video/{id}', 'updateEpisodeVideo')->name('episode.updateVideo');
        Route::get('episode/subtitle/list/{id}/{videoId}', 'subtitles')->name('episode.subtitle.list');
    });

    //SliderController
    Route::controller('SliderController')->name('sliders.')->prefix('sliders')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'addSlider')->name('add');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('status/{id}', 'status')->name('status');
    });

    //PlanController
    Route::controller('PlanController')->name('plan.')->prefix('plan')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
    });

    //OfferController
    Route::controller('OfferController')->name('offer.')->prefix('offer')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
    });

    //LiveTelevisionController
    Route::controller('LiveTelevisionController')->name('television.')->prefix('live-television')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::post('status/{id}', 'status')->name('status');
    });

    //AdvertiseController
    Route::controller('AdvertiseController')->prefix('advertise')->group(function () {
        Route::get('advertise', 'index')->name('advertise.index');
        Route::post('store/{id?}', 'store')->name('advertise.store');
        Route::post('remove/{id}', 'remove')->name('advertise.remove');

        Route::get('video/advertises', 'videoAdvertise')->name('video.advertise.index');
        Route::post('video/advertises/{id?}', 'videoAdvertiseStore')->name('video.advertise.store');
        Route::post('video/remove/{id}', 'videoAdvertiseRemove')->name('video.advertise.remove');
    });

    // Subscriber
    Route::controller('SubscriberController')->prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('send-email', 'sendEmailForm')->name('send.email');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('send-email', 'sendEmail')->name('send.email');
    });

    // Watch Party
    Route::middleware('watch.party')->controller('WatchPartyController')->prefix('watch/party')->name('watch.party.')->group(function () {
        Route::get('/', 'all')->name('all');
        Route::get('running', 'running')->name('running');
        Route::get('canceled', 'canceled')->name('canceled');
        Route::get('joined/{id}', 'joined')->name('joined');
    });

    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->group(function () {
        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });

        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // DEPOSIT SYSTEM
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function () {
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

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction/{user_id?}', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });

    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });

    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::controller('FileStorageController')->name('storage.')->prefix('storage')->group(function () {
        Route::get('ftp', 'ftp')->name('ftp');
        Route::post('ftp', 'ftpUpdate');
        Route::get('wasabi', 'wasabi')->name('wasabi');
        Route::post('wasabi', 'wasabiUpdate');
        Route::get('digital-ocean', 'digitalOcean')->name('digital.ocean');
        Route::post('digital-ocean', 'digitalOceanUpdate');
    });

    Route::controller('GeneralSettingController')->group(function () {

        Route::get('system-setting', 'systemSetting')->name('setting.system');

        // General Setting
        Route::get('general-setting', 'general')->name('setting.general');
        Route::post('general-setting', 'generalUpdate');

        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');

        //Custom CSS
        Route::get('sitemap', 'sitemap')->name('setting.sitemap');
        Route::post('sitemap', 'sitemapSubmit');

        //Custom CSS
        Route::get('robot', 'robot')->name('setting.robot');
        Route::post('robot', 'robotSubmit');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');

        //In app purchase
        Route::get('in-app-purchase', 'inAppPurchase')->name('setting.app.purchase');
        Route::post('in-app-purchase', 'inAppPurchaseConfigure');
        Route::get('in-app-purchase/file/download', 'inAppPurchaseFileDownload')->name('setting.app.purchase.file.download');

    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('system-update', 'systemUpdate')->name('update');
        Route::post('system-update', 'systemUpdateProcess')->name('update.process');
        Route::get('system-update/log', 'systemUpdateLog')->name('update.log');
    });

    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key?}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate');
            Route::post('remove/{id}', 'remove')->name('remove');
        });
    });
});
