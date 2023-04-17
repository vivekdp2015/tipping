<?php

use Illuminate\Http\Request;

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

Route::group(['namespace' => 'API\V1', 'prefix' => 'v1',], function () {
    Route::post('login', 'AuthenticationController@login');
    Route::post('social-login', 'AuthenticationController@socialLogin');
    Route::post('register', 'AuthenticationController@register');
    Route::post('request-forgot-password', 'AuthenticationController@requestForgotPassword');
    Route::post('response-forgot-password', 'AuthenticationController@responseForgotPassword');

    Route::get('cms/{page}', 'CMSController@content');
    Route::get('social-media', 'CMSController@socialMedia');
    Route::get('marketplace-template', 'CMSController@downloadFile');
});

Route::group(['namespace' => 'API\V1', 'prefix' => 'v1', 'middleware' => ['cors', 'auth:api']], function () {

    /** Authentication Management */
    Route::post('reset-password/{user}', 'AuthenticationController@resetPassword');
    /*************************** */

    /** User Management */
    Route::get('users/edit/{user}', 'UserController@edit');
    Route::get('users/details-by-qr-code/{code}', 'UserController@getUserDetailsByQRCode');
    Route::post('users/update/{user}', 'UserController@update');
    Route::post('users/switch-user/{user}', 'UserController@switchUser');
    Route::post('users/update-tippie/{user}', 'UserController@updateTippie');
    Route::post('users/location-update', 'UserController@locationUpdate');
    Route::post('near-by-tippes/{page?}', 'UserController@nearByTippes');
    Route::post('users/search/{page?}', 'UserController@search');
    Route::post('users/token-update', 'UserController@storeDeviceToken');
    /****************** */

    /** Stripe Flow */
    Route::get('cards', 'UserController@getCards');
    Route::post('cards/create', 'UserController@createCard');
    Route::post('cards/delete', 'UserController@deleteCard');
    Route::post('cards/update/{cardNumber}', 'UserController@updateCard');
    Route::get('cards/edit/{cardNumber}', 'UserController@editCard');
    Route::post('checkout', 'UserController@checkout');
    Route::post('connect-stripe-account', 'UserController@connectStripeAccount');
    /****************** */

    /** Transactions */
    Route::get('transactions', 'TransactionController@transactions');
    Route::get('payouts', 'TransactionController@payouts');
    Route::get('payouts-tippie', 'TransactionController@payoutsTippie');
    /*************** */

    /** Category Management */
    Route::get('categories', 'CategoryController@index');
    Route::post('categories/store', 'CategoryController@store');
    Route::get('categories/edit/{category}', 'CategoryController@edit');
    Route::post('categories/update/{category}', 'CategoryController@update');
    /********************** */

    /** Notification Management */
    Route::get('notifications/types/{type?}', 'NotificationController@types');
    Route::get('notifications/types/details/{typeId}', 'NotificationController@details');
    Route::post('notifications/types/set-notification-type', 'NotificationController@setNotificationType');
    Route::get('notifications', 'NotificationController@index');
    Route::get('notifications/delete/{id}', 'NotificationController@delete');
    Route::get('notifications/sound-img', 'NotificationController@soundImg');
    /************************** */

    /** CMS Management */
    Route::post('contact-us', 'FeedbackController@store');
    /***************** */
});
