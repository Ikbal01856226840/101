<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Voucher\VoucherSalesOrderRepository;
use App\Repositories\Backend\Voucher\VoucherSalesRepository;
use App\Services\Voucher_setup\Voucher_setup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\User\UserCheck;
use Illuminate\Http\Request;
use App\Services\Tree;
use App\Models\Voucher;
use Exception;




class SalesOrderController extends Controller
{

    private $voucher_setup;

    private $customerRepository;

    private $userCheck;

    private $voucherSalesOrderRepository;

    private $tree;

    private $ledger;

    private $groupChartRepository;

    private $voucherSalesRepository;


    public function __construct(Tree $tree, Voucher_setup $voucher_setup,  UserCheck $userCheck, VoucherSalesOrderRepository $voucherSalesOrderRepository,LegerHeadRepository $legerHeadRepository,GroupChartRepository $groupChartRepository,CustomerRepository $customerRepository,VoucherSalesRepository $voucherSalesRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->userCheck = $userCheck;
        $this->voucherSalesOrderRepository = $voucherSalesOrderRepository;
        $this->tree=$tree;
        $this->ledger=$legerHeadRepository;
        $this->groupChartRepository=$groupChartRepository;
        $this->customerRepository=$customerRepository;
        $this->voucherSalesRepository=$voucherSalesRepository;

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
                if ($request->ch_4_dup_vou_no == 0) {
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
                    $data = $this->voucherSalesOrderRepository->StoreVoucherSalesOrder($request,$voucher_invoice);

                    return RespondWithSuccess('Voucher Sales Order successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Sales Order Not successful !!', $e->getMessage(), 404);
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
            $balanceDebitCredit = $voucher->debit != 0 ? $this->voucher_setup->balanceDebitCredit($voucher->debit) : '';
            $debit_sum_value = $balanceDebitCredit ? $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit) : '';
            return view('admin.voucher.sales_order.create_sales_order', compact( 'voucher_invoice','voucher_date', 'branch_setup', 'voucher','debit_sum_value'));
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
        $data = $this->voucherSalesOrderRepository->getVoucherSalesOrderId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {
            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($data->ledger_head_id);
            $credit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);


            return view('admin.voucher.sales_order.edit_sales_order', compact('branch_setup', 'data', 'voucher', 'credit_sum_value'));
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

                $data = $this->voucherSalesOrderRepository->updateVoucherSalesOrder($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher  Sales Order Update successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Sales Order Update Not successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherSalesOrderRepository->getVoucherSalesOrderId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherSalesOrderRepository->deleteVoucherSalesOrder($id);

                return RespondWithSuccess('Voucher  Sales Order delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Sales Order delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function saleOrderData(Request $request){
        $data = $this->voucherSalesOrderRepository->getSalesOrderData($request->tran_id);
        return RespondWithSuccess('Voucher Sales Order successful  !! ', $data, 201);
    }

    public function  VoucherExchance(Request $request){
        $data = $this->voucherSalesOrderRepository->getVoucherSalesOrderId($request->tran_id);

        if (user_privileges_check('Voucher', $request->id, 'alter_role')) {
            $voucher = Voucher::find($request->id);
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
            $balanceDebitCredit = $this->voucher_setup->balanceDebitCredit($data->ledger_head_id);
            $debit_sum_value = $this->voucher_setup->balanceDebitCreditCalculation($balanceDebitCredit);
            $ledger_id_wise = $this->ledger->getLegerHeadId(186)??'';


            return view('admin.voucher.sales_order.voucher_exchange', compact('data','godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'ledger_tree', 'ledger_commission_tree', 'customers', 'distributionCenter', 'ledger_id_wise', 'ledger_name_debit_wise', 'ledger_name_credit_wise', 'debit_sum_value'));
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function VoucherExchanceStore(Request $request)
    {

        if (count(array_filter($request->product_id))!= 0) {
            if (user_privileges_check('Voucher', $request->voucher_id, 'create_role')) {
                if ($request->ch_4_dup_vou_no == 0) {

                    $validator = Validator::make($request->all(), [
                        'invoice_no' => 'required|unique:transaction_master,invoice_no,'.$request->invoice_no.',tran_id,voucher_id,'.$request->voucher_id,
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
                    $this->voucherSalesOrderRepository->statusUpadteSalesOrder($request->sales_order_tran_id);
                    $data = $this->voucherSalesRepository->storeSales($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher Sales successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Sales Not successful !!', $e->getMessage(), 404);
                }
            } else {
                abort(403);
            }
        } else {
            return RespondWithError('Voucher Sales Not successful !!', 'Product not Empty', 422);
        }
    }


}
