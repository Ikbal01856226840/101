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
Route::group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Backend\Approve'], function () {

    //route approve
    Route::get('show-approve-page', 'ApproveController@showApprove')->name('show-approve-page');
    Route::resource('approve', 'ApproveController');

    Route::get('approve-data', 'ApproveController@store')->name('approve-data');
    Route::get('delivery-approved/{id}', 'ApproveController@deliveryApproved')->name('delivery-approved');
    Route::get('show-challan-approve/{id}', 'ApproveController@showChallanApprove')->name('show-challan-approve');
    Route::get('show-salse-bill-order/{id}', 'ApproveController@showSalesBillOrder')->name('show-salse-bill-order');
    Route::get('show-pos-bill/{id}', 'ApproveController@showPosBill')->name('show-pos-bill');
    Route::get('show-purchase-order/{id}', 'ApproveController@purchaseOrder')->name('show-purchase-order');
    Route::get('stock-itam-commission-data/{id}', 'ApproveController@stockItemCommission')->name('stock-itam-commission-data');
});
