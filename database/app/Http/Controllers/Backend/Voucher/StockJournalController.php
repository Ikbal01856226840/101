<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\StockIn;
use App\Models\Voucher;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Voucher\{VoucherStockJournalRepository};
use App\Services\User\UserCheck;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockJournalController extends Controller
{
    private $godown;

    private $voucher_setup;

    private $customerRepository;

    private $userCheck;

    private $voucherStockJournalRepository;

    public function __construct(GodownRepository $godownRepository, Voucher_setup $voucher_setup, CustomerRepository $customerRepository, UserCheck $userCheck, VoucherStockJournalRepository $voucherStockJournalRepository)
    {
        $this->godown = $godownRepository;
        $this->voucher_setup = $voucher_setup;
        $this->customerRepository = $customerRepository;
        $this->userCheck = $userCheck;
        $this->voucherStockJournalRepository = $voucherStockJournalRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ((!empty(array_filter($request->product_in_id)))||(!empty(array_filter($request->product_out_id)))) {
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
                    $data = $this->voucherStockJournalRepository->storeStockJournal($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher StockJournal successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher StockJournal Not successful !!', $e->getMessage(), 404);
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
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->userCheck->AccessDistributionCenter();
            $destination_godowns = $this->godown->getGodownOfIndex();

            return view('admin.voucher.stock_journal.create_stock_journal', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'customers', 'distributionCenter', 'destination_godowns'));
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
        $data = $this->voucherStockJournalRepository->getStockJournalId($id);

        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {
            $voucher = Voucher::find($data->voucher_id);
            $godown_id_in = StockIn::where('tran_id', $id)->first(['godown_id']);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->userCheck->AccessDistributionCenter();
            $destination_godowns = $this->godown->getGodownOfIndex();
            if ($voucher->is_pary_ledger == 1) {
                $ledger = $this->voucherStockJournalRepository->getLedgerTranIdWise($id);
            } else {
                $ledger = 0;
            }

            return view('admin.voucher.stock_journal.edit_stock_journal', compact('branch_setup', 'data', 'voucher', 'godowns', 'customers', 'distributionCenter', 'voucher', 'godown_id_in', 'destination_godowns', 'ledger'));
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
                $data = $this->voucherStockJournalRepository->updateStockJournal($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher  Update StockJournal successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Update StockJournal Not successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherStockJournalRepository->getStockJournalId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherStockJournalRepository->deleteStockJournal($id);

                return RespondWithSuccess('Voucher  StockJournal delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher StockJournal delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function stockIn_with_current_stock(Request $request)
    {
        try {
            $data = $this->voucher_setup->stockIn_with_current_stock($request->tran_id);

            return RespondWithSuccess('Voucher  Stock In And out successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Stock In And out Not successful !!', $e->getMessage(), 404);
        }
    }

    public function destinationPrice(Request $request)
    {
        $price_setup = Voucher::find($request->voucher_id, ['destrination_price_type_id']);
        $get_price = $this->voucher_setup->stockItemPrice($request->stock_item_id, $price_setup->destrination_price_type_id);
        echo json_encode($get_price);
        exit();
    }
}
