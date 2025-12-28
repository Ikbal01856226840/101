<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Voucher\VoucherPOSRepository;
use App\Repositories\Backend\Voucher\VoucherReceivedRepository;
use App\Services\User\UserCheck;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{

    private $voucherPOSRepository;

    private $ledger;

    private $voucher_setup;

    private $voucherReceivedRepository;

    private $userCheck;

    public function __construct(Voucher_setup $voucher_setup, LegerHeadRepository $ledger, VoucherPOSRepository $voucherPOSRepository, VoucherReceivedRepository $voucherReceivedRepository,UserCheck  $userCheck)
    {
        $this->voucher_setup = $voucher_setup;
        $this->ledger = $ledger;
        $this->voucherPOSRepository = $voucherPOSRepository;
        $this->voucherReceivedRepository = $voucherReceivedRepository;
        $this->userCheck=$userCheck;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!empty(array_filter($request->product_id))) {
            if (user_privileges_check('Voucher', $request->voucher_id, 'create_role')) {

                if ($request->ch_4_dup_vou_no == 0) {
                    $voucher_invoice = '';
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
                    $data = $this->voucherPOSRepository->storePOS($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher POS successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher POS Not successful !!', $e->getMessage(), 404);
                }
            } else {
                abort(403);
            }
        } else {
            return RespondWithError('Voucher POS Not successful !!', 'Product not Empty', 422);
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
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);
            $ledger_tree = $this->voucher_setup->optionLedger(32, 3, $voucher->voucher_id);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $balanceDebitCredit = $voucher->debit != 0 ? $this->voucher_setup->balanceDebitCredit($voucher->debit) : '';
            $debit_sum_value = $balanceDebitCredit ? $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit) : '';
            $distributionCenter = $this->userCheck->AccessDistributionCenter();
            return view('admin.voucher.pos.create_pos', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_name_debit_wise', 'debit_sum_value', 'ledger_name_credit_wise','distributionCenter'));
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
        $data = $this->voucherPOSRepository->getPOSId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {
            $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
            $debit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Dr', 0);
            $credit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Cr', 0);
            $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($debit->ledger_head_id);
            $debit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $ledger_tree = $this->voucher_setup->optionLedger(32, 3, $voucher->voucher_id);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $distributionCenter = $this->userCheck->AccessDistributionCenter();
            return view('admin.voucher.pos.edit_pos', compact('branch_setup', 'data', 'voucher', 'debit_credit_data', 'ledger_tree', 'godowns', 'debit_sum_value', 'ledger_name_debit_wise','ledger_name_credit_wise' ,'debit', 'credit','distributionCenter'));
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
                        $voucher_invoice= $this->voucher_setup->duplicateVoucherCheckValidation($request->voucher_id,$request->invoice_no);
                    }
                }
            }
            try {
                $data =$this->voucherPOSRepository->updatePOS($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher POS Update  successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher POS Update Not successful !!', $e->getMessage(), 404);
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
        
        try {
            $data = $this->voucherPOSRepository->deletePOS($id);
            return RespondWithSuccess('Voucher POS Delete successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher POS Delete Not successful !!', $e->getMessage(), 404);
        }
       
    }
   
    public function posItemPrice(Request $request){
        
        try {
             $price_setup = Voucher::find($request->voucher_id, ['price_type_id']);
             $get_price = $this->voucher_setup->posPrice($request->stock_item_id,$request->shop_id,$price_setup->price_type_id);
             
             echo json_encode($get_price);
             exit();
        } catch (Exception $e) {
            return RespondWithError('Pos Not successful !!', $e->getMessage(), 404);
        }
    }
    
    public function Cancel(Request $request){
        $data = $this->voucherPOSRepository->getPOSId($request->id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucher_setup->cancelStockOutDebitCreditPOS($request->id);

                return RespondWithSuccess('Voucher  POS delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher POS delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

}
