<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockItemCommissionVoucher;
use App\Models\TransactionMaster;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherCommissionRepository implements VoucherCommissionInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }


    public function storeVoucherCommission($request, $voucher_invoice)
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
        $data->commission_from_date = $request->commission_from_date;
        $data->commission_to_date = $request->commission_to_date;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        //debit data
        $debit_credit_data = new DebitCredit();
        $debit_credit_data->tran_id = $data->tran_id ?? exit;
        $debit_credit_data->ledger_head_id = $request->commission_ledger_id;
        $debit_credit_data->debit = (float) $request->total_commission_per ?? 0;
        $debit_credit_data->credit = 0;
        $debit_credit_data->dr_cr = 'Dr';
        $debit_credit_data->save();

        //credit data
        $debit_data = new DebitCredit();
        $debit_data->tran_id = $data->tran_id ?? exit;
        $debit_data->ledger_head_id = $request->party_ledger_id;
        $debit_data->debit = 0;
        $debit_data->credit = (float) $request->total_commission_per ?? 0;
        $debit_credit_data->dr_cr = 'Cr';
        $debit_data->save();
        $stock_out_data = [];

        //credit data stock item commission
        for ($i = 0; $i < count(array_filter($request->stock_item_id)); $i++) {
            if (! empty($request->commission_parqty[$i])) {
                $stock_out_data[] = [
                    'tran_id' => $data->tran_id ?? exit,
                    'tran_date' => $data->transaction_date,
                    'stock_item_id' => $request->stock_item_id[$i],
                    'com_qty' => (int) $request->parqty[$i] ?? 0,
                    'com_rate' => (float) $request->commission_parqty[$i],
                    'com_percent' => (float) $request->commission_sale_value[$i],
                    'com_total' => (float) $request->commission_amount[$i],
                ];
            }
        }

        return StockItemCommissionVoucher::insert($stock_out_data);
    }

    public function getVoucherCommissionId($id)
    {

        return TransactionMaster::findOrFail($id);
    }

    public function updateVoucherCommission(Request $request, $id, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = TransactionMaster::findOrFail($id);
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
        $data->commission_from_date = $request->commission_from_date;
        $data->commission_to_date = $request->commission_to_date;
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        //debit data
        $debit_credit_data = DebitCredit::find($request->debit_id);
        $debit_credit_data->ledger_head_id = $request->commission_ledger_id;
        $debit_credit_data->debit = (float) $request->total_commission_per ?? 0;
        $debit_credit_data->credit = 0;
        $debit_credit_data->dr_cr = 'Dr';
        $debit_credit_data->save();

        //credit data
        $debit_data = DebitCredit::find($request->credit_id);
        $debit_data->ledger_head_id = $request->party_ledger_id;
        $debit_data->debit = 0;
        $debit_data->credit = (float) $request->total_commission_per ?? 0;
        $debit_data->dr_cr = 'Cr';
        $debit_data->save();

        //credit data stock item commission
        for ($i = 0; $i < count(array_filter($request->stock_item_id)); $i++) {
            if (! is_null($request->stock_comm_id[$i]) || ! empty($request->stock_comm_id[$i])) {
                if (! empty($request->commission_parqty[$i])) {
                    $stock_out_data = StockItemCommissionVoucher::where('stock_comm_id', $request->stock_comm_id[$i])->first();
                    if (isset($stock_out_data) && ! is_null($stock_out_data)) {
                        $stock_out_data->com_qty = (int) $request->parqty[$i] ?? 0;
                        $stock_out_data->com_rate = (float) $request->commission_parqty[$i];
                        $stock_out_data->com_percent = (float) $request->commission_sale_value[$i];
                        $stock_out_data->com_total = (float) $request->commission_amount[$i];
                        $stock_out_data->save();
                    } else {
                        if (! empty($request->commission_parqty[$i])) {
                            $stock_out_data1 = new StockItemCommissionVoucher();
                            $stock_out_data1->tran_id = $data->tran_id;
                            $stock_out_data1->stock_item_id = $request->stock_item_id[$i];
                            $stock_out_data1->com_qty = (int) $request->parqty[$i] ?? 0;
                            $stock_out_data1->com_rate = (float) $request->commission_parqty[$i];
                            $stock_out_data1->com_percent = (float) $request->commission_sale_value[$i];
                            $stock_out_data1->com_total = (float) $request->commission_amount[$i];
                            $stock_out_data1->save();
                        }
                    }

                }
            }

        }

        return $debit_credit_data;
    }

    public function deleteVoucherCommission($id)
    {
        DebitCredit::where('tran_id', $id)->delete();
        StockItemCommissionVoucher::where('tran_id', $id)->delete();
        return TransactionMaster::findOrFail($id)->delete();
    }

    public function getCommission($request)
    {
        return DB::table('transaction_master')
            ->select('stock_item.product_name', 'stock_out.stock_item_id', DB::raw('SUM(stock_out.qty) AS qty'), DB::raw('SUM(stock_out.rate) AS rate'), DB::raw('SUM(stock_out.total) AS total'), 'stock_group.stock_group_id', 'stock_group.under', 'stock_group.stock_group_name')
            ->leftJoin('debit_credit', 'transaction_master.tran_id', '=', 'debit_credit.tran_id')
            ->leftJoin('stock_out', 'transaction_master.tran_id', '=', 'stock_out.tran_id')
            ->leftJoin('stock_item', 'stock_out.stock_item_id', '=', 'stock_item.stock_item_id')
            ->rightJoin('stock_group', 'stock_item.stock_group_id', '=', 'stock_group.stock_group_id')
            ->where('debit_credit.ledger_head_id', $request->party_ledger_id)
            ->whereBetween('transaction_master.transaction_date', [$request->from_date, $request->to_date])
            ->groupBy('stock_out.stock_item_id')
            ->get();
    }

    public function stockItemCommissionGetId($id, $party_ledger_id, $request)
    {
        return DB::table('transaction_master')
            ->select('stock_item.product_name', 'stock_out.stock_item_id', DB::raw('SUM(stock_out.qty) AS qty'), DB::raw('SUM(stock_out.rate) AS rate'), DB::raw('SUM(stock_out.total) AS total'), 'stock_group.stock_group_id', 'stock_group.under', 'stock_group.stock_group_name', 'stock_item_commission.stock_comm_id', 'stock_item_commission.com_qty', 'stock_item_commission.com_rate', 'stock_item_commission.com_percent', 'stock_item_commission.com_total')
            ->leftJoin('debit_credit', 'transaction_master.tran_id', '=', 'debit_credit.tran_id')
            ->leftJoin('stock_out', 'transaction_master.tran_id', '=', 'stock_out.tran_id')
            ->leftJoin('stock_item', 'stock_out.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('stock_item_commission', 'stock_item.stock_item_id', '=', DB::raw('stock_item_commission.stock_item_id  AND stock_item_commission.tran_id='.$id))
            ->rightJoin('stock_group', 'stock_item.stock_group_id', '=', 'stock_group.stock_group_id')
            ->where('debit_credit.ledger_head_id', $party_ledger_id)
            ->whereBetween('transaction_master.transaction_date', [$request->commission_from_date, $request->commission_to_date])
            ->groupBy('stock_out.stock_item_id')
            ->get();
    }
}
