<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Voucher\VoucherJournalRepository;
use App\Repositories\Backend\Voucher\VoucherReceivedRepository;
use App\Services\Tree;
use App\Services\User\UserCheck;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JournalController extends Controller
{
    
    private $groupChartRepository;

    private $voucher_setup;

    private $tree;

    private $voucherReceivedRepository;

    private $customerRepository;

    private $userCheck;

    private $voucherJournalRepository;

    public function __construct( Voucher_setup $voucher_setup, Tree $tree, GroupChartRepository $groupChartRepository, CustomerRepository $customerRepository, UserCheck $userCheck, VoucherJournalRepository $voucherJournalRepository, VoucherReceivedRepository $voucherReceivedRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->customerRepository = $customerRepository;
        $this->userCheck = $userCheck;
        $this->voucherJournalRepository = $voucherJournalRepository;
        $this->voucherReceivedRepository = $voucherReceivedRepository;
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
            $data = $this->voucherJournalRepository->storeJournal($request, $voucher_invoice);

            return RespondWithSuccess('Voucher Journal successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Journal  Not successful !!', $e->getMessage(), 404);
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
        $ledger_tree = $this->voucher_setup->optionLedger(35, 4, $voucher->voucher_id);
        $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
        $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
        $customers = $this->customerRepository->getCustomerOfIndex();
        $distributionCenter = $this->userCheck->AccessDistributionCenter();

        return view('admin.voucher.journal.create_journal', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_commission_tree', 'customers', 'distributionCenter'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->voucherJournalRepository->getJournalId($id);
        $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
        $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($debit_credit_data[1]->ledger_head_id);
        $credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
        $voucher = Voucher::find($data->voucher_id);
        $branch_setup = $this->voucher_setup->branchSetup($voucher);

        $ledger_tree = $this->voucher_setup->optionLedger(35, 4, $voucher->voucher_id);
        $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
        $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
        $customers = $this->customerRepository->getCustomerOfIndex();
        $distributionCenter = $this->userCheck->AccessDistributionCenter();

        return view('admin.voucher.journal.edit_journal', compact('branch_setup', 'data', 'voucher', 'debit_credit_data', 'ledger_tree', 'godowns', 'ledger_commission_tree', 'credit_sum_value', 'customers', 'distributionCenter', 'voucher'));
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
            $data = $this->voucherJournalRepository->updateJournal($request, $id, $voucher_invoice);

            return RespondWithSuccess('Voucher Journal Update successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Journal Update Not successful !!', $e->getMessage(), 404);
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
            $data = $this->voucherJournalRepository->deleteJournal($id);

            return RespondWithSuccess('Voucher  Journal delete successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Journal delete Not successful !!', $e->getMessage(), 404);
        }
    }

    public function getDebitCreditAndStockInStockOut(Request $request)
    {

        try {
            $data = $this->voucherJournalRepository->getDebitCreditAndStockInStockOut($request->tran_id);

            return RespondWithSuccess('Voucher  journal data successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher journal data Not successful !!', $e->getMessage(), 404);
        }
    }

    public function getJournalData(Request $request)
    {
        // dd($request->stock_item_id);
        if ($request->stock_item_id) {
            $stock = $this->voucher_setup->stock_in_stock_out_sum_qty($request->stock_item_id);
        } else {
            $stock = 0;
        }
        if ($request->ledger_head_id) {
            $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($request->ledger_head_id);
            $balance = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
        } else {
            $balance = 0;
        }
        $data[] = ['stock' => $stock, 'balance' => $balance];
        echo json_encode($data);
        exit;

    }
}
