<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('v1/admin')->group(function () {
    Route::get('auth/create-token', [App\Http\Controllers\BackendApi\AuthController::class, 'createToken']);

    Route::post('login', [App\Http\Controllers\BackendApi\AuthController::class, 'login']);
    Route::group(['middleware' => 'auth:api', 'prifix' => 'admin', 'namespace' => 'App\Http\Controllers\BackendApi'], function () {
        //auth route
        Route::post('register', 'AuthController@register');
        Route::post('change-password', 'AuthController@change_password');
        Route::post('logout', 'AuthController@logout');
        // //user route
        // Route::get('users', 'UserController@index');
        // Route::get('user/edit/{id}', 'UserController@edit');
        // Route::post('user/update/{id}', 'UserController@update');
        // Route::post('user/delete/{id}', 'UserController@delete');

    });
    //Master
    Route::group(['middleware' => 'auth:api', 'prifix' => 'admin', 'namespace' => 'App\Http\Controllers\BackendApi\Master'], function () {
        //group chart route
        // Route::apiResource('group-chart', 'GroupChartController');
        //leager header route
        Route::apiResource('leager-head', 'LegerHeadController');
    });
});
