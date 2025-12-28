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

Route::group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Backend\Master'], function () {

    Route::get('master-dashboard', 'DashboardController@index')->name('master-dashboard');
    
    //group chart route
    Route::resource('group-chart', 'GroupChartController');
    Route::get('group-chart_view/tree_view', 'GroupChartController@treeView');
    Route::get('group-chart_view/plain_view', 'GroupChartController@planView');
    Route::get('get_nature_group', 'GroupChartController@getNatureGroup')->name('get_nature_group');

    //ledger route
    Route::resource('ledger', 'LedgerController');
    Route::get('ledger_view/tree_view', 'LedgerController@treeView');
    Route::get('ledger_view/plain_view', 'LedgerController@planView');
    Route::post('ledger-searching-data', 'LedgerController@searchingData')->name('ledger-searching-data');
    Route::post('alias-update', 'LedgerController@aliasUpdate')->name('alias-update');
    Route::get('ledger/edit-data/{id}', 'LedgerController@editdata')->name('ledger-edit-data');
    

    Route::get('get-auto-alias','LedgerController@getAutoAlias')->name('get-auto-alias');
    Route::get('get-last-manual-alias','LedgerController@getLastManualAlias')->name('get-last-manual-alias');
    Route::get('duplicate-alias-check','LedgerController@duplicateAliasCheck');
    Route::get('set-alias','LedgerController@setAlias')->name('set-alias');
    Route::get('get-set-alias-data/{id}/{alias_type}', 'LedgerController@getSetAliasData')->name('get-set-alias-data');
    
    //voucher route
    Route::resource('voucher', 'VoucherController');
    Route::get('voucher_view', 'VoucherController@getVoucher');

    //godown route
    Route::resource('godown', 'GodownController');
    Route::get('godown_view', 'GodownController@getGodown');

    //distributor center route
    Route::resource('distribution', 'DistributionCenterController');
    Route::get('distribution_view', 'DistributionCenterController@getDistributionCenter');

    //discount offer pos route
    Route::resource('discount-offer-pos', 'DiscountOfferPOSController');
    Route::get('discount-offer-pos-view', 'DiscountOfferPOSController@getDiscountOfferPOS');
    
    //component route
    Route::resource('components', 'ComponentsController');
    Route::get('components_view', 'ComponentsController@getComponents');

    // stock group route
    Route::resource('stock-group', 'StockGroupController');
    Route::get('stock-group_view/tree-view', 'StockGroupController@treeView');
    Route::get('stock-group_view/plain-view', 'StockGroupController@planView');

    // stock item route
    Route::resource('stock-item', 'StockItemController');
    Route::get('stock-item_view/tree-view', 'StockItemController@treeViewItem');
    Route::get('stock-item_view/plain-view', 'StockItemController@planViewItem');

    // stock price route
    Route::resource('stock-item-price', 'StockItemPriceController');
    Route::post('stock-item-price/tree-view', 'StockItemPriceController@treeView');

    //size route
    Route::resource('size', 'SizeController');
    Route::get('size_view', 'SizeController@getSize');

    //measure route
    Route::resource('measure', 'MeasureController');
    Route::get('measure_view', 'MeasureController@getMeasure');

    //customer route
    Route::resource('customer', 'CustomerController');
    Route::get('customer_view', 'CustomerController@getCustomer');

    // supplier route
    Route::resource('supplier', 'SupplierController');
    Route::get('supplier_view', 'SupplierController@getSupplier');

    //stock commission route
    Route::resource('stock-commission', 'StockCommissionController');
    Route::post('stock-commission/tree-view', 'StockCommissionController@treeView');

    //stock  item commission route
    Route::resource('stock-item-commission', 'StockItemCommissionController');
    Route::post('stock-item-commission/tree-view', 'StockItemCommissionController@treeView');

    // stock item opening
    Route::resource('stock-item-opening', 'StockItemOpeningController');
    Route::post('stock-item-opening/tree-view', 'StockItemOpeningController@treeView');

    // bill of material
    Route::resource('bill-of-material', 'BillOffMaterialController');
    Route::get('bill-of-material/get/data', 'BillOffMaterialController@getBillOffMaterialData');

    //stock group price route
    Route::resource('stock-group-price', 'StockGroupPriceController');
    Route::post('stock-group-price/tree-view', 'StockGroupPriceController@treeView');

});
