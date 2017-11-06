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
Route::get('/schema', 'ClientController@settingsSchema');
Route::get('/settings', 'ClientController@getSettings');
Route::post('/settings', 'ClientController@postSettings');
Route::delete('/settings', 'ClientController@deleteSettings');

// override routes from Passport to allow api client access
Route::group(['namespace' => 'Laravel\Passport\Http\Controllers'], function() {
    Route::get('/oauth/clients','ClientController@forUser')->middleware(['auth.basic']);
    Route::post('/oauth/clients', 'ClientController@store')->middleware(['auth.basic']);
});