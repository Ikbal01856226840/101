<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Voucher\VoucherCommissionRepository;
use App\Repositories\Backend\Voucher\VoucherReceivedRepository;
use App\Services\Tree;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\StoreUniqueInvoiceValidation;
use App\Rules\UpdateUniqueInvoiceValidation;
use App\Rules\CommissionValidation;

class CommissionController extends Controller
{

    private $groupChartRepository;

    private $voucher_setup;

    private $tree;


    private $voucherReceivedRepository;

    private $customerRepository;

    private $commissionRepository;

    public function __construct(Voucher_setup $voucher_setup,  Tree $tree, GroupChartRepository $groupChartRepository,  VoucherReceivedRepository $voucherReceivedRepository, CustomerRepository $customerRepository, VoucherCommissionRepository $voucherCommissionRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->voucherReceivedRepository = $voucherReceivedRepository;
        $this->customerRepository = $customerRepository;
        $this->commissionRepository = $voucherCommissionRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
         $voucher_invoice = '';
         $validator = Validator::make($request->all(), [
                    'invoice_no' => [
                        'required',
                        new StoreUniqueInvoiceValidation($request->invoice_no, $request->voucher_id),
                    ],
                    'commission_amount' => [
                        new CommissionValidation($request->all(),$this->voucher_setup),
                    ],
                ]);
                if($validator->fails()){
                    if($validator->errors()->get('invoice_no')){
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                    if($validator->errors()->get('commission_amount')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }
        try {
            $data = $this->commissionRepository->storeVoucherCommission($request, $voucher_invoice);

            return RespondWithSuccess('Voucher Commission successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Commission Not successful !!', $e->getMessage(), 404);
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

        $voucher = Voucher::find($id);
        $voucher_date = $this->voucher_setup->dateSetup($voucher);
        $branch_setup = $this->voucher_setup->branchSetup($voucher);
        $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);
        $ledger_tree = $this->voucher_setup->optionLedger(31, 1, $voucher->voucher_id);
        $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
        $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
        $customers = $this->customerRepository->getCustomerOfIndex();

        return view('admin.voucher.commission.create_commission', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_commission_tree', 'customers'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->commissionRepository->getVoucherCommissionId($id);
        $voucher = Voucher::find($data->voucher_id);
        $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
        $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
        $stock_item_commission = $this->commissionRepository->stockItemCommissionGetId($id, $debit_credit_data[0]->ledger_head_id, $data);

        return view('admin.voucher.commission.edit_commission', compact('data', 'debit_credit_data', 'ledger_commission_tree', 'stock_item_commission', 'voucher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
           $voucher_invoice = '';
            if ($request->ch_4_dup_vou_no == 0) {
               
                 $validator = Validator::make($request->all(), [
                    'invoice_no' => [
                        'required',
                        new  UpdateUniqueInvoiceValidation($id,$request->invoice_no,$request->voucher_id,$request->invoice_date),
                    ],
                    'commission_amount' => [
                        new CommissionValidation($request->all(),$this->voucher_setup),
                    ],
                ]);

                if($validator->fails()){
                    if($validator->errors()->get('invoice_no')){
                     return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                    if($validator->errors()->get('commission_amount')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }
            }
        
        try {
            $data = $this->commissionRepository->updateVoucherCommission($request, $id, $voucher_invoice);

            return RespondWithSuccess('Voucher Commission Update successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Commission Update Not successful !!', $e->getMessage(), 404);
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
            $data = $this->commissionRepository->deleteVoucherCommission($id);

            return RespondWithSuccess('Voucher  Commission delete successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Commission delete Not successful !!', $e->getMessage(), 404);
        }
    }

    public function showCommission(Request $request)
    {
        $data = $this->commissionRepository->getCommission($request);
        echo json_encode($data);
        exit();
    }

    public function Cancel(Request $request){
        
        $data = $this->commissionRepository->getVoucherCommissionId($request->id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucher_setup->cancelCommissionVoucher($request->id);
                $this->voucher_setup->transactionMasterNarrationUpdate($request);
                return RespondWithSuccess('Voucher  Commission delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Commission delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
    public function showEditCommission(Request $request)
    {
        
        $stock_item_commission = $this->commissionRepository->stockItemCommissionGetId($request->id, $request->ledger_head_id??0, $request);
        echo json_encode($stock_item_commission );
        exit();
    }
}
