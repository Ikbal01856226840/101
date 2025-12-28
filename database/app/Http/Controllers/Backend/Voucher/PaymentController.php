<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\BranchRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Voucher\VoucherPaymentRepository;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    private $godown;

    private $unit_branch;

    private $voucher_setup;

    private $voucherPaymentRepository;

    public function __construct(GodownRepository $godownRepository, BranchRepository $branchRepository, Voucher_setup $voucher_setup, VoucherPaymentRepository $voucherPaymentRepository)
    {
        $this->godown = $godownRepository;
        $this->voucherPaymentRepository = $voucherPaymentRepository;
        $this->unit_branch = $branchRepository;
        $this->voucher_setup = $voucher_setup;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.master.components.index');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!empty(array_filter($request->ledger_id))) {
            if (user_privileges_check('Voucher', $request->voucher_id, 'create_role')) {
                
                $voucher_invoice = '';
                if ($request->ch_4_dup_vou_no == 0) {

                    $validator = Validator::make($request->all(), [
                        'invoice_no' => 'required|unique:transaction_master,invoice_no,'.$request->invoice_no.',tran_id,voucher_id,'.$request->voucher_id,
                    ]);

                    if (empty($request->invoice)) {
                        if ($validator->fails()) {
                            return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                        }
                    } else {
                        if ($validator->fails()) {
                            $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();

                            preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
                            $voucher_invoice = str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                        }
                    }
                }
                try {
                    $data = $this->voucherPaymentRepository->storeVoucherPayment($request,$voucher_invoice);

                    return RespondWithSuccess('Voucher Payment successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Payment Not successful !!', $e->getMessage(), 404);
                }
            } else {
                abort(403);
            }
        } else {
            return RespondWithError('Voucher  Payment Not successful !!', 'Ledger not Empty', 422);
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
        if (user_privileges_check('Voucher', $id, 'create_role')) {
            $godowns = $this->godown->getGodownOfIndex();
            $unit_branch = $this->unit_branch->getBranchOfIndex();
            $voucher = Voucher::find($id);
            Session::put('voucher_data', $voucher);
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);
            $debit_setup = $this->voucher_setup->debit_setup($voucher);
            $credit_setup = $this->voucher_setup->credit_setup($voucher);
            $debit_balance_cal = $this->voucher_setup->balanceDebitCreditCalculation($debit_setup);
            $credit_balance_cal = $this->voucher_setup->balanceDebitCreditCalculation($credit_setup);

            return view('admin.voucher.payment.create_payment', compact('godowns', 'unit_branch', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'debit_setup', 'credit_setup', 'debit_balance_cal', 'credit_balance_cal'));
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->voucherPaymentRepository->getVoucherPaymentId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {
            $unit_branch = $this->unit_branch->getBranchOfIndex();
            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);

            return view('admin.voucher.payment.edit_payment', compact('branch_setup', 'data', 'voucher'));
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (user_privileges_check('Voucher', $request->voucher_id, 'alter_role')) {

            if ($request->ch_4_dup_vou_no == 0) {
                $voucher_invoice = '';
                $validator = Validator::make($request->all(), [
                    'invoice_no' => 'required|unique:transaction_master,invoice_no,'.$id.',tran_id,voucher_id,'.$request->voucher_id,
                ]);
                if (empty($request->invoice)) {
                    if ($validator->fails()) {
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                } else {
                    if ($validator->fails()) {
                        $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();

                        preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
                        $voucher_invoice = str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                    }
                }
            }
            try {
                $data = $this->voucherPaymentRepository->updateVoucherPayment($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher Payment  Update Successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Payment  Update Not Successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->voucherPaymentRepository->getVoucherPaymentId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherPaymentRepository->deleteVoucherPayment($id);

                return RespondWithSuccess('Voucher  Payment delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Payment delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
