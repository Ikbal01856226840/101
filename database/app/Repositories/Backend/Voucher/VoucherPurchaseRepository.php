<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockIn;
use App\Models\TransactionMaster;
use App\Services\DebitCredit\DebitCreditService;
use App\Services\StockIn\StockInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherPurchaseRepository implements VoucherPurchaseInterface
{
    private $debit_credit;

    private $stock_in;

    public function __construct(DebitCreditService $debit_credit_data, StockInService $stock_in)
    {
        $this->debit_credit = $debit_credit_data;
        $this->stock_in = $stock_in;
    }


    public function storePurchase(Request $request, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        // transaction master
        $data = new TransactionMaster();

        if (! empty($voucher_invoice)) {
            $data->invoice_no = $voucher_invoice;
        } else {
            $data->invoice_no = $request->invoice_no;
        }
        $data->ref_no = $request->ref_no;
        $data->transaction_date = $request->invoice_date;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->voucher_id = $request->voucher_id;
        $data->narration = $request->narration;
        $data->customer_id = $request->customer_id ?? 0;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        // multiple stock in insert
        $stock_in_data = [];
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                $stock_in_data[] = [
                    'tran_id' => $data->tran_id ?? exit,
                    'tran_date' => $request->invoice_date,
                    'stock_item_id' => $request->product_id[$i],
                    'godown_id' => $request->godown_id[$i] ?? 0,
                    'qty' => (int) $request->qty[$i] ?? 0,
                    'rate' => (float) $request->rate[$i] ?? 0,
                    'total' => (float) $request->amount[$i] ?? 0,
                    'remark' => $request->remark[$i] ?? '',
                ];
            }
        }
        StockIn::insert($stock_in_data);

        if ($request->credit_ledger_id) {
            // get  credit data
            $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->credit_ledger_id, 0, $request->get_without_commission, 'Cr');
        }
        if ($request->debit_ledger_id) {
            // get  debit data
            $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->debit_ledger_id, $request->total_amount, 0, 'Dr');
        }

        // get  commission ledger
        if ($request->get_commission ?? 0) {
            for ($i = 0; $i < count(array_filter($request->get_commission)); $i++) {
                if ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                    $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->commission_ledger_id[$i], 0, $request->get_commission[$i], 'Cr', $request->commission_amount[$i], $request->commision_cal[$i]);
                } elseif ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                    $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->commission_ledger_id[$i], $request->get_commission[$i], 0, 'Dr', $request->commission_amount[$i], $request->commision_cal[$i]);
                }
            }
        }

        return $data;

    }

    public function getPurchaseId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updatePurchase(Request $request, $id, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = TransactionMaster::findOrFail($id);
        $update_history = json_decode($data->other_details);
        if (! empty($voucher_invoice)) {
            $data->invoice_no = $voucher_invoice;
        } else {
            $data->invoice_no = $request->invoice_no;
        }
        $data->ref_no = $request->ref_no;
        $data->transaction_date = $request->invoice_date;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->voucher_id = $request->voucher_id;
        $data->narration = $request->narration;
        $data->customer_id = $request->customer_id ?? 0;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        //  multiple stock in update
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                if (! empty($request->stock_in_id[$i])) {
                    $this->stock_in->StockInUpdate($request->stock_in_id[$i], $data->tran_id ?? exit, $request->invoice_date, $request->product_id[$i], $request->godown_id[$i], $request->qty[$i], $request->rate[$i], $request->amount[$i], $request->remark[$i]);
                } else {
                    $this->stock_in->StockInStore($data->tran_id ?? exit, $request->invoice_date, $request->product_id[$i], $request->godown_id[$i], $request->qty[$i], $request->rate[$i], $request->amount[$i], $request->remark[$i]);
                }
            }
        }
        // get  credit data
        $this->debit_credit->debitCreditUpdate($request->credit_id, $data->tran_id ?? exit, $request->credit_ledger_id, 0, $request->get_without_commission, 'Cr');

        // get  debit data
        $this->debit_credit->debitCreditUpdate($request->debit_id, $data->tran_id ?? exit, $request->debit_ledger_id, $request->total_amount, 0, 'Dr');

        // get  commission ledger
        if ($request->get_commission ?? 0) {
            for ($i = 0; $i < count(array_filter($request->get_commission)); $i++) {
                if (! empty($request->commission_debit_credit_id[$i])) {
                    if ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                        $this->debit_credit->debitCreditUpdate($request->commission_debit_credit_id[$i], $data->tran_id ?? exit, $request->commission_ledger_id[$i], 0, $request->get_commission[$i], 'Cr', $request->commission_amount[$i], $request->commision_cal[$i]);
                    } elseif ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                        $this->debit_credit->debitCreditUpdate($request->commission_debit_credit_id[$i], $data->tran_id ?? exit, $request->commission_ledger_id[$i], $request->get_commission[$i], 0, 'Dr', $request->commission_amount[$i], $request->commision_cal[$i]);
                    }
                } else {
                    if ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                        $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->commission_ledger_id[$i], 0, $request->get_commission[$i], 'Cr', $request->commission_amount[$i], $request->commision_cal[$i]);
                    } elseif ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                        $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->commission_ledger_id[$i], $request->get_commission[$i], 0, 'Dr', $request->commission_amount[$i], $request->commision_cal[$i]);
                    }
                }
            }
        }

        // id wise stock_in delete
        if (! empty($request->delete_stock_in_id)) {
            $delete_stock_in = explode(',', $request->delete_stock_in_id);
            for ($i = 0; $i < count(array_filter($delete_stock_in)); $i++) {
                StockIn::find($delete_stock_in[$i])->delete();
            }
        }

        // id wise debit credit delete
        if (! empty($request->delete_debit_credit_id)) {
            $delete_debit_credit = explode(',', $request->delete_debit_credit_id);
            for ($i = 0; $i < count(array_filter($delete_debit_credit)); $i++) {
                DebitCredit::find($delete_debit_credit[$i])->delete();
            }
        }

        return $data;
    }

    public function deletePurchase($id)
    {
        
        StockIn::where('tran_id', $id)->delete();
        DebitCredit::where('tran_id', $id)->delete();
        return TransactionMaster::findOrFail($id)->delete(); 
    }

    public function purchaseStockIn($id)
    {
        return DB::table('stock_in')
            ->select('stock_in.stock_in_id', 'stock_in.tran_id', 'stock_in.stock_item_id', 'stock_in.qty', 'stock_in.rate', 'stock_in.total', 'stock_item.product_name', 'godowns.godown_id', 'godowns.godown_name')
            ->leftJoin('stock_item', 'stock_in.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_in.godown_id', '=', 'godowns.godown_id')
            ->where('stock_in.tran_id', $id)
            ->get();

    }
}
