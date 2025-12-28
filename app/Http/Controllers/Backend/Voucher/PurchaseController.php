<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Voucher\VoucherPurchaseRepository;
use App\Repositories\Backend\Voucher\VoucherReceivedRepository;
use App\Services\Voucher_setup\Voucher_setup;
use Illuminate\Support\Facades\Validator;
use App\Rules\StoreUniqueInvoiceValidation;
use App\Rules\UpdateUniqueInvoiceValidation;
use App\Rules\ProductValidation;
use App\Rules\LedgerPurchaseValidation;
use Illuminate\Http\Request;
use App\Services\Tree;
use App\Models\Godown;
use App\Models\LegerHead;
use App\Models\StockItem;
use App\Models\Voucher;
use Exception;



class PurchaseController extends Controller
{

    private $groupChartRepository;

    private $purchaseRepository;

    private $ledger;

    private $voucher_setup;

    private $tree;

    private $voucherReceivedRepository;

    private $customerRepository;

    public function __construct(Voucher_setup $voucher_setup, LegerHeadRepository $ledger, Tree $tree, GroupChartRepository $groupChartRepository, VoucherPurchaseRepository $purchaseRepository, VoucherReceivedRepository $voucherReceivedRepository, CustomerRepository $customerRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->ledger = $ledger;
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->purchaseRepository = $purchaseRepository;
        $this->voucherReceivedRepository = $voucherReceivedRepository;
        $this->customerRepository = $customerRepository;
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
                    'credit_ledger_name' => [
                        new LedgerPurchaseValidation($request->all()),
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
                    if($validator->errors()->get('product_name')||$validator->errors()->get('credit_ledger_id')||$validator->errors()->get('debit_ledger_id')||$validator->errors()->get('credit_ledger_name')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('product_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('credit_ledger_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }

                try {
                    $data = $this->purchaseRepository->storePurchase($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher Purchase successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Purchase Not successful !!', $e->getMessage(), 404);
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
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $balanceDebitCredit = $voucher->credit != 0 ? $this->voucher_setup->balanceDebitCredit($voucher->credit) : '';
            $credit_sum_value = $balanceDebitCredit ? $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit) : '';

            return view('admin.voucher.purchase.create_purchase', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_commission_tree', 'customers', 'ledger_name_debit_wise', 'credit_sum_value', 'ledger_name_credit_wise'));
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
        $data = $this->purchaseRepository->getPurchaseId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'display_role')) {
            $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
            $debit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Dr', 0);
            $credit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Cr', 0);
            $balanceDebitCredit ='';
            $credit_sum_value ='';
            if($credit){
                $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($credit->ledger_head_id);
                if($balanceDebitCredit){
                    $credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
                }
            }
            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = '';
            $ledger_tree = '';
            $godowns = '';
            if($voucher){
                $branch_setup = $this->voucher_setup->branchSetup($voucher);
                $ledger_tree = $this->voucher_setup->optionLedger(32, 3, $voucher->voucher_id);
                $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            }
            $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $ledger_name_debit_wise ='';
            if($voucher){
                $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            }
            return view('admin.voucher.purchase.edit_purchase', compact('branch_setup', 'data', 'voucher', 'debit_credit_data', 'ledger_tree', 'godowns', 'ledger_commission_tree', 'credit_sum_value', 'customers', 'ledger_name_debit_wise', 'debit', 'credit'));
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
                    'credit_ledger_name' => [
                        new LedgerPurchaseValidation($request->all()),
                    ],
                ]);

                if($validator->fails()){
                    if($validator->errors()->get('invoice_no')){
                     return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                    if($validator->errors()->get('product_name')||$validator->errors()->get('credit_ledger_id')||$validator->errors()->get('debit_ledger_id')||$validator->errors()->get('credit_ledger_name')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('product_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }else if($validator->errors()->get('credit_ledger_name')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }
            }
            try {
                $data = $this->purchaseRepository->updatePurchase($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher  Purchase Update successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Purchase Update Not successful !!', $e->getMessage(), 404);
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
        $data = $this->purchaseRepository->getPurchaseId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->purchaseRepository->deletePurchase($id);

                return RespondWithSuccess('Voucher  Purchase delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Purchase delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function purchaseStockIn(Request $request)
    {
        $data = $this->voucher_setup->stockIn($request->tran_id);
        echo json_encode($data);
        exit();
    }

    public function searchingDataGet(Request $request)
    {

        if ($request->fieldName == 'godown_name') {
            $data = $this->voucher_setup->godownAccessSearch($request->name, $request->voucher_id);
            echo json_encode($data);
            exit();
        } elseif ($request->fieldName == 'ledger_name') {
            $data = $this->voucher_setup->searchingLedgerDataGet($request->name, $request->voucher_id);
            echo json_encode($data);
            exit();
        } else {
            $data = $this->voucher_setup->search_item($request->name, $request->voucher_id);
            echo json_encode($data);
            exit();
        }
    }

    public function searchingStockItemPrice(Request $request)
    {
        $price_setup = Voucher::find($request->voucher_id, ['price_type_id', 'commission_type_id', 'commission_is']);
        if ($price_setup->commission_is == 1) {
            if ($price_setup->commission_type_id == 1) {
                $get_price = $this->voucher_setup->stock_group_commission_with_stock_price($request->stock_item_id, $price_setup->price_type_id,$request->tran_date??'');
            } elseif ($price_setup->commission_type_id == 2) {
                $get_price = $this->voucher_setup->stock_item_commission_with_stock_price($request->stock_item_id, $price_setup->price_type_id,$request->tran_date??'');
            }
        } else {
            if ($price_setup->voucher_type_id == 22 &&$price_setup->price_type_id==7) {
                $get_price = $this->voucher_setup->stockItemPrice($request->stock_item_id, $price_setup->price_type_id,$price_setup->voucher_type_id,$request->godown_id,$request->tran_date??'');
            }else{
                $get_price = $this->voucher_setup->stockItemPrice($request->stock_item_id, $price_setup->price_type_id,0,0,$request->tran_date??'');
            }
        }

        echo json_encode($get_price);
        exit();
    }

    public function searchingLedger(Request $request)
    {
        $voucher = Voucher::find($request->voucher_id);

        $data = $this->voucher_setup->searchingLedgerDataGetCredit($request->name, $request->voucher_id);
        if (!empty($data)) {
            echo json_encode($data);

            exit();
        } elseif ($voucher) {
            $data = $voucher->credit != 0 ? LegerHead::where('ledger_head_id', $voucher->credit)->get() : '';

            echo json_encode($data);
            exit();
        }
    }

    public function getProductName(Request $request)
    {
        if ($request->product_name) {
            $product_name = StockItem::where('product_name', $request->product_name)->first();

            echo json_encode($product_name);
            exit();
        }
        if ($request->godown_name) {
            $godown_name = Godown::where('godown_name', $request->godown_name)->first();
            echo json_encode($godown_name);
            exit();
        }
    }

    public function inlineSearchLedgerName(Request $request)
    {
        $data = $this->voucher_setup->ledger_head_searching($request->ledger_head_name);
        echo json_encode($data);
        exit();
    }


    public function BackwardAndForward(Request $request){
        try {
            $data = $this->voucher_setup->backwardForwardInvoiceNoAndRefNo($request);

            return RespondWithSuccess('Voucher  backward Forward successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher backward Forward Not successful !!', $e->getMessage(), 404);
        }
    }
}
