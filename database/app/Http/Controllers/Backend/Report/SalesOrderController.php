<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;

use App\Repositories\Backend\Report\SalesOrderRepository;
use App\Repositories\Backend\Voucher\VoucherDashboardRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{

    private $salesOrderRepository;

    private $tree;

    private $groupChartRepository;

    private $voucherRepository;

    public function __construct(Tree $tree, GroupChartRepository $groupChartRepository,SalesOrderRepository $salesOrderRepository,VoucherDashboardRepository $voucherRepository)
    {
        $this->groupChartRepository=$groupChartRepository;
        $this->tree=$tree;
        $this->salesOrderRepository=$salesOrderRepository;
        $this->voucherRepository=$voucherRepository;

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
}
