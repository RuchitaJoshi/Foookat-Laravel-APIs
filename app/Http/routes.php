<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['namespace' => 'App', 'prefix' => 'api/v1/app'], function () {
    Route::get('sync', 'AppController@getSync');
});

Route::group(['namespace' => 'User', 'prefix' => 'api/v1/user/auth'], function () {
    Route::post('login', 'AuthController@postLogin');

    Route::post('signup', 'AuthController@postSignup');

    Route::post('checkFacebookId', 'AuthController@postCheckFacebookId');

    Route::post('facebook', 'AuthController@postFacebook');

    Route::post('checkGoogleId', 'AuthController@postCheckGoogleId');

    Route::post('google', 'AuthController@postGoogle');

    Route::post('refreshToken', 'AuthController@postRefreshToken');
});

Route::group(['namespace' => 'Auth', 'prefix' => 'api/v1/auth'], function () {
    Route::get('signup/confirm/{verification_code}', ['as' => 'signup.confirm', 'uses' => 'AuthController@verifyEmail']);

    Route::post('password/email', ['as' => 'password.email', 'uses' => 'PasswordController@sendResetLinkEmail']);

    Route::post('password/reset', ['as' => 'password.reset', 'uses' => 'PasswordController@reset']);

    Route::get('password/reset/{token?}', ['as' => 'password.reset.request', 'uses' => 'PasswordController@showResetForm']);
});

Route::group(['namespace' => 'User', 'prefix' => 'api/v1/user', 'middleware' => ['before' => 'jwt.auth']], function () {
    Route::get('preferences', 'UserController@getPreferences');

    Route::post('runningLiveDeals', 'DealController@postRunningLiveDeals');

    Route::post('startingSoonDeals', 'DealController@postStartingSoonDeals');

    Route::get('deal', 'DealController@getDeal');

    Route::post('claimDeal', 'DealController@postClaimDeal');

    Route::post('redeemDeal', 'DealController@postRedeemDeal');

    Route::post('favouriteDeal', 'DealController@postFavouriteDeal');

    Route::post('removeFavouriteDeal', 'DealController@postRemoveFavouriteDeal');

    Route::post('reportDealError', 'DealController@postReportDealError');

    Route::get('store', 'StoreController@getStore');

    Route::get('reviews', 'StoreController@getReviews');

    Route::post('review', 'StoreController@postReview');

    Route::get('news', 'StoreController@getNews');

    Route::post('favouriteStore', 'StoreController@postFavouriteStore');

    Route::post('removeFavouriteStore', 'StoreController@postRemoveFavouriteStore');

    Route::get('preferences', 'UserController@getPreferences');

    Route::get('profile', 'UserController@getProfile');

    Route::post('profile', 'UserController@postProfile');

    Route::post('sendVerificationCode', 'UserController@postSendVerificationCode');

    Route::post('verifyCode', 'UserController@postVerifyCode');

    Route::get('favouriteDeals', 'UserController@getFavouriteDeals');

    Route::get('favouriteStores', 'UserController@getFavouriteStores');

    Route::get('redeems', 'UserController@getRedeems');

    Route::post('changePassword', 'UserController@postChangePassword');
    
    Route::post('logout', 'UserController@postLogout');
});

Route::group(['namespace' => 'Seller', 'prefix' => 'api/v1/seller', 'middleware' => ['before' => 'jwt.auth']], function () {
    Route::get('preferences', 'SellerController@getPreferences');
    
    Route::post('signupInquiry', 'BusinessController@postSignupInquiry');

    Route::get('business', 'BusinessController@getBusiness');

    Route::get('stores', 'BusinessController@getStores');

    Route::post('createStore', 'StoreController@postCreateStore');

    Route::post('editStore', 'StoreController@postEditStore');

    Route::get('store', 'StoreController@getStore');

    Route::get('deals', 'StoreController@getDeals');

    Route::post('createDeal', 'DealController@postCreateDeal');

    Route::post('editDeal', 'DealController@postEditDeal');

    Route::get('deal', 'DealController@getDeal');
});