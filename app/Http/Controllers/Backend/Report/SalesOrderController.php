<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\SalesOrderRepository;
use App\Repositories\Backend\Voucher\VoucherDashboardRepository;
use App\Repositories\Backend\AuthRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{

    private $salesOrderRepository;

    private $tree;

    private $groupChartRepository;

    private $voucherRepository;

    private $authRepository;

    public function __construct(Tree $tree, GroupChartRepository $groupChartRepository,SalesOrderRepository $salesOrderRepository,VoucherDashboardRepository $voucherRepository,AuthRepository $authRepository)
    {
        $this->groupChartRepository=$groupChartRepository;
        $this->tree=$tree;
        $this->salesOrderRepository=$salesOrderRepository;
        $this->voucherRepository=$voucherRepository;
        $this->authRepository = $authRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function salesOrderShow()
    {
        if (user_privileges_check('report', 'SalesOrder', 'display_role')) {
            $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            return view('admin.report.approved.sales_order', compact('ledgers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function salesOrder(Request $request)
    {
        if (user_privileges_check('report', 'SalesOrder', 'display_role')) {
            try {
                $data = $this->salesOrderRepository->SalesOrderOfIndex($request);
                return RespondWithSuccess('sales order successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('sales order not successfully', $e->getMessage(), 400);
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
    public function voucherExchangeShow($id)
    {
        $sales = $this->voucherRepository->getVoucherOfIndex(19);
        return view('admin.report.approved.sales_order_voucher', compact('sales','id'));

    }

    public function salesOrderListShow()
    {
        if (user_privileges_check('report', 'SalesOrderList', 'display_role')) {
          
            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if (array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0) {
                $all=0;
                $data = json_decode(json_encode($this->tree->group_chart_tree_row_query($get_user->agar), true), true);
                $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
                $new_array = array_map(function ($k) use ($data) {
                    return $data[$k];
                }, $keys);
                
                $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($data, true), true), $new_array[0]['under']);
            }else{
                $all=1;
                $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            }
            return view('admin.report.approved.sales_order_list_show', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }
    public function salesOrderListUserWiseData(Request $request)
    {
        
        if (user_privileges_check('report', 'SalesOrderList', 'display_role')) {
            try {
                
                $data = $this->salesOrderRepository->SalesOrderUserWise($request);
                return RespondWithSuccess('sales order successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('sales order not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }

    }
}
