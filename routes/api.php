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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('gumdrops','GumdropController');
Route::get('/users/{user_id}/gumdrops', 'GumdropController@gumdropsForUser');

/**
 * These generic routes are required for ALL EOS services
 */
Route::post('/configure','ClientController@configure');
Route::get('/probe','ProbeController@probe');
Route::get('/version', 'ProbeController@version');
