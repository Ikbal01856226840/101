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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        if ($request->ch_4_dup_vou_no == 0) {
            $voucher_invoice = '';

            $validator = Validator::make($request->all(), [
                'invoice_no' => 'required|unique:transaction_master',
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
        $stock_item_commission = $this->commissionRepository->stockItemCommissionGetId($id, $debit_credit_data[1]->ledger_head_id, $data);

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
        if ($request->ch_4_dup_vou_no == 0) {
            $voucher_invoice = '';
            $validator = Validator::make($request->all(), [
                'invoice_no' => 'required|unique:transaction_master,invoice_no,' . $id . ',tran_id',
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
}
