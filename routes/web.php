<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/response-forgot-password', function (Illuminate\Http\Request $request) {
    return redirect()->to('tippingjar://token='.$request->token.'&email='.$request->email);
});

/** Tippie Stripe Flow */
Route::get('get-stripe-auth-code', 'API\V1\UserController@getStripeAuthCode');
/****************/

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::group(['namespace' => 'Admin', 'middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        /** User Mangement */
        Route::get('/users/create', 'UserController@create')->name('users.create');
        Route::get('/users', 'UserController@index')->name('users.index');
        Route::get('/users/data', 'UserController@data')->name('users.data');
        Route::get('/users/edit/{user}', 'UserController@edit')->name('users.edit');
        Route::get('/users/show/{user}', 'UserController@view')->name('users.show');
        Route::post('/users/update/{user}', 'UserController@update')->name('users.update');
        Route::post('/users/store', 'UserController@store')->name('users.store');
        Route::get('/users/tip-sent/{user}', 'UserController@tipSent')->name('users.tip_sent');
        Route::match(['get', 'post'], '/users/tip-sent-data/{user}', 'UserController@tipSentData')->name('users.tip_sent_data');
        Route::get('/users/tip-received/{user}', 'UserController@tipReceived')->name('users.tip_received');
        Route::match(['get', 'post'], '/users/tip-received-data/{user}', 'UserController@tipReceivedData')->name('users.tip_received_data');
        Route::patch('/users/forgot-password/{user}', 'UserController@sendForgotPasswordLink')->name('users.forgot_password');
        Route::delete('/users/delete/{user}', 'UserController@delete')->name('users.delete');
        /***************** */

        /** Category Management */
        Route::get('/categories/create', 'CategoryController@create')->name('categories.create');
        Route::get('/categories', 'CategoryController@index')->name('categories.index');
        Route::get('/categories/data', 'CategoryController@data')->name('categories.data');
        Route::get('/categories/edit/{category}', 'CategoryController@edit')->name('categories.edit');
        Route::post('/categories/update/{category}', 'CategoryController@update')->name('categories.update');
        Route::post('/categories/store', 'CategoryController@store')->name('categories.store');
        Route::delete('/categories/delete/{category}', 'CategoryController@delete')->name('categories.delete');
        /********************** */

        /** CMS Management */
        Route::get('/cms/data', 'CMSController@data')->name('cms.data');
        Route::resource('cms', 'CMSController');
        /***************** */

        /** Feedback Management */
        Route::get('/feedbacks', 'FeedbackController@index')->name('feedbacks.index');
        Route::get('/feedbacks/data', 'FeedbackController@data')->name('feedbacks.data');
        Route::get('/feedback/show/{feedback}', 'FeedbackController@show')->name('feedbacks.show');
        Route::delete('/feedback/delete/{feedback}', 'FeedbackController@delete')->name('feedbacks.delete');
        Route::patch('/feedback/change-status/{feedback}', 'FeedbackController@changeStatus')->name('feedbacks.change_status');
        /********************* */

        /** Site Settings */
        Route::get('/site-settings', 'SiteSettingController@index')->name('site_settings.index');
        Route::post('/site-settings/update', 'SiteSettingController@updateOrCreate')->name('site_settings.update');
        /**************** */

        /** Types Of Notifications */
        Route::get('/notification-types/data', 'NotificationTypeController@data')->name('notification_types.data');
        Route::resource('notification-types', 'NotificationTypeController');
        /************************* */

        /** Sounds Of Notifications */
        Route::get('/notification-sounds/data', 'NotificationSoundController@data')->name('notification_sounds.data');
        Route::get('/notification-sounds', 'NotificationSoundController@index')->name('notification_sounds.index');
        Route::post('/notification-sounds/store', 'NotificationSoundController@store')->name('notification_sounds.store');
        Route::delete('/notification-sounds/delete/{sound}', 'NotificationSoundController@delete')->name('notification_sounds.delete');
        /************************* */
    });
});
