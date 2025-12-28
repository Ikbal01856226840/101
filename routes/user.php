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
Route::group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Backend\User'], function () {
    
    //user route
    Route::get('user-dashboard', 'DashboardController@index')->name('user-dashboard');
    Route::resource('user', 'UserController');
    Route::get('user/list/show', 'UserController@userListShow')->name('user-list-show');

    //user privilege route
    Route::get('user-privilege/{id}', 'UserController@userPrivilegeShow')->name('user-privilege');
    Route::post('user-privilege/store', 'UserController@userPrivilegeStore')->name('user-privilege-store');

    //user-refresh
    Route::get('user-refresh', 'UserController@userRefresh')->name('user-refresh');

});
