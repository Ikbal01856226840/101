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
Route::group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Backend\Report'], function () {

    //dashboard route
    Route::get('report-dashboard', 'DashboardController@index')->name('report-dashboard');
    Route::get('report-ledger-search', 'DashboardController@ledgerSearch')->name('report-ledger-search');

    //DepartmentConsumptionSummary
    Route::get('department-consumption-summary','DepartmentConsumptionSummaryController@index')->name('department-consumption-summary');
    Route::post('get-department-consumption-summary','DepartmentConsumptionSummaryController@getDepartmentConsumptionSummary')->name('get-department-consumption-summary');
    Route::get('department-consumption/{department?}/{from_date?}/{to_date?}/{stock_item_id?}','DepartmentConsumptionSummaryController@departmentConsumption')->name('department-consumption');
    Route::post('get-department-consumption','DepartmentConsumptionSummaryController@getDepartmentConsumption')->name('get-department-consumption');
    
    // StockConsumptionDepartmentWiseSummary
    Route::get('stock-consumption-department-wise-summary','StockConsumptionDepartmentWiseSummaryController@index')->name('stock-consumption-department-wise-summary');
    Route::post('get-stock-consumption-department-wise-summary','StockConsumptionDepartmentWiseSummaryController@getStockConsumptionDepartmentWiseSummary')->name('get-stock-consumption-department-wise-summary');
    Route::get('stock-consumption-department-wise/{stock_item_id?}/{from_date?}/{to_date?}/{department?}','StockConsumptionDepartmentWiseSummaryController@stockConsumptionDepartmentWise')->name('stock-consumption-department-wise');
    Route::post('get-stock-consumption-department-wise','StockConsumptionDepartmentWiseSummaryController@getStockConsumptionDepartmentWise')->name('get-stock-consumption-department-wise');

    Route::get('report/account-ratio', 'AccountRatioController@accountRatio')->name('account-ratio');
    Route::post('report/account-ratio-data', 'AccountRatioController@accountRatioData')->name('account-ratio-data');

    //daybook route
    Route::resource('daybook-report', 'DayBookController');
    Route::get('get-daybook', 'DayBookController@getDayBook');
    Route::get('get-debitOrStock', 'DayBookController@getDebitOrStock');

    // party ledger in details route
    Route::get('report/party-ledger', 'PartyLedgerController@PartyLedgerShow')->name('party-ledger');
    Route::match(['get', 'post'], 'report/party-ledger-get-data', 'PartyLedgerController@PartyLedgerGetData')->name('party-ledger-get-data');
    Route::get('party-ledger/{ledger_id?}/{form_date?}/{to_date?}', 'PartyLedgerController@PartyLedgerIdWise')->name('party-ledger-id-wise');
    Route::get('party-ledger/{ledger_id?}/{form_date?}/{to_date?}/{sort_by?}/{sort_type?}/{description?}/{narratiaon?}/{remarks?}/{user_info?}', 'PartyLedgerController@PartyLedgerIdWiseDetails')->name('party-ledger-id-wise-details');
    Route::get('party-ledger/{ledger_id?}/{form_date?}/{to_date?}/{sort_by?}/{sort_type?}/{description?}/{narratiaon?}/{remarks?}/{user_info?}/{inline_closing_blance?}/{ref_number?}', 'PartyLedgerController@PartyLedgerIdWiseDetails')->name('party-ledger-id-wise-details-search');
    Route::get('party-ledger-store/{ledger_id?}/{form_date?}/{to_date?}/{sort_by?}/{sort_type?}/{description?}/{narratiaon?}/{remarks?}/{user_info?}', 'PartyLedgerController@PartyLedgerIdWiseDetailsStore')->name('party-ledger-id-wise-details-store');
    Route::get('party-ledger-store/{ledger_id?}/{form_date?}/{to_date?}/{sort_by?}/{sort_type?}/{description?}/{narratiaon?}/{remarks?}/{user_info?}/{inline_closing_blance?}/{ref_number?}', 'PartyLedgerController@PartyLedgerIdWiseDetailsStore')->name('party-ledger-id-wise-details-store-search');
    Route::get('party-ledger-details', 'PartyLedgerDetailsController@PartyLedgerInDetailsShow')->name('party-ledger-details');
    Route::match(['get', 'post'],'party-ledger-details-get-data', 'PartyLedgerDetailsController@PartyLedgerInDetailsGetData')->name('party-ledger-details-get-data');
    Route::get('party-ledger-details-store', 'PartyLedgerDetailsController@PartyLedgerInDetailsStoreShow')->name('party-ledger-details-store');
    Route::match(['get', 'post'],'party-ledger-details-store-get-data', 'PartyLedgerDetailsController@PartyLedgerInDetailsStoreGetData')->name('party-ledger-details-store-get-data');
    Route::get('report/party-ledger-details-new', 'PartyLedgerDetailsController@PartyLedgerInDetailsShowNew')->name('party-ledger-details-new');
    Route::match(['get', 'post'],'party-ledger-details-get-data-new', 'PartyLedgerDetailsController@PartyLedgerInDetailsGetDataNew')->name('party-ledger-details-get-data-new');
    Route::get('party-ledger/{ledger_id?}/{form_date?}/{to_date?}/{sort_by?}/{sort_type?}/{description?}/{narratiaon?}/{remarks?}/{user_info?}/{inline_closing_blance?}', 'PartyLedgerController@PartyLedgerIdWiseDetails')->name('party-ledger-id-wise-details-search');
 
    //party ledger contact details route
    Route::get('report/party-ledger-contact-details', 'PartyLedgerDetailsController@PartyLedgerContactDetailsShow')->name('party-ledger-contact-details');
    Route::post('report/party-ledger-contact-details-data', 'PartyLedgerDetailsController@PartyLedgerContactDetailsGetData')->name('party-ledger-contact-details-data');

    // group wise  party ledger in details route
    Route::get('report/group-wise-party-ledger', 'GroupWisePartyLedgerController@groupWisePartyLedgerShow')->name('group-wise-party-ledger');
    Route::match(['get', 'post'], 'report/group-wise-party-ledger-get-data', 'GroupWisePartyLedgerController@groupWisePartyLedgerGetData')->name('group-wise-party-ledger-get-data');
    Route::get('report/group-wise-party-ledger/{group_chart_id?}/{form_date?}/{to_date?}', 'GroupWisePartyLedgerController@groupWisePartyLedgerIdWise')->name('group-wise-party-ledger-id-wise');
    Route::get('report/group-wise-party-ledger-credit-limit', 'GroupWisePartyLedgerController@groupWisePartyLedgerCreditLimitShow')->name('group-wise-party-ledger-credit-limit');
    Route::match(['get', 'post'],'report/group-wise-party-ledger-credit-limit-data', 'GroupWisePartyLedgerController@groupWisePartyLedgerCreditLimitGetData')->name('group-wise-party-ledger-credit-limit-data');

    // cash flow summary route
    Route::get('cash-flow-summary', 'CashFlowSummaryController@CashFlowSummaryDetailsShow')->name('cash-flow-summary');
    Route::match(['get', 'post'], 'cash-flow-summary-get-data', 'CashFlowSummaryController@PartyLedgerInDetailsGetData')->name('cash-flow-summary-get-data');

    //group cash flow route
    Route::get('group-cash-flow', 'GroupCashFlowController@GroupCashFlowDetailsShow')->name('group-cash-flow');
    Route::match(['get', 'post'], 'group-cash-flow-get-data', 'GroupCashFlowController@GroupCashFlowDetailsGetData')->name('group-cash-flow-get-data');
    Route::get('group-cash-flow/{id?}/{form_date?}/{to_date?}', 'GroupCashFlowController@GroupCashFlowDetailsIdWise')->name('group-cash-flow-id-wise');

    //ledger cash flow route
    Route::get('ledger-cash-flow', 'LedgerCashFlowController@LedgerCashFlowDetailsShow')->name('ledger-cash-flow');
    Route::match(['get', 'post'], 'ledger-cash-flow-get-data', 'LedgerCashFlowController@LedgerCashFlowDetailsGetData')->name('ledger-cash-flow-get-data');
    Route::get('ledger-cash-flow/{id?}/{form_date?}/{to_date?}', 'LedgerCashFlowController@LedgerCashFlowDetailsIdWise')->name('ledger-cash-flow-id-wise');

    //voucher register route
    Route::get('report/voucher-register', 'VoucherRegisterController@VoucherRegisterShow')->name('voucher-register');
    Route::match(['get', 'post'],'voucher-register-data', 'VoucherRegisterController@getVoucherRegister')->name('voucher-register-data');
    Route::get('report/voucher-month-id-wise/{voucher_id?}/{date?}/{from_date?}/{to_date?}', 'VoucherRegisterController@VoucherMonthWise')->name('voucher-month-id-wise');
    Route::get('report/voucher-month-id-wise/{voucher_id?}/{date?}/{from_date?}/{to_date?}/{sort_by?}/{debit?}/{credit?}/{narratiaon?}', 'VoucherRegisterController@VoucherMonthWiseDetails')->name('voucher-month-id-wise-details');

    // challan route
    Route::get('report/challan-show', 'ChallanController@index')->name('challan-show');
    Route::post('challan-data', 'ChallanController@getChallan')->name('challan-data');

    // bill route
    Route::get('report/bill-show', 'BillController@index')->name('bill-show');
    Route::match(['get', 'post'],'bill-data', 'BillController@getBill')->name('bill-data');

    // warehousewise stock
    Route::get('report/warehousewise/stock', 'WarehouseWiseStockController@warehouseWiseStockShow')->name('report-warehousewise-stock');
    Route::post('report/warehousewise/stock-data', 'WarehouseWiseStockController@warehouseWiseStock')->name('report-warehousewise-stock-data');
    Route::get('report/warehouse/without/damage/stock', 'WarehouseWiseStockController@warehouseWithoutDamageGodownWiseStockShow')->name('report-warehouse-without-damage-stock');

    // stock item register
    Route::get('report/stock-item-register', 'StockItemRegisterController@StockItemRegisterShow')->name('report-stock-item-register');
    Route::match(['get', 'post'], 'report/stock-item-register-data', 'StockItemRegisterController@StockItemRegister')->name('stock-item-register-data');
    Route::get('stock-item-select-option-tree', 'StockItemRegisterController@StockItemSelectOptionTree')->name('stock-item-select-option-tree');
    Route::get('stock-ledger-select-option-tree', 'StockItemRegisterController@StockLedgerSelectOptionTree')->name('stock-ledger-select-option-tree');
    Route::get('report/stock-item-register-daily/{date?}/{stock_item_id?}/{godown_id?}', 'StockItemRegisterController@StockItemRegisterDayWise')->name('stock-item-register-daily-id-wise');
    Route::get('report/stock-item-register-monthly/{date?}/{stock_item_id?}/{godown_id?}', 'StockItemRegisterController@StockItemRegisterMonthWise')->name('stock-item-register-monthly-id-wise');

    // stock item register store
    Route::get('report/stock-item-register-store', 'StockItemRegisterStoreController@StockItemRegisterStoreShow')->name('report-stock-item-register-store');
    Route::match(['get', 'post'], 'report/stock-item-register-store-data', 'StockItemRegisterStoreController@StockItemRegisterStore')->name('stock-item-register-store-data');
    Route::get('report/stock-item-register-store-monthly/{date?}/{stock_item_id?}/{godown_id?}', 'StockItemRegisterStoreController@StockItemRegisterStoreMonthWise')->name('stock-item-register-store-monthly-id-wise');

    // stock item daily summary
    Route::get('report/stock-item-daily-summary', 'StockItemDailySummaryController@stockItemDailySummaryShow')->name('stock-item-daily-summary');
    Route::match(['get', 'post'], 'report/stock-item-daily-summary-data', 'StockItemDailySummaryController@stockItemDailySummary')->name('stock-item-daily-summary-data');
    Route::get('report/stock-item-daily-id-wise/{date?}/{stock_item_id?}/{godown_id?}', 'StockItemDailySummaryController@StockItemDailyIdWise')->name('stock-item-daily-id-wise');

    // stock item monthly summary
    Route::get('report/stock-item-monthly-summary', 'StockItemMonthlySummaryController@stockItemMontlySummaryShow')->name('stock-item-monthly-summary');
    Route::match(['get', 'post'], 'report/stock-item-monthly-summary-data', 'StockItemMonthlySummaryController@stockItemMontlySummary')->name('stock-item-monthly-summary-data');
    Route::get('report/stock-item-monthly-summary/{id?}/{form_date?}/{to_date?}/{godown_id?}', 'StockItemMonthlySummaryController@stockItemMontlySummaryWise')->name('stock-item-monthly-summary-id-wise');

    // stock item monthly summary store
    Route::get('report/stock-item-monthly-summary-store', 'StockItemMonthlySummaryStoreController@stockItemMontlySummaryStoreShow')->name('stock-item-monthly-summary-store');
    Route::match(['get', 'post'], 'report/stock-item-monthly-summary-store-data', 'StockItemMonthlySummaryStoreController@stockItemMontlySummaryStore')->name('stock-item-monthly-summary-store-data');
    Route::get('report/stock-item-monthly-summary-store/{id?}/{form_date?}/{to_date?}/{godown_id?}', 'StockItemMonthlySummaryStoreController@stockItemMontlySummaryStoreWise')->name('stock-item-monthly-summary-store-id-wise');

    // stock group summary
    Route::get('report/stock-group-summary', 'StockGroupSummaryController@stockGroupSummaryShow')->name('report-stock-group-summary');
    Route::match(['get', 'post'],'report/stock-group-summary-data', 'StockGroupSummaryController@stockGroupSummary')->name('report-stock-group-summary-data');
    Route::get('report/stock-group-summary-profit-value/{type?}/{form_date?}/{to_date?}', 'StockGroupSummaryController@stockGroupSummaryProfitValue')->name('report-stock-group-summary-profit-value');
    Route::get('report/stock-group-summary-id-wise/{id?}/{form_date?}/{to_date?}/{godown_id?}', 'StockGroupSummaryController@stockGroupSummaryIdWise')->name('report-stock-group-summary-id-wise');
    Route::get('report/stock-group-summary-new', 'StockGroupSummaryController@stockGroupSummaryNewShow')->name('report-stock-group-summary-new');
     Route::match(['get', 'post'],'report/stock-group-summary-data-new', 'StockGroupSummaryController@stockGroupSummaryNew')->name('report-stock-group-summary-data-new');

     // stock group summary store
     Route::get('report/stock-group-summary-store', 'StockGroupSummaryStoreController@stockGroupSummaryStoreShow')->name('report-stock-group-summary-store');
     Route::match(['get', 'post'],'report/stock-group-summary-store-data', 'StockGroupSummaryStoreController@stockGroupSummaryStore')->name('report-stock-group-summary-store-data');
     Route::get('report/stock-group-summary-store-id-wise/{id?}/{form_date?}/{to_date?}/{godown_id?}', 'StockGroupSummarySoreController@stockGroupSummaryStoreIdWise')->name('report-stock-group-summary-store-id-wise');
     Route::get('report/stock-group-summary-short-store', 'StockGroupSummaryStoreController@stockGroupSummaryShortStoreShow')->name('stock-group-summary-short-store');
     Route::match(['get', 'post'],'report/stock-group-summary-short-store-data', 'StockGroupSummaryStoreController@stockGroupSummaryShortStore')->name('report-stock-group-summary-short-store-data');

    // current stock
    Route::get('report/current-stock', 'CurrentStockController@currentStockShow')->name('report-current-stock');
    Route::post('report/current-stock-data', 'CurrentStockController@currentStockStock')->name('report-current-stock-data');

    //stock group analysis
    Route::get('report/stock-group-analysis', 'StockGroupAnalysisController@stockGroupAnalysisShow')->name('report-stock-group-analysis');
    Route::match(['get', 'post'], 'report/stock-group-analysis-data', 'StockGroupAnalysisController@stockGroupAnalysis')->name('stock-group-analysis-data');
    Route::get('report/stock-group-analysis/{stock_group_id?}/{godown_id?}/{form_date?}/{to_date?}/{purchase_in?}/{grn_in?}/{purchase_return_in?}/{journal_in?}/{stock_journal_in?}/{sales_return_out?}/{gtn_out?}/{sales_out?}/{journal_out?}/{stock_journal_out?}', 'StockGroupAnalysisController@stockGroupAnalysisIdWise')->name('stock-group-analysis-id-wise');

    //stock item analysis
    Route::get('report/stock-item-analysis', 'StockItemAnalysisController@stockItemAnalysisShow')->name('report-stock-item-analysis');
    Route::match(['get', 'post'], 'report/stock-item-analysis-data', 'StockItemAnalysisController@stockItemAnalysis')->name('stock-item-analysis-data');
    Route::get('report/stock-item-analysis/{stock_item_id?}/{godown_id?}/{form_date?}/{to_date?}/{purchase_in?}/{grn_in?}/{purchase_return_in?}/{journal_in?}/{stock_journal_in?}/{sales_return_out?}/{gtn_out?}/{sales_out?}/{journal_out?}/{stock_journal_out?}', 'StockItemAnalysisController@stockItemAnalysisIdWise')->name('stock-item-analysis-id-wise');

    //stock item analysis details
    Route::get('report/stock-item-analysis-details', 'StockItemAnalysisDetailsController@stockItemAnalysisDetailsShow')->name('report-stock-item-analysis-details');
    Route::match(['get', 'post'], 'report/stock-item-analysis-details-data', 'StockItemAnalysisDetailsController@stockItemAnalysisDetails')->name('stock-item-analysis-details-data');
    Route::get('report/stock-item-analysis-details/{ledger_head_id?}/{stock_item_id?}/{godown_id?}/{form_date?}/{to_date?}/{purchase_in?}/{grn_in?}/{purchase_return_in?}/{journal_in?}/{stock_journal_in?}/{sales_return_out?}/{gtn_out?}/{sales_out?}/{journal_out?}/{stock_journal_out?}/{sort_by?}/{stort_type?}', 'StockItemAnalysisDetailsController@stockItemAnalysisDetailsIdWise')->name('stock-item-analysis-details-id-wise');

    //actual sales
    Route::get('report/actual_sales', 'ActualSalesController@actualSalesShow')->name('report-actual_sales');
    Route::match(['get', 'post'], 'report/actual_sales-data', 'ActualSalesController@actualSales')->name('actual_sales-data');
    Route::get('report/actual-sales-details/{godown_id?}/{stock_item_id?}/{form_date?}/{to_date?}', 'ItemVoucherAnalysisController@itemVoucherAnalysisDetails')->name('actual-sales-details');

    // ledger analysis
    Route::get('report/ledger-analysis', 'LedgerAnalysisController@ledgerAnalysisShow')->name('report-ledger-analysis');
    Route::match(['get','post'], 'report/ledger-analysis-data', 'LedgerAnalysisController@ledgerAnalysis')->name('report-ledger-analysis-data');
    Route::get('report/ledger-analysis-details/{stock_item_id?}/{ledger_id?}/{form_date?}/{to_date?}/{purchase_in?}/{grn_in?}/{purchase_return_in?}/{journal_in?}/{stock_journal_in?}/{sales_return_out?}/{gtn_out?}/{sales_out?}/{journal_out?}/{stock_journal_out?}', 'ItemVoucherAnalysisController@itemVoucherAnalysisLedgerDetails')->name('ledger-analysis-details');

    // item voucher analysis
    Route::get('report/item-voucher-analysis', 'ItemVoucherAnalysisController@itemVoucherAnalysisShow')->name('report-item-voucher-analysis');
    Route::match(['get','post'], 'report/litem-voucher-analysis-data', 'ItemVoucherAnalysisController@itemVoucherAnalysis')->name('report-item-voucher-analysis-data');

    // trail balance
    Route::get('report/trial-balance', 'TrialBalanceController@trialBalanceShow')->name('report-trial-balance');
    Route::match(['get', 'post'], 'report/trial-balance-data', 'TrialBalanceController@trialBalance')->name('trial-balance-data');

    //account group summary
    Route::get('report/account-group-summary', 'AccountGroupSummaryController@accountGroupSummaryShow')->name('report-account-group-summary');
    Route::match(['get', 'post'], 'report/account-group-summary-data', 'AccountGroupSummaryController@accountGroupSummary')->name('account-group-summary-data');
    Route::get('report/account-group-summary/{group_chart_id?}/{form_date?}/{to_date?}', 'AccountGroupSummaryController@accountGroupSummaryIdWise')->name('account-group-summary-id-wise');
    Route::get('report/account-group-summary-without-opening-blance/{group_chart_id?}/{form_date?}/{to_date?}', 'AccountGroupSummaryController@accountGroupSummaryWithOutOpeningBlanceIdWise')->name('account-group-summary-without-opening-blance-id-wise');

    //account ledger Daily summary
    Route::get('report/account-ledger-daily-summary', 'AccountLedgerDailySummaryController@accountLedgerDailySummaryShow')->name('account-ledger-daily-summary');
    Route::match(['get', 'post'], 'report/account-ledger-daily-summary-data', 'AccountLedgerDailySummaryController@accountLedgerDailySummary')->name('account-ledger-daily-summary-data');

    //account ledger monthly summary
    Route::get('report/account-ledger-monthly-summary', 'AccountLedgerMonthlySummaryController@accountLedgerMonthlySummaryShow')->name('account-ledger-monthly-summary');
    Route::match(['get', 'post'], 'report/account-ledger-monthly-summary-data', 'AccountLedgerMonthlySummaryController@accountLedgerMonthlySummary')->name('account-ledger-monthly-summary-data');
    Route::get('report/account-ledger-monthly-summary/{ledger_id?}/{form_date?}/{to_date?}', 'AccountLedgerMonthlySummaryController@accountLedgerMonthlySummaryIdWise')->name('account-ledger-monthly-summary-id-wise');

    //account ledger voucher
    Route::get('report/account-ledger-voucher', 'AccountLedgerVoucherController@accountLedgerVoucherShow')->name('account-ledger-voucher');
    Route::match(['get', 'post'], 'report/account-ledger-voucher-data', 'AccountLedgerVoucherController@accountLedgerVoucher')->name('account-ledger-voucher-data');
    Route::get('report/account-ledger-voucher/{ledger_id?}/{form_date?}/{to_date?}', 'AccountLedgerVoucherController@accountLedgerVoucherIdWise')->name('account-ledger-voucher-id-wise');
    Route::get('report/account-ledger-voucher-month/{ledger_id?}/{date?}', 'AccountLedgerVoucherController@accountLedgerVoucherMonthWise')->name('account-ledger-voucher-month-id-wise');
    Route::get('report/account-ledger-voucher-day/{ledger_id?}/{date?}', 'AccountLedgerVoucherController@accountLedgerVoucherDayhWise')->name('account-ledger-voucher-day-id-wise');
    Route::get('report/account-ledger-voucher-details/{ledger_id?}/{from_date?}/{to_date?}/{voucher_id?}/{narration?}', 'AccountLedgerVoucherController@accountLedgerVoucherDetails')->name('account-ledger-voucher-details-id-wise');

    // Bank Book
    Route::get('report/bank-book', 'BalanceSheetController@bankBookShow')->name('report-bank-book');
    Route::match(['get', 'post'], 'report/bank-book-data', 'BalanceSheetController@bankBook')->name('report-bank-book-data');

    // balance sheet
    Route::get('report/balance-sheet', 'BalanceSheetController@BalanceSheetShow')->name('report-balance-sheet');
    Route::match(['get', 'post'], 'report/balance-sheet-data', 'BalanceSheetController@BalanceSheet')->name('balance-sheet-data');

    // profit loss
    Route::get('report/profit-loss', 'ProfitLossController@profitLossShow')->name('report-profit-loss');
    Route::match(['get', 'post'], 'report/profit-loss-data', 'ProfitLossController@profitLoss')->name('profit-loss-data');

    //account group analysis
    Route::get('report/account-group-analysis', 'AccountGroupAnalysisController@AccountGroupAnalysisShow')->name('report-account-group-analysis');
    Route::match(['get','post'], 'report/account-group-analysis-data', 'AccountGroupAnalysisController@AccountGroupAnalysis')->name('account-group-analysis-data');
    Route::get('report/accounts-group-analysis-details/{stock_item_id?}/{group_id?}/{form_date?}/{to_date?}/{purchase_in?}/{grn_in?}/{purchase_return_in?}/{journal_in?}/{stock_journal_in?}/{sales_return_out?}/{gtn_out?}/{sales_out?}/{journal_out?}/{stock_journal_out?}', 'ItemVoucherAnalysisGroupController@itemVoucherAnalysisGroupDetails')->name('accounts-group-analysis-details');

    // item voucher analysis group
    Route::get('report/item-voucher-analysis-group', 'ItemVoucherAnalysisGroupController@itemVoucherAnalysisGroupShow')->name('report-item-voucher-analysis-group');
    Route::match(['get','post'], 'report/litem-voucher-analysis-group-data', 'ItemVoucherAnalysisGroupController@itemVoucherAnalysisGroup')->name('report-item-voucher-analysis-group-data');

    // sales order
    Route::get('report/sales-order', 'SalesOrderController@salesOrderShow')->name('report-sales-order');
    Route::match(['get','post'],'report/sales-order-data_ot', 'SalesOrderController@salesOrder')->name('report-sales-order-data');
    Route::get('report/sales-order-vouchere/{id}', 'SalesOrderController@voucherExchangeShow')->name('report-sales-order-voucher');
    Route::get('report/sales-order-list', 'SalesOrderController@salesOrderListShow')->name('sales-order-list');
    Route::match(['get','post'],'report/report-sales-order-list-user-wise-data', 'SalesOrderController@salesOrderListUserWiseData')->name('report-sales-order-list-user-wise-data');

    //pos
    Route::get('report/sales-list', 'POSController@salesListShow')->name('report-sales-list');
    Route::match(['get','post'],'report/sales-list-data', 'POSController@salesList')->name('report-sales-list-data');
    Route::get('report/pos-currnt-stock', 'POSController@posCurrentStockShow')->name('pos-currnt-stock');

    //voucher filter
    Route::get('report/voucher-filter', 'VoucherFilterAnalysisController@voucherFilterAnalysisShow')->name('report-voucher-filter');
    Route::match(['get','post'],'report/voucher-filter-data', 'VoucherFilterAnalysisController@voucherFilterAnalysis')->name('report-voucher-filter-data');

     //account Ledger Voucher Register
     Route::get('report/account-ledger-voucher-register', 'AccountLedgerVoucherRegisterController@ledgerVoucherRegisterShow')->name('report-account-ledger-voucher-register');
     Route::match(['get','post'],'report/account-ledger-voucher-register-data', 'AccountLedgerVoucherRegisterController@ledgerVoucherRegister')->name('report-account-ledger-voucher-register-data');
     Route::get('report/ledger-voucher-register/{from_date?}/{to_date?}/{voucher_id?}', 'AccountLedgerVoucherRegisterController@ledgerVoucherWiseRegister')->name('report-stock-item-voucher-wise-register');

     //account Ledger do history
     Route::get('report/account-ledger-do-history', 'AccountLedgerDOHistoryController@ledgerDoHistoryShow')->name('report-account-ledger-do-history');
     Route::match(['get','post'],'report/account-ledger-do-history-data', 'AccountLedgerDOHistoryController@ledgerDoHistory')->name('account-ledger-do-history-data');

     //account ledger analysis with sales type
    Route::get('report/account-ledger-analysis-with-sales-type', 'AccountLedgerAnalysisWithSalesTypeController@accountLedgerAnalysisWithSalesTypeShow')->name('report-account-ledger-analysis-with-sales-type');
    Route::match(['get','post'],'report/account-ledger-analysis-with-sales-type-data', 'AccountLedgerAnalysisWithSalesTypeController@accountLedgerAnalysisWithSalesType')->name('report-account-ledger-analysis-with-sales-type-data');

    //account group analysis with sales type
    Route::get('report/account-group-analysis-with-sales-type', 'AccountGroupAnalysisWithSalesTypeController@accountGroupAnalysisWithSalesTypeShow')->name('report-account-group-analysis-with-sales-type');
    Route::match(['get','post'],'report/account-group-analysis-with-sales-type-data', 'AccountGroupAnalysisWithSalesTypeController@accountGroupAnalysisWithSalesType')->name('report-account-group-analysis-with-sales-type-data');

    //retrailer ledger deatils
    Route::get('report/retrailer-ledger-details', 'RetrailerLedgerDetailsController@retrailerLedgerDetailsShow')->name('report-ledger-details-retrailer');
    Route::match(['get','post'],'report/retrailer-ledger-details-data', 'RetrailerLedgerDetailsController@retrailerLedgerDetailsGetData')->name('report-ledger-details-retrailer-data');

    // stock Voucher Register
    Route::get('report/stock-voucher-register', 'StockVoucherRegisterController@stockVoucherRegisterShow')->name('report-stock-voucher-register');
    Route::match(['get','post'],'report/stock-voucher-register-data', 'StockVoucherRegisterController@stockVoucherRegister')->name('report-stock-voucher-register-data');
    Route::get('report/stock-item-voucher-register/{stock_item_id?}/{godown_id?}/{form_date?}/{to_date?}/{voucher_id?}', 'StockVoucherRegisterController@stockItemVoucherRegister')->name('report-stock-item-voucher-register');
    Route::get('report/voucher-list-to-stock-voucher-register/{form_date?}/{to_date?}/{voucher_id?}', 'StockVoucherRegisterController@VoucherListStockItemVoucherRegister')->name('voucher-list-to-stock-voucher-register');

    // unused stock
    Route::get('report/stock-item-unnsed-stock', 'UnusedStockController@unusedStockShow')->name('report-stock-item-unnsed-stock');
    Route::match(['get','post'],'report/stock-item-unnsed-stock-data', 'UnusedStockController@unusedStock')->name('report-stock-item-unnsed-stock-data');

    // godown wise stock analysis
    Route::get('report/grodown-wise-stock-analysis', 'GrodownWiseStockAnalysisController@grodownWiseStockAnalysisShow')->name('report-grodown-wise-stock-analysis');
    Route::match(['get','post'],'report/grodown-wise-stock-analysis-data', 'GrodownWiseStockAnalysisController@grodownWiseStockAnalysis')->name('report-grodown-wise-stock-analysis-data');

    // company statistics
    Route::get('report/company-statistics', 'CompanyStatisticsController@companyStatisticsShow')->name('report-company-statistics');
    Route::match(['get','post'],'report/company-statistics-data', 'CompanyStatisticsController@companyStatistics')->name('report-company-statistics-data');

     // voucher monthly summary
     Route::get('report/voucher-monthly-summary', 'VoucherMonthlySummaryController@voucherMonthlySummaryShow')->name('report-voucher-monthly-summary');
     Route::match(['get','post'],'report/report/voucher-monthly-summary-data', 'VoucherMonthlySummaryController@voucherMonthlySummary')->name('report-voucher-monthly-summary-data');
     Route::get('report/company-statistics-monthly/{voucher_id?}/{form_date?}/{to_date?}', 'VoucherMonthlySummaryController@companyStatisticsMonthly')->name('report-company-statistics-monthly-data');

    //Goods in Transit
    Route::get('report/goods-in-transit', 'GoodsinTransitController@index')->name('report-goods-in-transit');
    Route::match(['get','post'],'report/goods-in-transit-data', 'GoodsinTransitController@getGoodsinTransit')->name('report-goods-in-transit-data');
    Route::match(['get','post'],'report/goods-in-transit-receive-data', 'GoodsinTransitController@getGoodsinTransitReceiveData')->name('report-goods-in-transit-receive-data');

    // Invoice Summary
    Route::get('report/invoice-summary', 'InvoiceSummaryController@invoiceSummaryShow')->name('report-invoice-summary');
    Route::match(['get','post'],'report/invoice-summary-data', 'InvoiceSummaryController@invoiceSummaryGetData')->name('report-invoice-summary-data');

    //Purchase Order
    Route::get('report/purchase-order', 'PurchaseOrderController@PurchaseOrderShow')->name('report-purchase-order');
    Route::match(['get','post'],'report/purchase-order-data', 'PurchaseOrderController@PurchaseOrder')->name('report-purchase-order-data');

    // Bank Reconciliation
    Route::controller('BankReconciliationController')->group(function(){
        Route::get('report/bank-reconciliation', 'bankReconciliationShow')->name('report-bank-reconciliation');
        Route::match(['get','post'],'report/bank-reconciliation-data', 'bankReconciliation')->name('report-bank-reconciliation-data');
        Route::match(['get','post'],'report/bank-reconciliation-date-store', 'bankReconciliationStore')->name('report-bank-reconciliation-date-store');
    });

     //account group monthly summary
     Route::get('report/account-group-monthly-summary', 'AccountGroupMontlySummaryController@accountGroupMonthlySummaryShow')->name('report-group-monthly-summary');
     Route::match(['get','post'],'report/account-group-monthly-summary-data', 'AccountGroupMontlySummaryController@accountGroupMonthlySummary')->name('report-account-group-monthly-summary-data');



    Route::prefix('report/voucher')->name('report.voucher.')->group(function(){
        Route::controller('VoucherNumberModifyController')->prefix('number/modify')->name('number.modify.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::match(['get','post'],'data', 'voucherNumberModify')->name('data');
            Route::post('store', 'voucherNumberModifyStore')->name('store');
        });
        Route::controller('VoucherExchangeController')->prefix('exchange')->name('exchange.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::match(['get','post'],'data', 'voucherExchange')->name('data');
            Route::get('{voucher_id?}/{tran_id?}', 'voucherDuplicate')->name('duplicate');
        });
        Route::controller('VoucherModeChangeController')->prefix('mode/change')->name('mode.change.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::match(['get','post'],'data', 'voucherModeChange')->name('data');
            Route::post('store', 'voucherModeChangeStore')->name('store');
        });
        Route::controller('VoucherSearchController')->prefix('search')->name('search.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::match(['get','post'],'data', 'voucherSearch')->name('data');
        });
    });

         //Dealer and Retrailer Quantity
    Route::controller('DealerAndRetailerSalesQuantityController')->group(function(){
        Route::get('report/dealer-retrailer-sales-quantity', 'dealerAndRetailerSalesQuantityShow')->name('dealer-retrailer-sales-quantity');
        Route::match(['get','post'],'report/dealer-retrailer-sales-quantity-data', 'dealerAndRetailerSalesQuantityGetData')->name('dealer-retrailer-sales-quantity-data');
        Route::get('report/retailer-actual-sales', 'retailerActualSalesShow')->name('retailer-actual-sales');
        Route::match(['get','post'],'report/retailer-actual-sales-data', 'RetailerActualSalesGetData')->name('retrailer-actual-sales-data-data');
        Route::get('report/current-stock', 'currentStockShow')->name('current-stock');
        Route::match(['get','post'],'report/current-stock-data', 'currentStockGetData')->name('current-stock-data');

    });

         //account group voucher wise analysis
    Route::controller('AccountGroupVoucherWiseAnalysisController')->group(function(){
        Route::get('report/account-group-voucher-wise-analysis', 'accountGroupVoucherWiseAnalysisShow')->name('report-account-group-voucher-wise-analysis');
        Route::match(['get','post'],'report/account-group-voucher-wise-analysis-data', 'accountGroupVoucherWiseAnalysis')->name('report-account-group-voucher-wise-analysis-data');
    });

    Route::controller('ErrorConsoleController')->group(function(){
        Route::get('report/error-console', 'ErrorConsoleController@ErrorConsoleShow')->name('error-console');
        Route::match(['get','post'],'report/error-console-data', 'ErrorConsoleController@ErrorConsoleGetData')->name('error-console-data');
    });
    //Party ledger closed balance
    Route::controller('PartyLedgerClosingBalanceController')->group(function(){
        Route::get('report/party-ledger-closed-balance', 'PartyLedgerClosingBalanceShow')->name('report-party-ledger-closed-balanc');
        Route::match(['get','post'],'report/party-ledger-closed-balanc-data', 'PartyLedgerClosingBalanceGetData')->name('report-party-ledger-closed-balanc-data');
    });

        //Retrailer Analysis
    Route::controller('RetrailerAnalysisController')->group(function(){
        Route::get('report/retrailer-analysis', 'retrailerAnalysisShow')->name('report-retrailer-analysis');
        Route::match(['get','post'],'report/retrailer-analysis-data', 'retraileAnalysisGetData')->name('report-retrailer-analysis-data');

    });


    Route::get('searching-party-ledger-name', 'DashboardController@searchingPartyLedgerName')->name('searching-party-ledger-name');

});
