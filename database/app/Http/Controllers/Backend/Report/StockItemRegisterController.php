<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Master\StockItemRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\StockItemRegisterRepository;
use Exception;
use Illuminate\Http\Request;

class StockItemRegisterController extends Controller
{
    private $voucherRepository;

    private $godownRepository;

    private $stockItemRegisterRepository;

    private $stockItemRepository;

    private $legerHeadRepository;

    public function __construct(GodownRepository $godownRepository, VoucherRepository $voucherRepository, StockItemRepository $stockItemRepository, StockItemRegisterRepository $stockItemRegisterRepository, LegerHeadRepository $legerHeadRepository)
    {

        $this->godownRepository = $godownRepository;
        $this->voucherRepository = $voucherRepository;
        $this->stockItemRepository = $stockItemRepository;
        $this->stockItemRegisterRepository = $stockItemRegisterRepository;
        $this->legerHeadRepository = $legerHeadRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function StockItemRegisterShow()
    {
        if (user_privileges_check('report', 'StockItemRegister', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $vouchers = $this->voucherRepository->voucher_specific_data();

            return view('admin.report.inventrory.stock_item_register', compact('vouchers', 'godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function StockItemRegister(Request $request)
    {
        if (user_privileges_check('report', 'StockItemRegister', 'display_role')) {
            try {
                $data = $this->stockItemRegisterRepository->getStockItemRegisterOfIndex($request);

                return RespondWithSuccess('stock item register successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item register successfully', $e->getMessage(), 400);
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
    public function StockItemSelectOptionTree()
    {
        if (user_privileges_check('report', 'StockItemRegister', 'display_role')) {
            try {
                $data = $this->stockItemRepository->getSpecificItemData();

                return RespondWithSuccess('stock item successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item successfully', $e->getMessage(), 400);
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
    public function StockLedgerSelectOptionTree()
    {
        if (user_privileges_check('report', 'StockItemRegister', 'display_role')) {
            try {
                $data = $this->legerHeadRepository->getSpecificLedgerData();

                return RespondWithSuccess('ledger successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('ledger successfully', $e->getMessage(), 400);
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
    public function StockItemRegisterWise(Request $request)
    {
        if (user_privileges_check('report', 'StockItemRegister', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $stock_item_id = $request->stock_item_id;
            $date = $request->date;

            $month_year = date('Y-m', strtotime($date));
            $from_date = "$month_year-01";

            $month = date('Y-m-d', strtotime($month_year));
            $to_day = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date)), date('Y', strtotime($date)));
            if (date('m') == date('m', strtotime($date))) {
                $current_day = date('d');
                $to_date = "$month_year-$current_day";
            } else {
                $to_date = "$month_year-$to_day";
            }
            $godown_id = $request->godown_id;

            return view('admin.report.inventrory.stock_item_register', compact('vouchers', 'godowns', 'from_date', 'to_date', 'godown_id', 'stock_item_id'));
        } else {
            abort(403);
        }
    }
}
