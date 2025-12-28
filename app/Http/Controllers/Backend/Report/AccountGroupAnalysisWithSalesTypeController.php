<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountGroupAnalysisWithSaleTypeRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Master\GodownRepository;
use Exception;
use Illuminate\Http\Request;

class AccountGroupAnalysisWithSalesTypeController extends Controller
{

    private $accountGroupAnalysisWithSaleTypeRepository;

    private $groupChartRepository;

    private $stockGroupRepository;

    private $godownRepository;

    public function __construct(StockGroupRepository $stockGroupRepository,GodownRepository $godownRepository, GroupChartRepository $groupChartRepository,AccountGroupAnalysisWithSaleTypeRepository $accountGroupAnalysisWithSaleTypeRepository)
    {
        $this->stockGroupRepository = $stockGroupRepository;
        $this->groupChartRepository = $groupChartRepository;
        $this->accountGroupAnalysisWithSaleTypeRepository=$accountGroupAnalysisWithSaleTypeRepository;
        $this->godownRepository = $godownRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupAnalysisWithSalesTypeShow()
    {
        if (user_privileges_check('report', 'AccountsgroupAnalysisWithSalesType', 'display_role')) {

            $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');

            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();
            return view('admin.report.movement_analysis_2.account_group_analysis_with_sales_type',compact('group_chart_data','stock_group','godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupAnalysisWithSalesType(Request $request)
    {
        if (user_privileges_check('report', 'AccountsgroupAnalysisWithSalesType', 'display_role')) {
            try {
                $data = $this->accountGroupAnalysisWithSaleTypeRepository->getAccountGroupAnalysisWithSaleTypeOfIndex($request);

                return RespondWithSuccess('account group  analysis with sales type successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account group analysis with sales type successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
