<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherPaymentRepository implements VoucherPaymentInterface
{
    
    public function storeVoucherPayment($request, $voucher_invoice)
    {

        $ip = $_SERVER['REMOTE_ADDR'];
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
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        //multiple debit credit
        $debit_credit_data = [];
        for ($i = 0; $i < count($request->ledger_id); $i++) {
            if (! empty($request->ledger_id[$i])) {
                $debit_credit_data[] = [
                    'tran_id' => $data->tran_id,
                    'ledger_head_id' => $request->ledger_id[$i],
                    'debit' => (float) $request->debit[$i] ?? 0,
                    'credit' => (float) $request->credit[$i] ?? 0,
                    'remark' => $request->remark[$i] ?? null,
                    'dr_cr' => $request->DrCr[$i],
                ];
            }
        }

        return DebitCredit::insert($debit_credit_data);
    }

    public function getVoucherPaymentId($id)
    {

        return TransactionMaster::findOrFail($id);
    }

    public function updateVoucherPayment(Request $request, $id, $voucher_invoice)
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
        $data->narration = $request->narration;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        //multiple debit credit
        for ($i = 0; $i < count($request->ledger_id); $i++) {
            if (! empty($request->ledger_id[$i])) {
                if (! empty($request->debit_credit_id[$i])) {
                    $debit_credit_data = DebitCredit::find($request->debit_credit_id[$i]);
                    $debit_credit_data->ledger_head_id = $request->ledger_id[$i];
                    $debit_credit_data->debit = (float) $request->debit[$i] ?? 0;
                    $debit_credit_data->credit = (float) $request->credit[$i] ?? 0;
                    $debit_credit_data->remark = $request->remark[$i] ?? null;
                    $debit_credit_data->dr_cr = $request->DrCr[$i];
                    $debit_credit_data->save();
                } else {
                    $debit_credit = new DebitCredit();
                    $debit_credit->tran_id = $id;
                    $debit_credit->ledger_head_id = $request->ledger_id[$i];
                    $debit_credit->debit = (float) $request->debit[$i] ?? 0;
                    $debit_credit->credit = (float) $request->credit[$i] ?? 0;
                    $debit_credit->remark = $request->remark[$i] ?? null;
                    $debit_credit->dr_cr = $request->DrCr[$i];
                    $debit_credit->save();
                }
            }
        }

        //multiple or single debit credit delete
        if (! empty($request->delete_debit_credit_id)) {
            $delete_debit_credit = explode(',', $request->delete_debit_credit_id);
            for ($i = 0; $i < count(array_filter($delete_debit_credit)); $i++) {
                DebitCredit::find($delete_debit_credit[$i])->delete();
            }
        }

        return $debit_credit_data;
    }

    public function deleteVoucherPayment($id)
    {
        
        DebitCredit::where('tran_id', $id)->delete();
        return TransactionMaster::findOrFail($id)->delete();
    }

    public function editDebitCredit($id)
    {

        return DB::table('debit_credit')
            ->select('debit_credit.debit_credit_id', 'debit_credit.ledger_head_id', 'ledger_head.ledger_name', 'debit_credit.debit', 'debit_credit.credit', 'debit_credit.dr_cr')
            ->leftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
            ->where('debit_credit.tran_id', $id)
            ->get();
    }
}
