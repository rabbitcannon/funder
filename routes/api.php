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

// all your API routes should be within this route group!
Route::group(['namespace'=>'App\Http\Controllers'], function() {

    /*
     * Funding routes
     */
    Route::post('/funding/login', 'FundingController@login');
    Route::get('/funding', 'FundingController@getFunding');

    /*
     * Payment Routes
     */
    Route::post('/methods', 'FundingController@getPaymentMethods');
    Route::post('/methods/add', 'FundingController@addPaymentMethod');

    /*
     * Nickname Check
     */
    Route::post('/nickname/check', 'FundingController@checkNickname');

    /*
     * Fund Routes
     */
    Route::post('/funds/add', 'FundingController@fundWallet');
    Route::get('/funds/balance/{hash}', 'FundingController@getPlayerBalance');

    /*
     * Account History
     */
    Route::post('/account/history', 'FundingController@getAccountHistory');
});
