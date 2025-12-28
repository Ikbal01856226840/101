<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\Backend\AuthController::class, 'showLogin'])->name('show-login');
Route::post('login', [App\Http\Controllers\Backend\AuthController::class, 'login'])->name('login')->middleware('throttle:6,2');

Route::group(['middleware' => 'auth'], function () {
    
    Route::get('main-dashboard', [App\Http\Controllers\Backend\DashboardController::class, 'mainIndex'])->name('main-dashboard');
    Route::get('day-wise-sales', [App\Http\Controllers\Backend\DashboardController::class, 'dayWiseSales'])->name('day-wise-sales');
    Route::get('top-sales-return-customer', [App\Http\Controllers\Backend\DashboardController::class, 'topSalesReturnCustomer'])->name('top-sales-return-customer');
    //auth route
    Route::get('register', [App\Http\Controllers\Backend\AuthController::class, 'registerCreate'])->name('register');
    Route::get('user_change_password', [App\Http\Controllers\Backend\AuthController::class, 'show_change_password'])->name('user_change_password');
    Route::post('change_password', [App\Http\Controllers\Backend\AuthController::class, 'change_password'])->name('change_password');
    Route::post('logout', [App\Http\Controllers\Backend\AuthController::class, 'logout'])->name('logout');
    //company route
    Route::resource('company', App\Http\Controllers\Backend\CompanyController::class)->only(['index', 'update']);
    Route::get('company-show', [App\Http\Controllers\Backend\CompanyController::class, 'showCompany'])->name('showCompany');
    //branch
    Route::resource('branch', App\Http\Controllers\Backend\Branch\BranchController::class);
    Route::get('branch-show', [App\Http\Controllers\Backend\Branch\BranchController::class, 'branchShow'])->name('branch-show');
    Route::get('dashboard', [App\Http\Controllers\Backend\DashboardController::class, 'index'])->name('dashboard');

    // setting wise page
    Route::post('page-wise-setting', [App\Http\Controllers\Backend\Setting\PageWiseSettingController::class, 'pageWiseSetting'])->name('page-wise-setting');

     //report setting wise page
     Route::post('report-page-wise-setting', [App\Http\Controllers\Backend\Setting\PageWiseReportSettingController::class, 'reportPageWiseSetting'])->name('page-page-wise-setting');
     
    //error log
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

    Route::match(['get', 'post'], 'stockCalculationUpdate', [App\Http\Controllers\DevController::class, 'stockCalculationUpdate'])->name('stockCalculationUpdate');

});
//cache clear
Route::get('clear_cache', function () {
    //\Artisan::call('key:generate');
    \Artisan::call('config:clear');
    //\Artisan::call('route:cache');
    //\Artisan::call('view:cache');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    //Artisan::call('route:cache');
    dd('Cache is cleared');
});
