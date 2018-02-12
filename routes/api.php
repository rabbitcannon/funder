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


 /*   Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
*/
    Route::resource('gumdrops', 'GumdropController');
    Route::get('/players/gumdrops', 'GumdropController@gumdropsForPlayer');

});
