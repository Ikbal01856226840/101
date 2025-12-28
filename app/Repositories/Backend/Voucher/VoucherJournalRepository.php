<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherJournalRepository implements VoucherJournalInterface
{

    public function storeJournal(Request $request, $voucher_invoice)
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
        $data->customer_id = $request->customer_id ?? 0;
        $data->dis_cen_id = $request->dis_cen_id ?? 0;
        $data->secret_narration = $request->secret_narration??'';
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        // debit credit insert
        $debit_credit = [];
        for ($i = 0; $i < count(($request->ledger_id)); $i++) {
            if (! empty($request->ledger_id[$i])) {
                $debit_credit_data = new DebitCredit();
                $debit_credit_data->tran_id = $data->tran_id ?? exit;
                $debit_credit_data->ledger_head_id = $request->ledger_id[$i];
                $debit_credit_data->debit = (float) $request->debit[$i] ?? 0;
                $debit_credit_data->credit = (float) $request->credit[$i] ?? 0;
                $debit_credit_data->remark = $request->remark[$i] ?? null;
                $debit_credit_data->dr_cr = $request->DrCr[$i];
                $debit_credit_data->save();
                $debit_credit[] = ['debit_credit_data' => $debit_credit_data->debit_credit_id, 'ledger_id' => $debit_credit_data->ledger_head_id];
            }
        }

        // stock in insert
        if (array_filter($request->product_id)) {
            for ($i = 0; $i < count($request->ledger_in_id); $i++) {
                if (! empty($request->ledger_in_id[$i])) {
                    $stock_in_data = new StockIn();
                    $stock_in_data->tran_id = $data->tran_id ?? exit;
                    $stock_in_data->tran_date = $request->invoice_date;
                    $stock_in_data->stock_item_id = $request->product_id[$i];
                    $stock_in_data->godown_id = $request->godown_id[$i] ?? 0;
                    $stock_in_data->qty = (float) $request->qty[$i] ?? 0;
                    $stock_in_data->rate = (float) $request->rate[$i];
                    $stock_in_data->total = (float) $request->amount[$i];
                    for ($j = 0; $j < count(array_filter($debit_credit)); $j++) {
                        if ($debit_credit[$j]['ledger_id'] == $request->ledger_in_id[$i]) {
                            $stock_in_data->debit_credit_id = $debit_credit[$j]['debit_credit_data'];
                        }
                    }
                    $stock_in_data->remark = $request->remark[$i] ?? '';
                    $stock_in_data->save();
                }
            }

            //  stock out insert
            for ($i = 0; $i < count($request->ledger_out_id); $i++) {
                if (! empty($request->ledger_out_id[$i])) {
                    $stock_out_data = new StockOut();
                    $stock_out_data->tran_id = $data->tran_id ?? exit;
                    $stock_out_data->tran_date = $request->invoice_date;
                    $stock_out_data->stock_item_id = $request->product_id[$i];
                    $stock_out_data->godown_id = $request->godown_id[$i] ?? 0;
                    $stock_out_data->qty = (float) $request->qty[$i] ?? 0;
                    $stock_out_data->rate = (float) $request->rate[$i];
                    $stock_out_data->total = (float) $request->amount[$i];
                    for ($j = 0; $j < count(array_filter($debit_credit)); $j++) {
                        if ($debit_credit[$j]['ledger_id'] == $request->ledger_out_id[$i]) {
                            $stock_out_data->debit_credit_id = $debit_credit[$j]['debit_credit_data'];
                        }
                    }
                    $stock_out_data->remark = $request->remark[$i] ?? 0;
                    $stock_out_data->save();
                }
            }
        }

        return $data;
    }

    public function getJournalId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updateJournal(Request $request, $id, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = TransactionMaster::findOrFail($id);
        $data->invoice_no = $request->invoice_no;
        $data->ref_no = $request->ref_no;
        $update_history = json_decode($data->other_details);
        $data->transaction_date = $request->invoice_date;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->voucher_id = $request->voucher_id;
        $data->narration = $request->narration;
        $data->customer_id = $request->customer_id ?? 0;
        $data->dis_cen_id = $request->dis_cen_id ?? 0;
        $data->secret_narration = $request->secret_narration??'';
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        // debit credit insert or update
        for ($i = 0; $i < count(($request->ledger_id)); $i++) {
            if (! empty($request->debit_credit_id[$i])) {
                if (! empty($request->ledger_id[$i])) {
                    $debit_credit_data = DebitCredit::find($request->debit_credit_id[$i]);
                    $debit_credit_data->ledger_head_id = $request->ledger_id[$i];
                    $debit_credit_data->debit = (float) $request->debit[$i] ?? 0;
                    $debit_credit_data->credit = (float) $request->credit[$i] ?? 0;
                    $debit_credit_data->remark = $request->remark[$i] ?? null;
                    $debit_credit_data->dr_cr = $request->DrCr[$i];
                    $debit_credit_data->save();
                }
            } else {
                if (! empty($request->ledger_id[$i])) {
                    $debit_credit_data = new DebitCredit();
                    $debit_credit_data->tran_id = $data->tran_id ?? exit;
                    $debit_credit_data->ledger_head_id = $request->ledger_id[$i];
                    $debit_credit_data->debit = (float) $request->debit[$i] ?? 0;
                    $debit_credit_data->credit = (float) $request->credit[$i] ?? 0;
                    $debit_credit_data->remark = $request->remark[$i] ?? null;
                    $debit_credit_data->dr_cr = $request->DrCr[$i];
                    $debit_credit_data->save();
                }
            }
        }

        // stock in insert or update
        if (array_filter($request->product_id)) {
            for ($i = 0; $i < count($request->product_id); $i++) {
                if (! empty($request->stock_in_id[$i])) {
                    StockIn::where('stock_in_id', $request->stock_in_id[$i])->update([
                        'tran_date' => $request->invoice_date,
                        'godown_id' => $request->godown_id[$i] ?? 0,
                        'stock_item_id' => $request->product_id[$i] ?? 0,
                        'qty' => (float) $request->qty[$i] ?? 0,
                        'rate' => (float) $request->rate[$i] ?? 0,
                        'total' => (float) $request->amount[$i] ?? 0,
                        'remark' => $request->remark[$i] ?? '',
                    ]);

                } else {
                    if (! empty($request->ledger_in_id[$i])) {
                        $stock_in_data = new StockIn();
                        $stock_in_data->tran_id = $data->tran_id ?? exit;
                        $stock_in_data->tran_date = $request->invoice_date;
                        $stock_in_data->stock_item_id = $request->product_id[$i];
                        $stock_in_data->godown_id = $request->godown_id[$i] ?? 0;
                        $stock_in_data->qty = (float) $request->qty[$i] ?? 0;
                        $stock_in_data->rate = (float) $request->rate[$i];
                        $stock_in_data->total = (float) $request->amount[$i];
                        $stock_in_data->remark = $request->remark[$i] ?? '';
                        $stock_in_data->save();
                    }
                }
            }

            //  stock out insert or update
            for ($i = 0; $i < count($request->product_id); $i++) {
                if (! empty($request->stock_out_id[$i])) {
                    StockOut::where('stock_out_id', $request->stock_out_id[$i])->update([
                        'tran_date' => $request->invoice_date,
                        'godown_id' => $request->godown_id[$i],
                        'stock_item_id' => $request->product_id[$i],
                        'qty' => (float) $request->qty[$i] ?? 0,
                        'rate' => (float) $request->rate[$i] ?? 0,
                        'total' => (float) $request->amount[$i] ?? 0,
                        'remark' => $request->remark[$i] ?? '',
                    ]);
                } else {
                    if (! empty($request->ledger_out_id[$i])) {
                        $stock_out_data = new StockOut();
                        $stock_out_data->tran_id = $data->tran_id ?? exit;
                        $stock_out_data->tran_date = $request->invoice_date;
                        $stock_out_data->stock_item_id = $request->product_id[$i];
                        $stock_out_data->godown_id = $request->godown_id[$i] ?? 0;
                        $stock_out_data->qty = (float) $request->qty[$i] ?? 0;
                        $stock_out_data->rate = (float) $request->rate[$i];
                        $stock_out_data->total = (float) $request->amount[$i];
                        $stock_out_data->remark = $request->remark[$i] ?? '';
                        $stock_out_data->save();
                    }
                }
            }
        }
         //multiple or single debit credit delete
         if (! empty($request->delete_debit_credit_id)) {
            $delete_debit_credit = explode(',', $request->delete_debit_credit_id);
            for ($i = 0; $i < count($delete_debit_credit); $i++) {
                if (!empty($delete_debit_credit[$i])) {
                  DebitCredit::find($delete_debit_credit[$i])->delete();
                }
            }
        }

        //single or multiple  delete
        if (!empty($request->delete_stock_out_id)) {
            $delete_stock_out = explode(',', $request->delete_stock_out_id);
            for ($i = 0; $i < count($delete_stock_out); $i++) {
                if (!empty($delete_stock_out[$i])) {
                  StockOut::find($delete_stock_out[$i])->delete();
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

        return $debit_credit_data;
    }

    public function deleteJournal($id)
    {
        StockOut::where('tran_id', $id)->delete();
        DebitCredit::where('tran_id', $id)->delete();
        StockIn::where('tran_id', $id)->delete();

        return TransactionMaster::findOrFail($id)->delete();
    }

    public function getDebitCreditAndStockInStockOut($tran_in)
    {
        $params = [];
        $query="SELECT stock_in.stock_in_id as in_stock_in_id,
                        stock_in.qty as in_qty,
                        stock_in.rate as in_rate,
                        stock_in.total as in_total,
                        stock_in.stock_item_id as in_item_id,
                        stock_in.product_name as in_product_name,
                        stock_in.godown_id as in_godown_id,
                        stock_in.godown_name as in_godown_name,
                        stock_out.stock_out_id as out_stock_in_id,
                        stock_out.rate,
                        stock_out.qty as out_qty,
                        stock_out.total as out_total,
                        stock_out.stock_item_id as out_item_id,
                        stock_out.product_name as  out_product_name,
                        stock_out.godown_id as out_godown_id,
                        stock_out.godown_name as out_godowns_name ,
                        debit_credit.debit_credit_id,
                        debit_credit.dr_cr,
                        debit_credit.ledger_head_id,
                        debit_credit.debit,
                        debit_credit.credit,
                        debit_credit.remark,
                        ledger_head.ledger_name
                FROM debit_credit
                LEFT JOIN ( SELECT
                            stock_in.stock_in_id,
                            stock_in.qty,
                            stock_in.rate,
                            stock_in.total,
                            stock_in.stock_item_id,
                            stock_item.product_name ,
                            stock_in.debit_credit_id,
                            godowns_in.godown_id,
                            godowns_in.godown_name
                        FROM stock_in
                        LEFT JOIN stock_item  ON stock_in.stock_item_id=stock_item.stock_item_id
                        LEFT JOIN godowns as godowns_in  ON stock_in.godown_id=godowns_in.godown_id
                        WHERE stock_in.tran_id=:tran_in_1 ) AS stock_in
                ON debit_credit.debit_credit_id=stock_in.debit_credit_id
                LEFT JOIN ( SELECT
                            stock_out.stock_out_id,
                            stock_out.qty,
                            stock_out.rate,
                            stock_out.total,
                            stock_out.stock_item_id,
                            stock_item.product_name,
                            stock_out.debit_credit_id,
                            godowns_out.godown_id,
                            godowns_out.godown_name
                        FROM stock_out
                        LEFT JOIN stock_item  ON stock_out.stock_item_id=stock_item.stock_item_id
                        LEFT JOIN godowns as godowns_out  ON stock_out.godown_id=godowns_out.godown_id
                        WHERE stock_out.tran_id=:tran_in_2 ) AS stock_out
                ON debit_credit.debit_credit_id=stock_out.debit_credit_id
                LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                WHERE debit_credit.tran_id=:tran_in_3 ORDER BY debit_credit.debit_credit_id
                        ";
                $params['tran_in_1'] =$tran_in;
                $params['tran_in_2'] =$tran_in;
                $params['tran_in_3'] =$tran_in;

                return DB::select($query,$params);
    }
}
