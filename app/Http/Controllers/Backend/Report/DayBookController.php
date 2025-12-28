<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\DayBookRepository;
use App\Services\DebitCredit\DebitCreditService;
use App\Services\StockIn\StockInService;
use App\Services\StockOut\StockOutService;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;

class DayBookController extends Controller
{
    private $dayBookRepository;

    private $voucherRepository;

    private $stockInService;

    private $debitCreditService;

    private $stockOutService;

    private $voucher_setup;

    public function __construct(DayBookRepository $dayBookRepository, VoucherRepository $voucherRepository, StockInService $stockInService, DebitCreditService $debitCreditService, StockOutService $stockOutService,Voucher_setup $voucher_setup)
    {
        $this->dayBookRepository = $dayBookRepository;
        $this->voucherRepository = $voucherRepository;
        $this->debitCreditService = $debitCreditService;
        $this->stockInService = $stockInService;
        $this->stockOutService = $stockOutService;
        $this->voucher_setup = $voucher_setup;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('report', 'Daybook', 'display_role')) {
            $vouchers = $this->voucher_setup->AccessVoucherSetup();
            return view('admin.report.report_daybook', compact('vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDayBook(Request $request)
    {
        if (user_privileges_check('report', 'Daybook', 'display_role')) {
            try {
                $data = $this->dayBookRepository->getDayBookOfIndex($request);

                return RespondWithSuccess('Day Book show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Day Book show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $this->dayBookRepository->getDayBookOfIndex($request);

            return RespondWithSuccess('Day Book show successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Day Book show successfully', $e->getMessage(), 400);
        }
    }

    public function getDebitOrStock(Request $request)
    {
        if ($request->voucher_type_id == 1 || $request->voucher_type_id == 6 || $request->voucher_type_id == 8 || $request->voucher_type_id == 14) {
            $debit = $this->debitCreditService->debit_sum($request->tran_id);
            $data = ['debit' => $debit];

            return response()->json($data);
        } else {
            $stock_in_sum = $this->stockInService->stockInSum($request->tran_id);
            $stock_out_sum = $this->stockOutService->stockOutSum($request->tran_id);
            $data = ['stock_in_sum' => $stock_in_sum, 'stock_out_sum' => $stock_out_sum];

            return response()->json($data);
        }

    }
}
