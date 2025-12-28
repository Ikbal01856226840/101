<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockOut;
use App\Models\TransactionMaster;
use App\Services\DebitCredit\DebitCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherPOSRepository implements VoucherPOSInterface
{
    private $debit_credit;

    public function __construct(DebitCreditService $debit_credit_data)
    {
        $this->debit_credit = $debit_credit_data;
    }


    public function storePOS(Request $request, $voucher_invoice)
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
        $data->payment_type = $request->payment_type ?? 0;
        $data->card_no = $request->card_no ?? 0;
        $data->appr_code = $request->appr_code ?? 0;
        $data->received_amount = $request->amount_received ?? 0;
        $data->dis_cen_id=$request->dis_cen_id;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name=Auth::user()->user_name;
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
                    'type_out'=>'POS',
                    'godown_id' => $request->godown_id[$i] ?? 0,
                    'qty' => (int) $request->qty[$i] ?? 0,
                    'rate' => (float) $request->rate[$i] ?? 0,
                    'total' => (float) $request->amount[$i] ?? 0,
                    'disc'=> (float) $request->disc[$i] ?? 0,
                ];
            }
        }
        StockOut::insert($stock_out_data);

        if ($request->credit_ledger_id) {
            // get  credit data
            $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->credit_ledger_id, 0,$request->total_amount, 'Cr');
        }
        if ($request->debit_ledger_id) {
            // get  debit data
            $this->debit_credit->debitCreditStore($data->tran_id ?? exit, $request->debit_ledger_id, $request->total_amount, 0, 'Dr');
        }

        return $data;

    }

    public function getPOSId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updatePOS(Request $request, $id, $voucher_invoice)
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
        $data->payment_type = $request->payment_type ?? 0;
        $data->card_no = $request->card_no ?? 0;
        $data->appr_code = $request->appr_code ?? 0;
        $data->received_amount = $request->amount_received ?? 0;
        $data->dis_cen_id=$request->dis_cen_id;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();
        // get  credit data
        $this->debit_credit->debitCreditUpdate($request->credit_id, $data->tran_id ?? exit, $request->credit_ledger_id, 0,$request->total_amount, 'Cr');

        // get  debit data
        $this->debit_credit->debitCreditUpdate($request->debit_id, $data->tran_id ?? exit, $request->debit_ledger_id, $request->total_amount, 0, 'Dr');

        // multiple stock out data
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                if (! empty($request->stock_out_id[$i])) {
                    StockOut::where('stock_out_id', $request->stock_out_id[$i])->update([
                        'tran_date' => $request->invoice_date,
                        'stock_item_id' => $request->product_id[$i],
                        'godown_id' => $request->godown_id[$i] ?? 0,
                        'qty' => (int) $request->qty[$i] ?? 0,
                        'rate' => (float) $request->rate[$i] ?? 0,
                        'total' => (float) $request->amount[$i] ?? 0,
                        'disc'=> (float) $request->disc[$i] ?? 0,
                    ]);
                } else {
                    $stock_out_data = new StockOut();
                    $stock_out_data->tran_id= $data->tran_id ?? exit;
                    $stock_out_data->tran_date = $request->invoice_date;
                    $stock_out_data->stock_item_id = $request->product_id[$i];
                    $stock_out_data->godown_id = $request->godown_id[$i] ?? 0;
                    $stock_out_data->type_out='POS';
                    $stock_out_data->qty = (int) $request->qty[$i] ?? 0;
                    $stock_out_data->rate = (float) $request->rate[$i];
                    $stock_out_data->total = (float) $request->amount[$i];
                    $stock_out_data->disc= (float) $request->disc[$i] ?? 0;
                    $stock_out_data->save();
                }
            }
        }

        // id wise stock_in delete
        if (! empty($request->delete_stock_out_id)) {
            $delete_stock_out = explode(',', $request->delete_stock_out_id);
            for ($i = 0; $i < count(array_filter($delete_stock_out)); $i++) {
                StockOut::find($delete_stock_out[$i])->delete();
            }
        }

        return $data;
    }

    public function deletePOS($id)
    {
        StockOut::where('tran_id', $id)->delete();
        return TransactionMaster::findOrFail($id)->delete();
    }

}
