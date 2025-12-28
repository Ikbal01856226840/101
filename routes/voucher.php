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
Route::group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Backend\Voucher'], function () {

    //route dashboard
    Route::get('voucher-dashboard', 'DashboardController@index')->name('voucher-dashboard');

    //route purchase
    Route::resource('voucher-purchase', 'PurchaseController');
    Route::get('edit/purchase/stock/in', 'PurchaseController@purchaseStockIn');
    Route::get('searching-data', 'PurchaseController@searchingDataGet');
    Route::get('searching-item-data', 'PurchaseController@searchingDataGet')->name('searching-item-data');
    Route::get('searching-stock-item-price', 'PurchaseController@searchingStockItemPrice')->name('searching-stock-item-price');
    Route::get('searching-ledger', 'PurchaseController@searchingLedger')->name('searching-ledger');

    //route receipt
    Route::resource('voucher-receipt', 'ReceiptController');
    Route::get('voucher-receipt/edit/{id}', 'ReceiptController@edit')->name('voucher-receipt-edit');
    Route::get('edit/debit-credit', 'ReceiptController@editDebitCredit');
    Route::get('searching-ledger-data', 'ReceiptController@searchingLedgerDataGet')->name('searching-ledger-data');
    Route::get('searching-stock-item', 'SalesController@searchingStockItem')->name('searching-stock-item');
    Route::get('searching-stock-item-name', 'SalesController@searchingStockItemName')->name('searching-stock-item-name');
    Route::get('get/balance/debit-credit', 'ReceiptController@balanceDebitCredit')->name('balance-debit-credit');
    Route::post('voucher-receipt-cancel/{id}', 'ReceiptController@Cancel');

    //route payment
    Route::resource('voucher-payment', 'PaymentController');
    Route::post('voucher-payment-cancel/{id}', 'PaymentController@Cancel');

    //route contra
    Route::resource('voucher-contra', 'ContraController');
    Route::post('voucher-contra-cancel/{id}', 'ContraController@Cancel');

    //route Grn
    Route::resource('voucher-grn', 'GrnController');
    Route::post('voucher-grn-cancel/{id}', 'GrnController@Cancel');
    Route::get('voucher-duplicate-check', 'GrnController@duplicateVoucherCheck');

    //route sales
    Route::resource('voucher-sales', 'SalesController');
    Route::get('current-stock', 'SalesController@currentStock');
    Route::get('edit/stock/out', 'SalesController@stockOut');
    Route::get('searching-ledger-debit', 'SalesController@searchingLedgerDebit')->name('searching-ledger-debit');
    Route::post('voucher-sales-cancel/{id}', 'SalesController@Cancel');

    //route gtn
    Route::resource('voucher-gtn', 'GtnController');
    Route::post('voucher-gtn-cancel/{id}', 'GtnController@Cancel');

    // route purchase return
    Route::resource('voucher-purchase-return', 'PurchaseReturnController');

    // route transfer return
    Route::resource('voucher-transfer', 'TransferController');
    Route::get('voucher-stock-in-out', 'TransferController@stockOut_with_stockIn');
    Route::post('voucher-transfer-cancel/{id}', 'TransferController@Cancel');

    //route sales return
    Route::resource('voucher-sales-return', 'SalesReturnController');
    Route::post('voucher-sales-return-cancel/{id}', 'SalesReturnController@Cancel');

    // route stock journal
    Route::resource('voucher-stock-journal', 'StockJournalController');
    Route::get('edit/stock/in/with/current_stock', 'StockJournalController@stockIn_with_current_stock');
    Route::get('destination_searching-stock-item-price', 'StockJournalController@destinationPrice')->name('destination_searching-stock-item-price');
    Route::post('voucher-stock-journal-cancel/{id}', 'StockJournalController@Cancel');
    Route::get('bill-of-material-searching', 'StockJournalController@billOfMaterialSearching')->name('bill-of-material-searching');
    Route::get('bill-of-material-qty', 'StockJournalController@billOfMaterialQty')->name('bill-of-material-qty');

    // route journal
    Route::resource('voucher-journal', 'JournalController');
    Route::get('voucher-journal-edit', 'JournalController@getDebitCreditAndStockInStockOut');
    Route::get(' journal-data', 'JournalController@getJournalData');
    Route::post('voucher-journal-cancel/{id}', 'JournalController@Cancel');
    
    // route commission
    Route::resource('voucher-commission', 'CommissionController');
    Route::post('show-commission', 'CommissionController@showCommission');
    Route::post('show-commission-edit', 'CommissionController@showEditCommission');
    Route::get('product_name_get', 'PurchaseController@getProductName');
    Route::post('voucher-commission-cancel/{id}', 'CommissionController@Cancel');
    
    // sales order
    Route::resource('voucher-sales-order', 'SalesOrderController');
    Route::get('sales-order-data', 'SalesOrderController@saleOrderData');
    Route::get('voucher-exchange/{id?}/{tran_id?}', 'SalesOrderController@VoucherExchance')->name('voucher-exchange');
    Route::post('voucher-exchange-store', 'SalesOrderController@VoucherExchanceStore')->name('voucher-exchange-store');

    // route in line search ledger name
    Route::get('ledger_name', 'PurchaseController@inlineSearchLedgerName')->name('ledger_name');

    // pos
    Route::resource('voucher-pos', 'PosController');
    Route::get('pos-stock-item-price', 'PosController@posItemPrice')->name('pos-stock-item-price');
    Route::post('voucher-pos-cancel/{id}', 'PosController@Cancel');

    //exchange
    Route::resource('voucher-pos-exchange', 'ExchangeController');
    Route::get('voucher-exchange-invoice', 'ExchangeController@voucherExchangeInvoice')->name('voucher-exchange-invoice');

     //Order Requisition
     Route::resource('voucher-order-requisition', 'OrderRequisitionController');
     Route::match(['get','post'],'voucher-order-requisition-show-data', 'OrderRequisitionController@RequisitionShowData')->name('report-purchase-order-show-data');

     //   Backward And Forward
     Route::get('voucher-backward-forward-data', 'PurchaseController@BackwardAndForward')->name('voucher-backward-forward-data');
});
