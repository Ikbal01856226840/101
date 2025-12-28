<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Voucher\VoucherGrnRepository;
use App\Repositories\Backend\Voucher\VoucherReceivedRepository;
use App\Services\Tree;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GrnController extends Controller
{
    private $godown;

    private $groupChartRepository;

    private $ledger;

    private $voucher_setup;

    private $tree;

    private $voucherGrnRepository;

    private $voucherReceivedRepository;

    private $customerRepository;

    public function __construct(GodownRepository $godownRepository, Voucher_setup $voucher_setup, LegerHeadRepository $ledger, Tree $tree, GroupChartRepository $groupChartRepository, VoucherGrnRepository $voucherGrnRepository, VoucherReceivedRepository $voucherReceivedRepository, CustomerRepository $customerRepository)
    {
        $this->godown = $godownRepository;
        $this->voucher_setup = $voucher_setup;
        $this->ledger = $ledger;
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->voucherGrnRepository = $voucherGrnRepository;
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
                    $data = $this->voucherGrnRepository->StoreGrn($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher Grn successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Grn Not successful !!', $e->getMessage(), 404);
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
        if (user_privileges_check('Voucher', $id, 'create_role')) {
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

            return view('admin.voucher.grn.create_grn', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_commission_tree', 'customers', 'ledger_name_debit_wise', 'ledger_name_credit_wise', 'credit_sum_value'));
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
        $data = $this->voucherGrnRepository->getGrnId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {
            $debit_credit_data = $this->voucherReceivedRepository->editDebitCredit($id);
            $debit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Dr', 0);
            $credit = $this->voucher_setup->id_wise_debit_credit_data($id, 'Cr', 0);
            $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($credit->ledger_head_id);
            $credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $ledger_tree = $this->voucher_setup->optionLedger(32, 3, $voucher->voucher_id);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $ledger_commission_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $ledger_name_debit_wise = $voucher->debit != 0 ? $this->ledger->getLegerHeadId($voucher->debit) : '';

            return view('admin.voucher.grn.edit_grn', compact('branch_setup', 'data', 'voucher', 'debit_credit_data', 'ledger_tree', 'godowns', 'ledger_commission_tree', 'credit_sum_value', 'customers', 'ledger_name_debit_wise', 'debit', 'credit'));
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
                $validator = Validator::make($request->all(), [
                    'invoice_no' => 'required|unique:transaction_master,invoice_no,'.$id.',tran_id,voucher_id,'.$request->voucher_id,
                ]);

                if (empty($request->invoice)) {
                    if ($validator->fails()) {
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                    $voucher_invoice = '';
                } else {
                    if ($validator->fails()) {
                        $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();
                        preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
                        $voucher_invoice = str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                    } else {
                        $voucher_invoice = '';
                    }
                }
            } else {
                $voucher_invoice = '';
            }
            try {
                $data = $this->voucherGrnRepository->updateGrn($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher  Grn Update successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher grn Update Not successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherGrnRepository->getGrnId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherGrnRepository->deleteGrn($id);

                return RespondWithSuccess('Voucher  Grn delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Grn delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
