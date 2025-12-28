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

                if ($request->ch_4_dup_vou_no == 0) {
                    $voucher_invoice = '';
                    $validator = Validator::make($request->all(), [
                        'invoice_no' => 'required|unique:transaction_master,invoice_no,' . $request->invoice_no . ',tran_id,voucher_id,' . $request->voucher_id,
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
        if (user_privileges_check('Voucher', $id, 'create_role')) {
            $voucher = Voucher::find($id);
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);

            $ledger_tree = $this->voucher_setup->optionLedger(35, 4, $voucher->voucher_id, 1);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->userCheck->AccessDistributionCenter();
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $balanceDebitCredit = $voucher->debit != 0 ? $this->voucher_setup->balanceDebitCredit($voucher->debit) : '';
            $debit_sum_value = $balanceDebitCredit ? $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit) : '';
            $ledger_id_wise = $this->ledger->getLegerHeadId(186) ?? '';
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
        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {
            $voucher = Voucher::find($data->voucher_id);
            $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
            $debit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Dr', null);
            $credit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Cr', null);
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
            $distributionCenter = $this->userCheck->AccessDistributionCenter();
            $ledger_name_credit_wise = $voucher->credit != 0 ? $this->ledger->getLegerHeadId($voucher->credit) : '';
            $ledger_id_wise = '';

            return view('admin.voucher.sales.edit_sales', compact('branch_setup', 'data', 'voucher', 'debit_credit_data', 'ledger_tree', 'godowns', 'ledger_commission_tree', 'credit_sum_value', 'customers', 'distributionCenter', 'voucher', 'ledger_id_wise', 'debit_data', 'debit', 'credit', 'ledger_name_credit_wise'));
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
                    'invoice_no' => 'required|unique:transaction_master,invoice_no,' . $id . ',tran_id,voucher_id,' . $request->voucher_id,
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
            $data = $this->voucher_setup->stock_in_stock_out_sum_qty($request->stock_item_id, $request->godown_id);

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
}
