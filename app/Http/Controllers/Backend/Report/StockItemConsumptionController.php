<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\BillOfMaterialRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\StockItemRegisterRepository;
use Exception;
use Illuminate\Http\Request;

class StockItemConsumptionController extends Controller
{
    private $voucherRepository;

    private $godownRepository;

    private $billOfMaterialRepository;

    private $stockItemRegisterRepository;


    public function __construct(GodownRepository $godownRepository, VoucherRepository $voucherRepository, BillOfMaterialRepository $billOfMaterialRepository,StockItemRegisterRepository $stockItemRegisterRepository)
    {

        $this->godownRepository = $godownRepository;
        $this->voucherRepository = $voucherRepository;
        $this->billOfMaterialRepository = $billOfMaterialRepository;
        $this->stockItemRegisterRepository = $stockItemRegisterRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function StockItemConsumptionShow()
    {
        if (user_privileges_check('report', 'StockItemConsumption', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $puduction_items=$this->billOfMaterialRepository->getBillOfMaterialProduction();
            return view('admin.report.inventrory.stock_item_consumption', compact('vouchers', 'godowns','puduction_items'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function StockItemConsumptionData(Request $request)
    {
        if (user_privileges_check('report', 'StockItemConsumption', 'display_role')) {
            try {
                $data = $this->stockItemRegisterRepository->getStockItemConsumption($request);

                return RespondWithSuccess('stock item register  store successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item register store  successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }


    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function StockItemRegisterStoreMonthWise(Request $request)
    {
        if (user_privileges_check('report', 'StockItemRegisterStore', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $stock_item_id = $request->stock_item_id;
            $date = $request->date;

            $month_year = date('Y-m', strtotime($date));
            if(date('Y-m', strtotime($request->from_date))==$month_year){
                $form_date =$request->from_date;
            }else{
                $form_date = "$month_year-01";
            }

            $month = date('Y-m-d', strtotime($month_year));
            $to_day = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date)), date('Y', strtotime($date)));
            if(date('Y-m', strtotime($request->to_date))==$month_year){
                $to_date =$request->to_date;
            }else{
                if (date('m') == date('m', strtotime($date))) {
                    $current_day = date('d');
                    $to_date = "$month_year-$current_day";
                } else {
                    $to_date = "$month_year-$to_day";
                }
            }
            $godown_id = $request->godown_id;

            return view('admin.report.inventrory.stock_item_register_store', compact('vouchers', 'godowns', 'from_date', 'to_date', 'godown_id', 'stock_item_id'));
        } else {
            abort(403);
        }
    }
}
