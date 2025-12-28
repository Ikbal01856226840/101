<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\BranchRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Voucher\VoucherOrderRequisitionRepository;
use App\Repositories\Backend\Voucher\VoucherPaymentRepository;
use App\Services\Voucher_setup\Voucher_setup;
use App\Models\LegerHead;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OrderRequisitionController extends Controller
{
    private $godown;

    private $unit_branch;

    private $voucher_setup;

    private $voucherPaymentRepository;

    private $voucherOrderRequisitionRepository;

    public function __construct(GodownRepository $godownRepository, BranchRepository $branchRepository, Voucher_setup $voucher_setup, VoucherPaymentRepository $voucherPaymentRepository,VoucherOrderRequisitionRepository $voucherOrderRequisitionRepository)
    {
        $this->godown = $godownRepository;
        $this->voucherPaymentRepository = $voucherPaymentRepository;
        $this->unit_branch = $branchRepository;
        $this->voucher_setup = $voucher_setup;
        $this->voucherOrderRequisitionRepository=$voucherOrderRequisitionRepository;

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


            if (user_privileges_check('Voucher', $request->voucher_id, 'create_role')) {

                $voucher_invoice = '';
                if ($request->ch_4_dup_vou_no == 0) {

                    $validator = Validator::make($request->all(), [
                        'invoice_no' => 'required',
                    ]);

                    if (empty($request->invoice)) {
                        if ($validator->fails()) {
                            return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                        }
                    }
                }
                try {
                    $data = $this->voucherOrderRequisitionRepository->storeOrderRequisition($request,$voucher_invoice);

                    return RespondWithSuccess('Voucher Payment successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Payment Not successful !!', $e->getMessage(), 404);
                }
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
    public function show($id)
    {
        if (user_privileges_check('Voucher', $id, 'create_role')) {
            $voucher = Voucher::find($id);
            Session::put('voucher_data', $voucher);
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_invoice = $this->voucher_setup->PurchaseOrderInvoice($voucher);

            return view('admin.voucher.order_requisition.create_order_requistion', compact('voucher_date', 'voucher_invoice', 'voucher'));


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

        $data = $this->voucherOrderRequisitionRepository->getOrderRequisitionId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {

            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $ledger_name =  LegerHead::where('ledger_head_id',$data->ledger_id)->first(['ledger_name']);
            $cashBalanceDebitCredit = $this->voucher_setup->balanceDebitCredit($data->ledger_id);
            $cash_credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($cashBalanceDebitCredit);
            return view('admin.voucher.order_requisition.edit_order_requistion', compact('branch_setup', 'data', 'voucher','voucher_date','ledger_name','cash_credit_sum_value'));

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

                $voucher_invoice = '';
                try {
                    $data = $this->voucherOrderRequisitionRepository->updateOrderRequisition($request, $id, $voucher_invoice);

                    return RespondWithSuccess('Voucher Order Requisition  Update Successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Order Requisition  Update Not Successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherOrderRequisitionRepository->getOrderRequisitionId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherOrderRequisitionRepository->deleteOrderRequisition($id);

                return RespondWithSuccess('Voucher  Order Requisition delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Order Requisition delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function Cancel(Request $request){
        $data = $this->voucherPaymentRepository->getVoucherPaymentId($request->id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucher_setup->cancelDebitCredit($request->id);
                $this->voucher_setup->transactionMasterNarrationUpdate($request);
                return RespondWithSuccess('Voucher  Payment delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Payment delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function getPaymentWithStockIn(Request $request){
        try {
            $data =$this->voucherPaymentRepository->getDebitCreditAndStockIn($request->tran_id);

            return RespondWithSuccess('Voucher  Payment data successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Payment  data Not successful !!', $e->getMessage(), 404);
        }
    }

    public function RequisitionShowData(Request $request){
        try {
            $data =$this->voucherOrderRequisitionRepository->PurchaseOrderData($request->tran_id);
            return RespondWithSuccess('Voucher  Order Requisition data successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Order Requisition  data Not successful !!', $e->getMessage(), 404);
        }
    }


}
