<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockOut;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherSalesReturnRepository implements VoucherSalesReturnInterface
{
    
    public function storeSalesReturn(Request $request, $voucher_invoice)
    {

        $ip = $_SERVER['REMOTE_ADDR'];

        // tran master
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
        $data->dis_cen_id = $request->dis_cen_id ?? 0;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        // multiple stock out data
        $stock_out_data = [];
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                $stock_out_data[] = [
                    'tran_id' => $data->tran_id ?? exit,
                    'tran_date' => $request->invoice_date,
                    'stock_item_id' => $request->product_id[$i],
                    'godown_id' => $request->godown_id[$i] ?? 0,
                    'qty' => -(int) $request->qty[$i] ?? 0,
                    'rate' => -(float) $request->rate[$i] ?? 0,
                    'total' => -(float) $request->amount[$i] ?? 0,
                    'remark' => $request->remark[$i] ?? '',
                ];
            }
        }
        StockOut::insert($stock_out_data);

        // credit
        $debit_credit_data_sup = new DebitCredit();
        $debit_credit_data_sup->tran_id = $data->tran_id ?? exit;
        $debit_credit_data_sup->ledger_head_id = $request->credit_ledger_id;
        $debit_credit_data_sup->debit = 0;
        $debit_credit_data_sup->credit = $request->get_without_commission;
        $debit_credit_data_sup->dr_cr = 'Cr';
        $debit_credit_data_sup->save();

        //debit
        $debit_credit_data = new DebitCredit();
        $debit_credit_data->tran_id = $data->tran_id ?? exit;
        $debit_credit_data->ledger_head_id = $request->debit_ledger_id;
        $debit_credit_data->debit = $request->total_credit ?? 0;
        $debit_credit_data->dr_cr = 'Dr';
        $debit_credit_data->credit = 0;
        $debit_credit_data->save();
        
        //single or multiple product wise commission
        if ($request->get_commission ?? 0) {
            for ($i = 0; $i < count(array_filter($request->get_commission)); $i++) {
                if ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                    $debit_credit_data_com = new DebitCredit();
                    $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                    $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                    $debit_credit_data_com->debit = 0;
                    $debit_credit_data_com->credit = $request->get_commission[$i] ?? 0;
                    $debit_credit_data_com->dr_cr = 'Cr';
                    $debit_credit_data_com->commission = $request->commission_amount[$i] ?? 0;
                    $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                    $debit_credit_data_com->save();
                } elseif ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                    $debit_credit_data_com = new DebitCredit();
                    $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                    $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                    $debit_credit_data_com->debit = $request->get_commission[$i] ?? 0;
                    $debit_credit_data_com->credit = 0;
                    $debit_credit_data_com->dr_cr = 'Dr';
                    $debit_credit_data_com->commission = $request->commission_amount[$i] ?? 0;
                    $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                    $debit_credit_data_com->save();
                }
            }
        }

        return $debit_credit_data_sup;

    }

    public function getSalesReturnId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updateSalesReturn(Request $request, $id, $voucher_invoice)
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
        $data->dis_cen_id = $request->dis_cen_id ?? 0;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

       // multiple stock out data
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                if (! empty($request->stock_out_id[$i])) {
                    StockOut::where('stock_out_id', $request->stock_out_id[$i])->update([
                        'tran_date' => $request->invoice_date,
                        'godown_id' => $request->godown_id[$i],
                        'stock_item_id' => $request->product_id[$i],
                        'qty' => -(int) $request->qty[$i] ?? 0,
                        'rate' => -(float) $request->rate[$i] ?? 0,
                        'total' => -(float) $request->amount[$i] ?? 0,
                        'remark' => $request->remark[$i] ?? 0,
                    ]);
                } else {
                    $stock_out_data = new StockOut();
                    $stock_out_data->tran_id = $id;
                    $stock_out_data->tran_date = $request->invoice_date;
                    $stock_out_data->stock_item_id = $request->product_id[$i];
                    $stock_out_data->godown_id = $request->godown_id[$i] ?? 0;
                    $stock_out_data->qty = -(int) $request->qty[$i] ?? 0;
                    $stock_out_data->rate = -(float) $request->rate[$i];
                    $stock_out_data->total = -(float) $request->amount[$i];
                    $stock_out_data->remark = $request->remark[$i];
                    $stock_out_data->save();
                }
            }
        }
        //credit
        $debit_credit_data_sup = DebitCredit::findOrFail($request->credit_id);
        $debit_credit_data_sup->tran_id = $data->tran_id;
        $debit_credit_data_sup->ledger_head_id = $request->credit_ledger_id;
        $debit_credit_data_sup->debit = 0;
        $debit_credit_data_sup->credit = $request->get_without_commission;
        $debit_credit_data_sup->dr_cr = 'Cr';
        $debit_credit_data_sup->save();

        // debit
        $debit_credit_data = DebitCredit::findOrFail($request->debit_id);
        $debit_credit_data->tran_id = $data->tran_id;
        $debit_credit_data->ledger_head_id = $request->debit_ledger_id;
        $debit_credit_data->debit = $request->total_credit ?? 0;
        $debit_credit_data->dr_cr = 'Dr';
        $debit_credit_data->credit = 0;
        $debit_credit_data->save();

       //single or multiple product wise commission
        if ($request->get_commission ?? 0) {
            for ($i = 0; $i < count(array_filter($request->get_commission)); $i++) {
                if (! empty($request->commission_debit_credit_id[$i])) {
                    if ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                        $debit_credit_data_com = DebitCredit::findOrFail($request->commission_debit_credit_id[$i]);
                        $debit_credit_data_com->tran_id = $data->tran_id;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->credit = 0;
                        $debit_credit_data_com->dr_cr = 'Dr';
                        $debit_credit_data_com->commission = $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->save();
                    } elseif ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                        $debit_credit_data_com = DebitCredit::findOrFail($request->commission_debit_credit_id[$i]);
                        $debit_credit_data_com->tran_id = $data->tran_id;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = 0;
                        $debit_credit_data_com->credit = $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->dr_cr = 'Cr';
                        $debit_credit_data_com->commission = $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->save();
                    }
                } else {
                    if ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                        $debit_credit_data_com = new DebitCredit();
                        $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->credit = 0;
                        $debit_credit_data_com->dr_cr = 'Dr';
                        $debit_credit_data_com->commission = $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->save();
                    } elseif ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                        $debit_credit_data_com = new DebitCredit();
                        $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = 0;
                        $debit_credit_data_com->credit = $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->dr_cr = 'Cr';
                        $debit_credit_data_com->commission = $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->save();
                    }
                }
            }
        }
          //single or multiple  delete
        if (! empty($request->delete_stock_out_id)) {
            $delete_stock_out = explode(',', $request->delete_stock_out_id);
            for ($i = 0; $i < count(array_filter($delete_stock_out)); $i++) {
                StockOut::find($delete_stock_out[$i])->delete();
            }
        }
        if (! empty($request->delete_debit_credit_id)) {
            $delete_debit_credit = explode(',', $request->delete_debit_credit_id);
            for ($i = 0; $i < count(array_filter($delete_debit_credit)); $i++) {
                DebitCredit::find($delete_debit_credit[$i])->delete();
            }
        }

        return $debit_credit_data_sup;
    }

    public function deleteSalesReturn($id)
    {
        
        StockOut::where('tran_id', $id)->delete();
        DebitCredit::where('tran_id', $id)->delete();
        return TransactionMaster::findOrFail($id)->delete(); 
    }
}
