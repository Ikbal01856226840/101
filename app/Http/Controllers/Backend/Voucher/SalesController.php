<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Voucher\VoucherReceivedRepository;
use App\Repositories\Backend\Voucher\VoucherSalesRepository;
use App\Services\Voucher_setup\Voucher_setup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\User\UserCheck;
use Illuminate\Http\Request;
use App\Models\LegerHead;
use App\Models\Voucher;
use App\Services\Tree;
use Exception;
use App\Rules\ProductValidation;
use App\Rules\StoreUniqueInvoiceValidation;
use App\Rules\UpdateUniqueInvoiceValidation;
use App\Rules\LedgerSalesValidation;



class SalesController extends Controller
{
    private $groupChartRepository;

    private $ledger;

    private $voucher_setup;

    private $tree;

    private $voucherReceivedRepository;

    private $customerRepository;

    private $userCheck;

    private $voucherSalesRepository;

    public function __construct(Voucher_setup $voucher_setup, LegerHeadRepository $ledger, Tree $tree, GroupChartRepository $groupChartRepository, CustomerRepository $customerRepository, UserCheck $userCheck, VoucherSalesRepository $voucherSalesRepository, VoucherReceivedRepository $voucherReceivedRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->ledger = $ledger;
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->customerRepository = $customerRepository;
        $this->userCheck = $userCheck;
        $this->voucherSalesRepository = $voucherSalesRepository;
        $this->voucherReceivedRepository = $voucherReceivedRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        if (empty(array_filter($request->product_id))) {
            return RespondWithError('Product not Empty !!', 'Product not Empty', 404);
        } else {
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
                        new LedgerSalesValidation($request->all()),
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
                    }
                }
                try {
                    $data = $this->voucherSalesRepository->storeSales($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher Sales successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Sales Not successful !!', $e->getMessage(), 404);
                }
            } else {
                abort(403);
            }
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

            $ledger_tree = $this->voucher_setup->optionLedger(35, 4, $voucher->voucher_id, 1);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->voucher_setup->AccessVoucherDistributionCenter($voucher->distribution_center_id);
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $balanceDebitCredit = $voucher->debit != 0 ? $this->voucher_setup->balanceDebitCredit($voucher->debit) : '';
            $debit_sum_value = $balanceDebitCredit ? $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit) : '';
            $ledger_id_wise =$voucher->commission_ledger_id? $this->ledger->getLegerHeadId($voucher->commission_ledger_id):'';
            return view('admin.voucher.sales.create_sales', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_commission_tree', 'customers', 'distributionCenter', 'ledger_id_wise', 'ledger_name_debit_wise', 'ledger_name_credit_wise', 'debit_sum_value'));
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
        $data = $this->voucherSalesRepository->getSalesId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'display_role')) {
            $voucher = Voucher::find($data->voucher_id);
            $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
            $debit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Dr', null);
            $credit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Cr', null);

            if($data->sale_type== 1){
                $cash_ledger_name =  LegerHead::where('ledger_head_id',$data->salse_cash_ledger_id)->first(['ledger_name']);
                $cashBalanceDebitCredit = $this->voucher_setup->balanceDebitCredit($data->salse_cash_ledger_id);
                $cash_credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($cashBalanceDebitCredit);
            }else{
                $cash_ledger_name =0;
                $cash_credit_sum_value = 0;
            }
            if ($voucher->commission_is == 1) {
                $debit_data = $this->voucherSalesRepository->product_wise_debit_data($id);
                $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($debit_data[0]->debit_ledger_id);
                $credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
            } else {
                $debit_data = 0;
                $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($debit->ledger_head_id);
                $credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
            }

            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $ledger_tree = $this->voucher_setup->optionLedger(35, 4, $voucher->voucher_id, 1);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->voucher_setup->AccessVoucherDistributionCenter($voucher->distribution_center_id);
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $ledger_id_wise =$voucher->commission_ledger_id? $this->ledger->getLegerHeadId($voucher->commission_ledger_id):'';

            return view('admin.voucher.sales.edit_sales', compact('branch_setup', 'data', 'voucher', 'debit_credit_data', 'ledger_tree', 'godowns', 'ledger_commission_tree', 'credit_sum_value', 'customers', 'distributionCenter', 'voucher', 'ledger_id_wise', 'debit_data', 'debit', 'credit', 'ledger_name_credit_wise','cash_ledger_name','cash_credit_sum_value'));
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
                        new LedgerSalesValidation($request->all()),
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
                    }
                }
            }
            try {
                $data = $this->voucherSalesRepository->updateSales($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher  Sales Update successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Sales Update Not successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherSalesRepository->getSalesId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherSalesRepository->deleteSales($id);

                return RespondWithSuccess('Voucher  Sales delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Sales delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function currentStock(Request $request)
    {
        try {
            $data = $this->voucher_setup->stock_in_stock_out_sum_qty($request->stock_item_id, $request->godown_id,$request->allowAllStock??0);

            return RespondWithSuccess(' CurrentStock  successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('CurrentStock  Not successful !!', $e->getMessage(), 404);
        }
    }

    public function stockOut(Request $request)
    {
        try {
            if ($request->commission_is == 1) {
                $data = $this->voucherSalesRepository->product_wise_commission_sales($request->tran_id);
            } else {
                $data = $this->voucher_setup->stockOut($request->tran_id);
            }

            return RespondWithSuccess('stock Out  Purchase delete successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('stock Out delete Not successful !!', $e->getMessage(), 404);
        }
    }

    public function searchingLedgerDebit(Request $request)
    {
        $voucher = Voucher::find($request->voucher_id);
        $data = $this->voucher_setup->searchingLedgerDataGet($request->name, $request->voucher_id);
        if (!empty($data)) {
            echo json_encode($data);
            exit();
        } elseif ($voucher) {
            $data = $voucher->debit != 0 ? LegerHead::where('ledger_head_id', $voucher->debit)->get() : '';
            echo json_encode($data);
            exit();
        }
    }

    public function Cancel(Request $request){
        $data = $this->voucherSalesRepository->getSalesId($request->id);
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

    public function searchingStockItem(Request $request)
    {
        // $voucher = Voucher::find($request->voucher_id);
        $data = $this->voucher_setup->search_item($request->name);
        if (!empty($data)) {
            echo json_encode($data);
            exit();
        } 
    }
    public function searchingStockItemName(Request $request)
    {
        // $voucher = Voucher::find($request->voucher_id);
        $data=DB::table('stock_item')->where('stock_item_id',$request->id)->first();
        if (!empty($data)) {
            echo json_encode($data);
            exit();
        } 
    }
}
