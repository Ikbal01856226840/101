<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Voucher\VoucherGtnRepository;
use App\Repositories\Backend\Voucher\VoucherReceivedRepository;
use App\Services\Tree;
use App\Services\User\UserCheck;
use App\Services\Voucher_setup\Voucher_setup;
use App\Rules\ProductValidation;
use App\Rules\LedgerGtnValidation;
use App\Rules\StoreUniqueInvoiceValidation;
use App\Rules\UpdateUniqueInvoiceValidation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GtnController extends Controller
{
   
    private $groupChartRepository;

    private $ledger;

    private $voucher_setup;

    private $tree;

    private $voucherReceivedRepository;

    private $customerRepository;

    private $userCheck;

    private $voucherGtnRepository;

    public function __construct( Voucher_setup $voucher_setup, LegerHeadRepository $ledger, Tree $tree, GroupChartRepository $groupChartRepository, CustomerRepository $customerRepository, UserCheck $userCheck, VoucherGtnRepository $voucherGtnRepository, VoucherReceivedRepository $voucherReceivedRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->ledger = $ledger;
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->customerRepository = $customerRepository;
        $this->userCheck = $userCheck;
        $this->voucherGtnRepository = $voucherGtnRepository;
        $this->voucherReceivedRepository = $voucherReceivedRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!empty(array_filter($request->product_id)) != 0) {
            if (user_privileges_check('Voucher', $request->voucher_id, 'create_role')) {
        
                $voucher_invoice = '';
                $validator = Validator::make($request->all(), [
                    'invoice_no' => [
                        'required',
                        new StoreUniqueInvoiceValidation($request->invoice_no, $request->voucher_id),
                    ],
                    'credit_ledger_id' => 'required',
                    'debit_ledger_id' => 'required',
                    'product_name' => [
                        new ProductValidation($request->all(),$this->voucher_setup),
                    ],
                    'debit_ledger_name' => [
                        new LedgerGtnValidation($request->all()),
                    ],
                ]);

                
                if($validator->fails()){
                    if($validator->errors()->get('invoice_no')){
                        if (!empty($request->invoice)) {
                            $voucher_invoice= $this->voucher_setup->duplicateVoucherCheckValidation($request->voucher_id,$request->invoice_no);
                        }else{
                            return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                        }
                    }
                    if($validator->errors()->get('product_name')||$validator->errors()->get('credit_ledger_id')||$validator->errors()->get('debit_ledger_id')||$validator->errors()->get('debit_ledger_name')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('product_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('debit_ledger_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }
                try {
                    $data = $this->voucherGtnRepository->storeGtn($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher Gtn successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Gtn Not successful !!', $e->getMessage(), 404);
                }
            } else {
                abort(403);
            }
        } else {
            return RespondWithError('Product not Empty !!', 'Product not Empty', 404);
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
        if (user_privileges_check('Voucher', $id, 'display_role')) {
            $voucher = Voucher::find($id);
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);
            $ledger_tree = $this->voucher_setup->optionLedger(32, 3, $voucher->voucher_id);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->voucher_setup->AccessVoucherDistributionCenter($voucher->distribution_center_id);
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $balanceDebitCredit = $voucher->debit != 0 ? $this->voucher_setup->balanceDebitCredit($voucher->debit) : '';
            $debit_sum_value = $balanceDebitCredit ? $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit) : '';

            return view('admin.voucher.gtn.create_gtn', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_commission_tree', 'customers', 'distributionCenter', 'ledger_name_debit_wise', 'ledger_name_credit_wise', 'debit_sum_value'));
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
        $data = $this->voucherGtnRepository->getGtnId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'display_role')) {
            $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
            $debit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Dr', null);
            $credit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Cr', null);
            $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($debit->ledger_head_id);
            $credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $ledger_tree = $this->voucher_setup->optionLedger(32, 3, $voucher->voucher_id);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter =$this->voucher_setup->AccessVoucherDistributionCenter($voucher->distribution_center_id);
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';

            return view('admin.voucher.gtn.edit_gtn', compact('branch_setup', 'data', 'voucher', 'debit_credit_data', 'ledger_tree', 'godowns', 'ledger_commission_tree', 'credit_sum_value', 'customers', 'distributionCenter', 'voucher', 'debit', 'credit', 'ledger_name_debit_wise', 'ledger_name_credit_wise'));
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
            if ($request->ch_4_dup_vou_no == 0) {
               
                $validator = Validator::make($request->all(), [
                    'invoice_no' => [
                        'required',
                        new  UpdateUniqueInvoiceValidation($id,$request->invoice_no,$request->voucher_id,$request->invoice_date),
                    ],
                    'credit_ledger_id' => 'required',
                    'debit_ledger_id' => 'required',
                    'product_name' => [
                        new ProductValidation($request->all(),$this->voucher_setup),
                    ], 
                    'debit_ledger_name' => [
                        new LedgerGtnValidation($request->all()),
                    ],
                ]);

                if($validator->fails()){
                    if($validator->errors()->get('invoice_no')){
                     return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                    if($validator->errors()->get('product_name')||$validator->errors()->get('credit_ledger_id')||$validator->errors()->get('debit_ledger_id')||$validator->errors()->get('debit_ledger_name')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('product_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('debit_ledger_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }
            }
            try {
                $data = $this->voucherGtnRepository->updateGtn($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher  Gtn Update successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Gtn Update Not successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherGtnRepository->getGtnId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherGtnRepository->deleteGtn($id);

                return RespondWithSuccess('Voucher  Gtn delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Gtn delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function Cancel(Request $request){
        $data = $this->voucherGtnRepository->getGtnId($request->id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucher_setup->cancelStockOutDebitCredit($request->id);
                $this->voucher_setup->transactionMasterNarrationUpdate($request);
                return RespondWithSuccess('Voucher  Sales delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Sales delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
