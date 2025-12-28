<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\StockVoucherRegisterRepository;
use Exception;
use Illuminate\Http\Request;

class StockVoucherRegisterController extends Controller
{
    private $voucherRepository;

    private $godownRepository;

    private $stockVoucherRegisterRepository;

    public function __construct(VoucherRepository $voucherRepository, GodownRepository $godownRepository,StockVoucherRegisterRepository $stockVoucherRegisterRepository)
    {
        $this->voucherRepository = $voucherRepository;
        $this->godownRepository = $godownRepository;
        $this->stockVoucherRegisterRepository = $stockVoucherRegisterRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockVoucherRegisterShow()
    {
        if (user_privileges_check('report', 'StockVoucherRegister', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.inventrory.stock_voucher_register', compact('vouchers', 'godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockVoucherRegister(Request $request)
    {
        if (user_privileges_check('report', 'StockVoucherRegister', 'display_role')) {
            try {
                $data = $this->stockVoucherRegisterRepository->getStockVoucherRegisterOfIndex($request);

                return RespondWithSuccess('stock voucher register successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock voucher register not successfully', $e->getMessage(), 400);
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
    public function stockItemVoucherRegister(Request $request)
    {
        if (user_privileges_check('report', 'StockVoucherRegister', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $stock_item_id = $request->stock_item_id;
            $from_date = $request->form_date;
            $to_date = $request->to_date;
            $godown_id = $request->godown_id;
            $voucher_id = $request->voucher_id;

            return view('admin.report.inventrory.stock_item_register', compact('vouchers', 'godowns', 'from_date', 'to_date', 'godown_id', 'stock_item_id','voucher_id'));
        } else {
            abort(403);
        }
    }
}
