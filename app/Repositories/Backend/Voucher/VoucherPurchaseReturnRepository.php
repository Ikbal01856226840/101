<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockIn;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherPurchaseReturnRepository implements VoucherPurchaseReturnInterface
{


    public function storePurchaseReturn(Request $request, $voucher_invoice)
    {

        $ip = $_SERVER['REMOTE_ADDR'];

        //tran master
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
        $data->secret_narration = $request->secret_narration??'';
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
                    'qty' => -(float) $request->qty[$i] ?? 0,
                    'rate' => -(float) $request->rate[$i],
                    'total' => -(float) $request->amount[$i],
                    'remark' => $request->remark[$i],
                ];
            }
        }
        StockIn::insert($stock_in_data);

        //debit
        $debit_credit_data_sup = new DebitCredit();
        $debit_credit_data_sup->tran_id = $data->tran_id ?? exit;
        $debit_credit_data_sup->ledger_head_id = $request->debit_ledger_id;
        $debit_credit_data_sup->debit = (float) $request->get_without_commission;
        $debit_credit_data_sup->credit = 0;
        $debit_credit_data_sup->dr_cr = 'Dr';
        $debit_credit_data_sup->save();

        //credit
        $debit_credit_data = new DebitCredit();
        $debit_credit_data->tran_id = $data->tran_id ?? exit;
        $debit_credit_data->ledger_head_id = $request->credit_ledger_id;
        $debit_credit_data->debit = 0;
        $debit_credit_data->dr_cr = 'Cr';
        $debit_credit_data->credit = (float) $request->total_credit ?? 0;
        $debit_credit_data->save();

        //single or multiple  commission
        if ($request->get_commission ?? 0) {
            for ($i = 0; $i < count(array_filter($request->get_commission)); $i++) {
                if ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                    $debit_credit_data_com = new DebitCredit();
                    $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                    $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                    $debit_credit_data_com->debit = 0;
                    $debit_credit_data_com->credit = (float) $request->get_commission[$i] ?? 0;
                    $debit_credit_data_com->dr_cr = 'Cr';
                    $debit_credit_data_com->commission = (float) $request->commission_amount[$i] ?? 0;
                    $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                    $debit_credit_data_com->comm_level = 1;
                    $debit_credit_data_com->save();
                } elseif ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                    $debit_credit_data_com = new DebitCredit();
                    $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                    $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                    $debit_credit_data_com->debit = (float) $request->get_commission[$i] ?? 0;
                    $debit_credit_data_com->credit = 0;
                    $debit_credit_data_com->dr_cr = 'Dr';
                    $debit_credit_data_com->commission = (float) $request->commission_amount[$i] ?? 0;
                    $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                    $debit_credit_data_com->comm_level = 1;
                    $debit_credit_data_com->save();
                }
            }
        }

        return $debit_credit_data_sup;

    }

    public function getPurchaseReturnId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updatePurchaseReturn(Request $request, $id, $voucher_invoice)
    {

        $ip = $_SERVER['REMOTE_ADDR'];
        $data = TransactionMaster::findOrFail($id);
        $update_history = json_decode($data->other_details);
        $data->invoice_no = $request->invoice_no;
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
        $data->secret_narration = $request->secret_narration??'';
        $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        //multiple stock in data
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                if (! empty($request->stock_in_id[$i])) {
                    StockIn::where('stock_in_id', $request->stock_in_id[$i])->update([
                        'tran_date' => $request->invoice_date,
                        'godown_id' => $request->godown_id[$i] ?? 0,
                        'stock_item_id' => $request->product_id[$i] ?? 0,
                        'qty' => -(float) $request->qty[$i] ?? 0,
                        'rate' => -(float) $request->rate[$i] ?? 0,
                        'total' => -(float) $request->amount[$i] ?? 0,
                        'remark' => $request->remark[$i] ?? '',
                    ]);
                } else {
                    $stock_in_data = new StockIn();
                    $stock_in_data->tran_id = $id;
                    $stock_in_data->tran_date = $request->invoice_date;
                    $stock_in_data->stock_item_id = $request->product_id[$i];
                    $stock_in_data->godown_id = $request->godown_id[$i] ?? 0;
                    $stock_in_data->qty = -(float) $request->qty[$i] ?? 0;
                    $stock_in_data->rate = -(float) $request->rate[$i];
                    $stock_in_data->total = -(float) $request->amount[$i];
                    $stock_in_data->save();
                }
            }
        }

        //debit
        $debit_credit_data_sup = DebitCredit::findOrFail($request->debit_id);
        $debit_credit_data_sup->ledger_head_id = $request->debit_ledger_id;
        $debit_credit_data_sup->debit = (float) $request->get_without_commission;
        $debit_credit_data_sup->credit = 0;
        $debit_credit_data_sup->dr_cr = 'Dr';
        $debit_credit_data_sup->save();

        //credit
        $debit_credit_data = DebitCredit::findOrFail($request->credit_id);
        $debit_credit_data->ledger_head_id = $request->credit_ledger_id;
        $debit_credit_data->debit = 0;
        $debit_credit_data->dr_cr = 'Cr';
        $debit_credit_data->credit = (float) $request->total_credit ?? 0;
        $debit_credit_data->save();

        //single or multiple  commission
        if ($request->get_commission ?? 0) {
            for ($i = 0; $i < count(array_filter($request->get_commission)); $i++) {
                if (! empty($request->commission_debit_credit_id[$i])) {
                    if ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                        $debit_credit_data_com = DebitCredit::findOrFail($request->commission_debit_credit_id[$i]);
                        $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = 0;
                        $debit_credit_data_com->credit = (float) $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->dr_cr = 'Cr';
                        $debit_credit_data_com->commission = (float) $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->save();
                    } elseif ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                        $debit_credit_data_com = DebitCredit::findOrFail($request->commission_debit_credit_id[$i]);
                        $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = (float) $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->credit = 0;
                        $debit_credit_data_com->dr_cr = 'Dr';
                        $debit_credit_data_com->commission = (float) $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->save();
                    }
                } else {
                    if ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                        $debit_credit_data_com = new DebitCredit();
                        $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = 0;
                        $debit_credit_data_com->credit = (float) $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->dr_cr = 'Cr';
                        $debit_credit_data_com->commission = (float) $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->comm_level = 1;
                        $debit_credit_data_com->save();
                    } elseif ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                        $debit_credit_data_com = new DebitCredit();
                        $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = (float) $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->credit = 0;
                        $debit_credit_data_com->dr_cr = 'Dr';
                        $debit_credit_data_com->commission = (float) $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->comm_level = 1;
                        $debit_credit_data_com->save();
                    }
                }
            }
        }

        // id wise stock_in delete
        if (! empty($request->delete_stock_in_id)) {
            $delete_stock_in = explode(',', $request->delete_stock_in_id);
            for ($i = 0; $i < count($delete_stock_in); $i++) {
                if (!empty($delete_stock_in[$i])) {
                  StockIn::find($delete_stock_in[$i])->delete();
                }
            }
        }

        // id wise debit credit delete
        if (! empty($request->delete_debit_credit_id)) {
            $delete_debit_credit = explode(',', $request->delete_debit_credit_id);
            for ($i = 0; $i < count(array_filter($delete_debit_credit)); $i++) {
                if (!empty($delete_debit_credit[$i])) {
                  DebitCredit::find($delete_debit_credit[$i])->delete();
                }
            }
        }

        return $debit_credit_data_sup;
    }

    public function deletePurchaseReturn($id)
    {

        StockIn::where('tran_id', $id)->delete();
        DebitCredit::where('tran_id', $id)->delete();
        return  TransactionMaster::findOrFail($id)->delete();
    }
}
