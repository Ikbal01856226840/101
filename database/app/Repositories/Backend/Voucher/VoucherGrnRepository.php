<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockIn;
use App\Models\TransactionMaster;
use App\Services\DebitCredit\DebitCreditService;
use App\Services\StockIn\StockInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherGrnRepository implements VoucherGrnInterface
{
    private $debit_credit;

    private $stock_in;

    public function __construct(DebitCreditService $debit_credit_data, StockInService $stock_in)
    {
        $this->debit_credit = $debit_credit_data;
        $this->stock_in = $stock_in;
    }


    public function StoreGrn(Request $request, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new TransactionMaster();

        if (!empty($voucher_invoice)) {
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

        //multiple stock in data
        $stock_in_data = [];
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                $stock_in_data[] = [
                    'tran_id' => $data->tran_id ?? exit,
                    'tran_date' => $request->invoice_date,
                    'stock_item_id' => $request->product_id[$i],
                    'godown_id' => $request->godown_id[$i] ?? 0,
                    'qty' => (int) $request->qty[$i] ?? 0,
                    'rate' => (float) $request->rate[$i],
                    'total' => (float) $request->amount[$i],
                    'remark' => $request->remark[$i] ?? '',
                ];

            }
        }
        StockIn::insert($stock_in_data);

        if ($request->credit_ledger_id) {
            // get  credit data
            $this->debit_credit->debitCreditStore($data->tran_id, $request->credit_ledger_id, 0, $request->get_without_commission, 'Cr');
        }

        if ($request->debit_ledger_id) {
            // get  debit data
            $this->debit_credit->debitCreditStore($data->tran_id, $request->debit_ledger_id, $request->total_credit, 0, 'Dr');
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

    public function getGrnId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updateGrn(Request $request, $id, $voucher_invoice)
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

        // stock in
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                if (! empty($request->stock_in_id[$i])) {
                    $this->stock_in->StockInUpdate($request->stock_in_id[$i], $data->tran_id, $request->invoice_date, $request->product_id[$i], $request->godown_id[$i], $request->qty[$i], $request->rate[$i], $request->amount[$i], $request->remark[$i]);
                } else {
                    $this->stock_in->StockInStore($data->tran_id, $request->invoice_date, $request->product_id[$i], $request->godown_id[$i], $request->qty[$i], $request->rate[$i], $request->amount[$i], $request->remark[$i]);
                }
            }
        }

        // get  credit data
        $this->debit_credit->debitCreditUpdate($request->credit_id, $data->tran_id, $request->credit_ledger_id, 0, $request->get_without_commission, 'Cr');

        // get  debit data
        $this->debit_credit->debitCreditUpdate($request->debit_id, $data->tran_id, $request->debit_ledger_id, $request->total_credit, 0, 'Dr');

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

    public function deleteGrn($id)
    {
       
        DebitCredit::where('tran_id', $id)->delete();
        StockIn::where('tran_id', $id)->delete();

        return  TransactionMaster::findOrFail($id)->delete();
    }
}
