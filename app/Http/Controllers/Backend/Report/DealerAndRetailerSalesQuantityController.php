<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\AccountLedgerDealerRetrailerAnalysisRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Report\WarehouseWiseStockRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\AuthRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class DealerAndRetailerSalesQuantityController extends Controller
{

    private $tree;

    private $groupChartRepository;

    private $accountLedgerDealerRetrailerAnalysisRepository;

    private $stockGroupRepository;

    private $warehouseWiseStockRepository;

    private $ledgerHead;

    private $authRepository;


    public function __construct(LegerHeadRepository $ledgerHead,StockGroupRepository $stockGroupRepository,Tree $tree, GroupChartRepository $groupChartRepository,AccountLedgerDealerRetrailerAnalysisRepository $accountLedgerDealerRetrailerAnalysisRepository,WarehouseWiseStockRepository $warehouseWiseStockRepository,AuthRepository $authRepository)
    {

        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->accountLedgerDealerRetrailerAnalysisRepository = $accountLedgerDealerRetrailerAnalysisRepository;
        $this->stockGroupRepository = $stockGroupRepository;
        $this->warehouseWiseStockRepository = $warehouseWiseStockRepository;
        $this->ledgerHead = $ledgerHead;
        $this->authRepository = $authRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dealerAndRetailerSalesQuantityShow()
    {
        if (user_privileges_check('report', 'DealerAndRetrailerSalesQuantity', 'display_role')) {
            $all=1;
            $ledgers = $this->tree->getTreeViewSelectOptionRetrailerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);

            return view('admin.report.movement_analysis_1.dealer_and_retailer_sales_quantity', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function dealerAndRetailerSalesQuantityGetData(Request $request)
    {

        if (user_privileges_check('report', 'DealerAndRetrailerSalesQuantity', 'display_role')) {
            try {
                $data = $this->accountLedgerDealerRetrailerAnalysisRepository->DealerAndRetrailerSalesQuantity($request);

                return RespondWithSuccess('Retrailer Analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Retrailer Analysis successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function retailerActualSalesShow()
    {
        if (user_privileges_check('report', 'RetrailerActualSales', 'display_role')) {
            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if (array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0) {
                $all=0;
                // $data = json_decode(json_encode($this->tree->group_chart_tree_row_query($get_user->agar), true), true);
                // $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
                // $new_array = array_map(function ($k) use ($data) {
                //     return $data[$k];
                // }, $keys);

                // $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($data, true), true), $new_array[0]['under']);
                $all=0;
                $ledgers='';
                $group_chart_data = $this->tree->group_chart_tree_row_query($get_user->agar);
                $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
                $new_array = array_map(function ($k) use ($group_chart_data) {
                    return $group_chart_data[$k];
                }, $keys);

                for ($i = 0; $i < count($keys); $i++) {
                     $ledgers.= $this->tree->getTreeViewSelectOptionLedgerTree($this->tree->group_chart_tree_row_query($get_user->agar), $new_array[$i]['under']);
                }
            }else{
                $all=1;
                $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            }
            
            return view('admin.report.movement_analysis_1.retailer_actual_sales', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function RetailerActualSalesGetData(Request $request)
    {

        if (user_privileges_check('report', 'RetrailerActualSales', 'display_role')) {
            try {
                $data = $this->accountLedgerDealerRetrailerAnalysisRepository->RetailerActualSalesGetData($request);

                return RespondWithSuccess('Retrailer Analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Retrailer Analysis successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentStockShow()
    {
        if (user_privileges_check('report', 'CurrentStock', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            return view('admin.report.movement_analysis_1.dealer_and_retailer_current_stock', compact('stock_group'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentStockGetData(Request $request)
    {

        if (user_privileges_check('report', 'CurrentStock', 'display_role')) {
            try {
                $data =$this->warehouseWiseStockRepository->getWarehouseWiseStockOfIndex($request);;

                return RespondWithSuccess('Current Stock successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Current Stock successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }


}
