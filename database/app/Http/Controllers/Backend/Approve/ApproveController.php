<?php

namespace App\Http\Controllers\Backend\Approve;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Approve\ApproveRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Voucher\VoucherSalesRepository;
use Exception;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    private $approveRepository;

    private $voucherRepository;

    private $voucherSalesRepository;

    public function __construct(ApproveRepository $approveRepository, VoucherRepository $voucherRepository, VoucherSalesRepository $voucherSalesRepository)
    {
        $this->approveRepository = $approveRepository;
        $this->voucherRepository = $voucherRepository;
        $this->voucherSalesRepository = $voucherSalesRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showApprove()
    {
        $vouchers = $this->voucherRepository->voucher_specific_data();

        return view('admin.approve.approve', compact('vouchers'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $this->approveRepository->getApproveOfIndex($request);

            return RespondWithSuccess('approve show successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('approve not show successfully', $e->getMessage(), 404);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $tran_ledger = $this->approveRepository->tranmasterAndLedger($id);

        if ($tran_ledger->voucher_type_id == 14 || $tran_ledger->voucher_type_id == 8 || $tran_ledger->voucher_type_id == 1) {
            $debit_credit = $this->approveRepository->getdebitCredit($id);
            $stock = 0;
            $debit_credit_commission = 0;
            $stock_in = 0;
            $stock_out = 0;
        } elseif ($tran_ledger->voucher_type_id == 10 || $tran_ledger->voucher_type_id == 24 || $tran_ledger->voucher_type_id == 29) {
            $stock = $this->approveRepository->getStockIn($id);
            $debit_credit_commission = $this->approveRepository->getdebitCreditCommission($id);
            $debit_credit = 0;
            $stock_in = 0;
            $stock_out = 0;
        } elseif ($tran_ledger->voucher_type_id == 19 || $tran_ledger->voucher_type_id == 23 || $tran_ledger->voucher_type_id == 25 || $tran_ledger->voucher_type_id == 22) {

            if ($tran_ledger->commission_is == 1) {
                $stock = $this->voucherSalesRepository->product_wise_commission_sales($id);
                $debit_credit_commission = $this->approveRepository->getdebitCreditCommissionProductWise($id);

            } else {
                $stock = $this->approveRepository->getStockOut($id);
                if ($tran_ledger->voucher_type_id != 22) {
                    $debit_credit_commission = $this->approveRepository->getdebitCreditCommission($id);
                } else {
                    $debit_credit_commission = 0;
                }

            }
            $stock_in = 0;
            $stock_out = 0;
            $debit_credit = 0;

        } elseif ($tran_ledger->voucher_type_id == 21) {
            $stock_in = $this->approveRepository->getStockIn($id);
            $stock_out = $this->approveRepository->getStockOut($id);
            $stock = 0;
            $debit_credit_commission = 0;
            $debit_credit = 0;

        }

        return view('admin.approve.approve_report', compact('stock_in', 'stock_out', 'debit_credit', 'tran_ledger', 'stock', 'debit_credit_commission'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deliveryApproved($id)
    {
        try {
            $data = $this->approveRepository->deliveryApproved($id);

            return RespondWithSuccess('approve successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('approve not  successfully', $e->getMessage(), 404);
        }

    }
}
