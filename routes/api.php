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
Route::group(['namespace'=>'App\Http\Controllers'],function() {


    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::resource('gumdrops', 'GumdropController');
    Route::get('/players/{registrar_id}/gumdrops', 'GumdropController@gumdropsForPlayer');

    /**
     * These generic routes are required for ALL EOS services
     */
    Route::post('/configure', 'ClientController@configure')->middleware(['auth.key']);
    Route::get('/probe', 'ProbeController@probe');
    Route::get('/version', 'ProbeController@version');
    Route::get('/schema', 'ClientController@settingsSchema')->middleware(['auth.key']);
    Route::get('/settings', 'ClientController@getSettings')->middleware(['auth.key']);
    Route::post('/settings', 'ClientController@postSettings')->middleware(['auth.key']);
    Route::delete('/settings', 'ClientController@deleteSettings')->middleware(['auth.key']);
});
// override routes from Passport to allow api client access
Route::group(['namespace' => 'Laravel\Passport\Http\Controllers'], function() {
    Route::get('/oauth/clients','ClientController@forUser')->middleware(['auth.basic']);
    Route::post('/oauth/clients', 'ClientController@store')->middleware(['auth.basic']);
});