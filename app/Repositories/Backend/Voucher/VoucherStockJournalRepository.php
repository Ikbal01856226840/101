<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderDelivery;

class VoucherStockJournalRepository implements VoucherStockJournalInterface
{

    public function storeStockJournal(Request $request, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        // tran master
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
        $data->dis_cen_id = $request->dis_cen_id ?? 0;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->ledger_id_optional = $request->ledger_id_optional ?? 0;
        $data->secret_narration = $request->secret_narration??'';
        $data->other_details = json_encode('Created on: ' . \Carbon\Carbon::now()->toDateTimeString() . 'By:' . Auth::user()->user_name . 'Ip:' . $ip);
        $data->save();
        $tran_id=$data->tran_id;
        // multiple stock out data
        $stock_out_data = [];
        if(!empty($request->product_out_id)){
            for ($i = 0; $i < count($request->product_out_id); $i++) {
                if (!empty($request->product_out_id[$i])) {
                    $stock_out_data[] = [
                        'tran_id' => $tran_id ?? exit,
                        'tran_date' => $request->invoice_date,
                        'stock_item_id' => $request->product_out_id[$i],
                        'godown_id' => $request->godown_out_id[$i] ?? 0,
                        'qty' => (float) $request->qty_out[$i] ?? 0,
                        'rate' => (float) $request->rate_out[$i],
                        'total' => (float) $request->amount_out[$i],
                        'remark' => $request->remark_out[$i] ?? '',
                    ];
                }
            }

            $data= StockOut::insert($stock_out_data);
        }

        // multiple stock in data
        $stock_in_data = [];
        if(!empty($request->product_in_id)){
            for ($i = 0; $i < count($request->product_in_id); $i++) {
                if (!empty($request->product_in_id[$i])) {
                    $stock_in_data[] = [
                        'tran_id' => $tran_id ?? exit,
                        'tran_date' => $request->invoice_date,
                        'stock_item_id' => $request->product_in_id[$i],
                        'godown_id' => $request->godown_in_id[$i] ?? 0,
                        'qty' => (float) $request->qty_in[$i] ?? 0,
                        'rate' => (float) $request->rate_in[$i],
                        'total' => (float) $request->amount_in[$i],
                        'remark' => $request->remark_in[$i] ?? '',
                    ];
                }
            }
            $data= StockIn::insert($stock_in_data);
        }
        return $data;

    }

    public function getStockJournalId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updateStockJournal(Request $request, $id, $voucher_invoice)
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
        if(unit_branch_first()!=151){
            $data->delivery_status=0;
         }
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->ledger_id_optional = $request->ledger_id_optional ?? 0;
        $data->secret_narration = $request->secret_narration??'';
        $data->other_details = json_encode($update_history . '<br> Updated on:' . \Carbon\Carbon::now()->toDateTimeString() . 'By:' . Auth::user()->user_name . 'Ip:' . $ip);
        $data->save();


     if (!empty($request->product_out_id)) {
            // multiple stock out data
            for ($i = 0; $i < count($request->product_out_id); $i++) {
                if (!empty($request->product_out_id[$i])) {
                    if (!empty($request->stock_out_id[$i])) {
                        StockOut::where('stock_out_id', $request->stock_out_id[$i])->update([
                            'tran_date' => $request->invoice_date,
                            'godown_id' => $request->godown_out_id[$i] ?? 0,
                            'stock_item_id' => $request->product_out_id[$i],
                            'qty' => (float) $request->qty_out[$i] ?? 0,
                            'rate' => (float) $request->rate_out[$i] ?? 0,
                            'total' => (float) $request->amount_out[$i] ?? 0,
                            'remark' => $request->remark_out[$i] ?? '',
                        ]);
                    } else {
                        $stock_out_data = new StockOut();
                        $stock_out_data->tran_id = $data->tran_id ?? exit;
                        $stock_out_data->tran_date = $request->invoice_date;
                        $stock_out_data->stock_item_id = $request->product_out_id[$i];
                        $stock_out_data->godown_id = $request->godown_out_id[$i] ?? 0;
                        $stock_out_data->qty = $request->qty_out[$i] ?? 0;
                        $stock_out_data->rate = $request->rate_out[$i];
                        $stock_out_data->total = $request->amount_out[$i];
                        $stock_out_data->remark = $request->remark_out[$i] ?? '';
                        $stock_out_data->save();
                    }
                }
            }
       }
       if (!empty($request->product_in_id)) {
            // multiple stock in data
            for ($i = 0; $i < count($request->product_in_id); $i++) {
                if (!empty($request->product_in_id[$i])) {
                    if (!empty($request->stock_in_id[$i])) {
                        StockIn::where('stock_in_id', $request->stock_in_id[$i])->update([
                            'tran_date' => $request->invoice_date,
                            'godown_id' => $request->godown_in_id[$i] ?? 0,
                            'stock_item_id' => $request->product_in_id[$i] ?? 0,
                            'qty' => (float) $request->qty_in[$i] ?? 0,
                            'rate' => (float) $request->rate_in[$i] ?? 0,
                            'total' => (float) $request->amount_in[$i] ?? 0,
                            'remark' => $request->remark_in[$i] ?? '',
                        ]);
                    } else {
                        $stock_in_data = new StockIn();
                        $stock_in_data->tran_id = $data->tran_id ?? exit;
                        $stock_in_data->tran_date = $request->invoice_date;
                        $stock_in_data->stock_item_id = $request->product_in_id[$i];
                        $stock_in_data->godown_id = $request->godown_in_id[$i] ?? 0;
                        $stock_in_data->qty = $request->qty_in[$i] ?? 0;
                        $stock_in_data->rate = $request->rate_in[$i];
                        $stock_in_data->total = $request->amount_in[$i];
                        $stock_in_data->remark = $request->remark_in[$i] ?? '';
                        $stock_in_data->save();
                    }
                }
            }
        }

        //single or multiple  delete
        if (!empty($request->delete_stock_out_id) || !empty($request->delete_stock_in_id)) {
            $delete_stock_out = explode(',', $request->delete_stock_out_id);
            $delete_stock_in = explode(',', $request->delete_stock_in_id);

            for ($i = 0; $i < count(($delete_stock_out)); $i++) {
                if (!empty($delete_stock_out[$i])) {
                    StockOut::find($delete_stock_out[$i])->delete();
                }
            }

            for ($i = 0; $i < count(($delete_stock_in)); $i++) {
                if (!empty($delete_stock_in[$i])) {
                    StockIn::find($delete_stock_in[$i])->delete();
                }
            }
        }
        if(unit_branch_first()!=151){
            OrderDelivery::where('tran_id', $id)->delete();
        }
        return $data;
    }

    public function deleteStockJournal($id)
    {

        StockIn::where('tran_id', $id)->delete();
        StockOut::where('tran_id', $id)->delete();
        return  TransactionMaster::findOrFail($id)->delete();
    }

    public function getLedgerTranIdWise($id)
    {
        return DB::table('transaction_master')->select('ledger_head.ledger_head_id', 'ledger_head.ledger_name')->LeftJoin('ledger_head', 'transaction_master.ledger_id_optional', '=', 'ledger_head.ledger_head_id')->where('transaction_master.tran_id', $id)->first();
    }
}
