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

Route::group(['prefix' => 'v1'], function ($route) {

    Route::post('participate', 'ParticipantsController@participate');

    Route::get('results', 'ResultController@results');

    Route::prefix('auth')->group(function () {

        Route::get('participate', 'ParticipantsController@participants');

        Route::post('approve', 'ParticipantsController@approve');

        Route::post('decline', 'ParticipantsController@decline');

        Route::get('participant/{url}', 'ParticipantsController@getImage');

        Route::post('export', 'ParticipantsController@export');

        Route::get('download', 'ParticipantsController@getExported');

        Route::post('result', 'ResultController@result');

        Route::post('delete', 'ResultController@deleteResult');

        Route::post('register', 'AuthController@register');

        Route::post('login', 'AuthController@login');

        Route::get('refresh', 'AuthController@refresh');

        Route::middleware('auth:api')->group(function () {

            Route::get('user', 'AuthController@user');

            Route::post('logout', 'AuthController@logout');
        });
    });

});
