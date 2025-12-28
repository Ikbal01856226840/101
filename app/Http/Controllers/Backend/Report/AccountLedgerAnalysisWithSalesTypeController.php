<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountLedgerAnalysisWithSaleTypeRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use Exception;
use Illuminate\Http\Request;

class AccountLedgerAnalysisWithSalesTypeController extends Controller
{

    private $accountLedgerAnalysisWithSaleTypeRepository;
    private $godownRepository;
    private $stockGroupRepository;

    public function __construct(StockGroupRepository $stockGroupRepository,GodownRepository $godownRepository,AccountLedgerAnalysisWithSaleTypeRepository $accountLedgerAnalysisWithSaleTypeRepository)
    {
        $this->stockGroupRepository = $stockGroupRepository;
        $this->godownRepository = $godownRepository;
        $this->accountLedgerAnalysisWithSaleTypeRepository=$accountLedgerAnalysisWithSaleTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerAnalysisWithSalesTypeShow()
    {
        if (user_privileges_check('report', 'AccountsLedgerAnalysisWithSalesType', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();
            return view('admin.report.movement_analysis_1.account_ledger_analysis_with_sales_type',compact('godowns','stock_group'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerAnalysisWithSalesType(Request $request)
    {
        if (user_privileges_check('report', 'AccountsLedgerAnalysisWithSalesType', 'display_role')) {
            try {
                $data = $this->accountLedgerAnalysisWithSaleTypeRepository->getAccountLedgerAnalysisWithSaleTypeOfIndex($request);

                return RespondWithSuccess('account ledger  analysis with sales type successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account ledger analysis with sales type successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
