<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockOut;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherSalesRepository implements VoucherSalesInterface
{

    public function storeSales(Request $request, $voucher_invoice)
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
        $data->other_details = json_encode('Created on: ' . \Carbon\Carbon::now()->toDateTimeString() . 'By:' . Auth::user()->user_name . 'Ip:' . $ip);
        $data->save();

        // multiple stock out data
        $stock_out_data = [];
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (!empty($request->product_id[$i])) {
                $stock_out_data[] = [
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
        StockOut::insert($stock_out_data);

        //single  debit
        $debit_credit_data_sup = new DebitCredit();
        $debit_credit_data_sup->tran_id = $data->tran_id ?? exit;
        $debit_credit_data_sup->ledger_head_id = $request->credit_ledger_id;
        $debit_credit_data_sup->debit = (float) $request->get_without_commission;
        $debit_credit_data_sup->credit = 0;
        $debit_credit_data_sup->dr_cr = 'Dr';
        $debit_credit_data_sup->save();

        //single or multiple  credit
        if ($request->commission_is == 1) {
            $credit_data = [];
            for ($i = 0; $i < count($request->product_id); $i++) {
                if (!empty($request->product_id[$i])) {
                    $credit_data[] = [
                        'tran_id' => $data->tran_id ?? exit,
                        'ledger_head_id' => $request->debit_ledger_id[$i],
                        'debit' => 0,
                        'dr_cr' => 'Cr',
                        'credit' => (float) $request->amount[$i] ?? 0,
                        'stock_item_id' => $request->product_id[$i] ?? 0,
                    ];
                }
            }
            DebitCredit::insert($credit_data);
        } else {
            $debit_credit_data = new DebitCredit();
            $debit_credit_data->tran_id = $data->tran_id ?? exit;
            $debit_credit_data->ledger_head_id = $request->debit_ledger_id;
            $debit_credit_data->debit = 0;
            $debit_credit_data->dr_cr = 'Cr';
            $debit_credit_data->credit = (float) $request->total_amount ?? 0;
            $debit_credit_data->save();
        }

        //single or multiple product wise commission
        if ($request->commission_is == 1) {
            $debit_credit_data_com = [];
            for ($i = 0; $i < count($request->product_id); $i++) {
                if (!empty($request->product_id[$i]) && !empty($request->product_wise_commission_ledger[$i])) {
                    if ($request->product_wise_commission_cal[$i] == 2 || $request->product_wise_commission_cal[$i] == 4) {
                        $debit_credit_data_com[] = [
                            'tran_id' => $data->tran_id ?? exit,
                            'ledger_head_id' => $request->product_wise_commission_ledger[$i] ?? 0,
                            'debit' => 0,
                            'credit' => (float) $request->product_wise_get_commission[$i] ?? 0,
                            'dr_cr' => 'Cr',
                            'commission' => (float) $request->product_wise_commission_amount[$i] ?? 0,
                            'commission_type' => $request->product_wise_commission_cal[$i] ?? 0,
                            'stock_item_id' => $request->product_id[$i] ?? 0,
                            'comm_level' => 2

                        ];
                    } elseif ($request->product_wise_commission_cal[$i] == 1 || $request->product_wise_commission_cal[$i] == 3) {
                        $debit_credit_data_com[] = [
                            'tran_id' => $data->tran_id ?? exit,
                            'ledger_head_id' => $request->product_wise_commission_ledger[$i] ?? 0,
                            'debit' => (float) $request->product_wise_get_commission[$i] ?? 0,
                            'credit' => 0,
                            'dr_cr' => 'Dr',
                            'commission' => (float) $request->product_wise_commission_amount[$i] ?? 0,
                            'commission_type' => $request->product_wise_commission_cal[$i] ?? 0,
                            'stock_item_id' => $request->product_id[$i] ?? 0,
                            'comm_level' => 2

                        ];
                    }
                }
            }
            DebitCredit::insert($debit_credit_data_com);
        }

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

    public function getSalesId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updateSales(Request $request, $id, $voucher_invoice)
    {

        $ip = $_SERVER['REMOTE_ADDR'];
        $data = TransactionMaster::findOrFail($id);
        $update_history = json_decode($data->other_details);
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
        $data->other_details = json_encode($update_history . '<br> Updated on:' . \Carbon\Carbon::now()->toDateTimeString() . 'By:' . Auth::user()->user_name . 'Ip:' . $ip);
        $data->save();

        // multiple stock out data
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (!empty($request->product_id[$i])) {
                if (!empty($request->stock_out_id[$i])) {
                    StockOut::where('stock_out_id', $request->stock_out_id[$i])->update([
                        'tran_date' => $request->invoice_date,
                        'godown_id' => $request->godown_id[$i],
                        'stock_item_id' => $request->product_id[$i],
                        'qty' => (int) $request->qty[$i] ?? 0,
                        'rate' => (float) $request->rate[$i] ?? 0,
                        'total' => (float) $request->amount[$i] ?? 0,
                        'remark' => $request->remark[$i] ?? 0,
                    ]);
                } else {
                    $stock_out_data = new StockOut();
                    $stock_out_data->tran_id = $id;
                    $stock_out_data->tran_date = $request->invoice_date;
                    $stock_out_data->stock_item_id = $request->product_id[$i];
                    $stock_out_data->godown_id = $request->godown_id[$i] ?? 0;
                    $stock_out_data->qty = (int) $request->qty[$i] ?? 0;
                    $stock_out_data->rate = (float) $request->rate[$i];
                    $stock_out_data->total = (float) $request->amount[$i];
                    $stock_out_data->remark = $request->remark[$i];
                    $stock_out_data->save();
                }
            }
        }

        //single  debit
        $debit_credit_data_sup = DebitCredit::findOrFail($request->credit_id);
        $debit_credit_data_sup->tran_id = $data->tran_id;
        $debit_credit_data_sup->ledger_head_id = $request->credit_ledger_id;
        $debit_credit_data_sup->debit = (float) $request->get_without_commission;
        $debit_credit_data_sup->credit = 0;
        $debit_credit_data_sup->dr_cr = 'Dr';
        $debit_credit_data_sup->save();

        //single or multiple  credit
        if ($request->commission_is == 1) {
            for ($i = 0; $i < count($request->product_id); $i++) {
                if (!empty($request->product_id[$i])) {
                    if (!empty($request->debit_id[$i])) {
                        $debit_credit_data = DebitCredit::findOrFail($request->debit_id[$i]);
                        $debit_credit_data->tran_id = $data->tran_id;
                        $debit_credit_data->ledger_head_id = $request->debit_ledger_id[$i];
                        $debit_credit_data->debit = 0;
                        $debit_credit_data->dr_cr = 'Cr';
                        $debit_credit_data->credit = (float) $request->amount[$i] ?? 0;
                        $debit_credit_data->stock_item_id = $request->product_id[$i] ?? 0;
                        $debit_credit_data->save();
                    } else {
                        $debit_credit_data = new DebitCredit();
                        $debit_credit_data->tran_id = $data->tran_id ?? exit;
                        $debit_credit_data->ledger_head_id = $request->debit_ledger_id[$i];
                        $debit_credit_data->debit = 0;
                        $debit_credit_data->dr_cr = 'Cr';
                        $debit_credit_data->credit = (float) $request->amount[$i] ?? 0;
                        $debit_credit_data->stock_item_id = $request->product_id[$i] ?? 0;
                        $debit_credit_data->save();
                    }
                }
            }
        } else {
            $debit_credit_data = DebitCredit::findOrFail($request->debit_id);
            $debit_credit_data->tran_id = $data->tran_id;
            $debit_credit_data->ledger_head_id = $request->debit_ledger_id;
            $debit_credit_data->debit = 0;
            $debit_credit_data->dr_cr = 'Cr';
            $debit_credit_data->credit = (float) $request->total_amount ?? 0;
            $debit_credit_data->save();
        }

        //single or multiple product wise commission
        if ($request->commission_is == 1) {
            for ($i = 0; $i < count(array_filter($request->product_wise_commission_ledger)); $i++) {
                if (!empty($request->product_id[$i])) {
                    if ($request->product_wise_commission_cal[$i] == 2 || $request->product_wise_commission_cal[$i] == 4) {
                        if (!empty($request->commission_id[$i])) {
                            $debit_credit_data_com = DebitCredit::findOrFail($request->commission_id[$i]);
                            $debit_credit_data_com->ledger_head_id = $request->product_wise_commission_ledger[$i] ?? 0;
                            $debit_credit_data_com->debit = 0;
                            $debit_credit_data_com->credit = (float) $request->product_wise_get_commission[$i] ?? 0;
                            $debit_credit_data_com->dr_cr = 'Cr';
                            $debit_credit_data_com->commission = (float) $request->product_wise_commission_amount[$i] ?? 0;
                            $debit_credit_data_com->commission_type = $request->product_wise_commission_cal[$i] ?? 0;
                            $debit_credit_data_com->stock_item_id = $request->product_id[$i] ?? 0;
                            $debit_credit_data_com->save();
                        } else {
                            $debit_credit_data_com = new DebitCredit();
                            $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                            $debit_credit_data_com->ledger_head_id = $request->product_wise_commission_ledger[$i] ?? 0;
                            $debit_credit_data_com->debit = 0;
                            $debit_credit_data_com->credit = (float) $request->product_wise_get_commission[$i] ?? 0;
                            $debit_credit_data_com->dr_cr = 'Cr';
                            $debit_credit_data_com->commission = (float) $request->product_wise_commission_amount[$i] ?? 0;
                            $debit_credit_data_com->commission_type = $request->product_wise_commission_cal[$i] ?? 0;
                            $debit_credit_data_com->comm_level = 2;
                            $debit_credit_data_com->stock_item_id = $request->product_id[$i] ?? 0;
                            $debit_credit_data_com->save();
                        }
                    } elseif ($request->product_wise_commission_cal[$i] == 1 || $request->product_wise_commission_cal[$i] == 3) {
                        if (!empty($request->commission_id[$i])) {
                            $debit_credit_data_com = DebitCredit::findOrFail($request->commission_id[$i]);
                            $debit_credit_data_com->ledger_head_id = $request->product_wise_commission_ledger[$i] ?? 0;
                            $debit_credit_data_com->debit = (float) $request->product_wise_get_commission[$i] ?? 0;
                            $debit_credit_data_com->credit = 0;
                            $debit_credit_data_com->dr_cr = 'Dr';
                            $debit_credit_data_com->commission = (float) $request->product_wise_commission_amount[$i] ?? 0;
                            $debit_credit_data_com->commission_type = $request->product_wise_commission_cal[$i] ?? 0;
                            $debit_credit_data_com->stock_item_id = $request->product_id[$i] ?? 0;
                            $debit_credit_data_com->save();
                        } else {
                            $debit_credit_data_com = new DebitCredit();
                            $debit_credit_data_com->tran_id = $data->tran_id ?? exit;
                            $debit_credit_data_com->ledger_head_id = $request->product_wise_commission_ledger[$i] ?? 0;
                            $debit_credit_data_com->debit = (float) $request->product_wise_get_commission[$i] ?? 0;
                            $debit_credit_data_com->credit = 0;
                            $debit_credit_data_com->dr_cr = 'Dr';
                            $debit_credit_data_com->commission = (float) $request->product_wise_commission_amount[$i] ?? 0;
                            $debit_credit_data_com->commission_type = $request->product_wise_commission_cal[$i] ?? 0;
                            $debit_credit_data_com->comm_level = 2;
                            $debit_credit_data_com->stock_item_id = $request->product_id[$i] ?? 0;
                            $debit_credit_data_com->save();
                        }
                    }
                }
            }
        }

        //single or multiple  commission
        if ($request->get_commission ?? 0) {
            for ($i = 0; $i < count(array_filter($request->get_commission)); $i++) {
                if (!empty($request->commission_debit_credit_id[$i])) {
                    if ($request->commision_cal[$i] == 2 || $request->commision_cal[$i] == 4) {
                        $debit_credit_data_com = DebitCredit::findOrFail($request->commission_debit_credit_id[$i]);
                        $debit_credit_data_com->tran_id = $data->tran_id;
                        $debit_credit_data_com->ledger_head_id = $request->commission_ledger_id[$i] ?? 0;
                        $debit_credit_data_com->debit = 0;
                        $debit_credit_data_com->credit = (float) $request->get_commission[$i] ?? 0;
                        $debit_credit_data_com->dr_cr = 'Cr';
                        $debit_credit_data_com->commission = (float) $request->commission_amount[$i] ?? 0;
                        $debit_credit_data_com->commission_type = $request->commision_cal[$i] ?? 0;
                        $debit_credit_data_com->save();
                    } elseif ($request->commision_cal[$i] == 1 || $request->commision_cal[$i] == 3) {
                        $debit_credit_data_com = DebitCredit::findOrFail($request->commission_debit_credit_id[$i]);
                        $debit_credit_data_com->tran_id = $data->tran_id;
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

        //single or multiple  delete
        if (!empty($request->delete_stock_out_id)) {
            $delete_stock_out = explode(',', $request->delete_stock_out_id);
            for ($i = 0; $i < count(array_filter($delete_stock_out)); $i++) {
                StockOut::find($delete_stock_out[$i])->delete();
            }
        }
        if (!empty($request->delete_debit_credit_id)) {
            $delete_debit_credit = explode(',', $request->delete_debit_credit_id);
            for ($i = 0; $i < count(array_filter($delete_debit_credit)); $i++) {
                DebitCredit::find($delete_debit_credit[$i])->delete();
            }
        }

        return $debit_credit_data_sup;
    }

    public function deleteSales($id)
    {

        StockOut::where('tran_id', $id)->delete();
        DebitCredit::where('tran_id', $id)->delete();
        return  TransactionMaster::findOrFail($id)->delete();
    }

    public function product_wise_commission_sales($id)
    {
        return DB::select(
            "SELECT t1.*,t2.*,t3.* FROM (
                select `stock_out`.`stock_out_id`, `stock_out`.`tran_id`, `stock_out`.`stock_item_id`, `stock_out`.`qty`, `stock_out`.`rate`, `stock_out`.`total`, `stock_out`.`remark`, `stock_item`.`product_name`, `godowns`.`godown_id`, `godowns`.`godown_name`, `unitsof_measure`.`symbol`,unitsof_measure.unit_of_measure_id,
                 (SELECT SUM(st_in.qty) FROM stock_in as st_in WHERE st_in.stock_item_id=stock_out.stock_item_id AND st_in.godown_id=stock_out.godown_id GROUP BY st_in.stock_item_id )as stock_in_sum,
                 (SELECT SUM(st_out.qty) FROM stock_out as st_out WHERE st_out.stock_item_id=stock_out.stock_item_id AND st_out.godown_id=stock_out.godown_id GROUP BY st_out.stock_item_id )as stock_out_sum,(row_number() over (order by `stock_out`.`stock_out_id`)) row_no1 from `stock_out` left join `stock_item` on `stock_out`.`stock_item_id` = `stock_item`.`stock_item_id` left join `godowns` on `stock_out`.`godown_id` = `godowns`.`godown_id` left join `unitsof_measure` on `stock_item`.`unit_of_measure_id` = `unitsof_measure`.`unit_of_measure_id` where `stock_out`.`tran_id` = '$id'
                ) as t1
                LEFT JOIN (SELECT debit_credit.debit_credit_id AS credit_id,debit_credit.debit AS credit_debit ,debit_credit.credit AS credit_credit,ledger_head.ledger_name AS credit_ledger_name,debit_credit.ledger_head_id AS credit_ledger_id, (row_number() over (order by debit_credit.debit_credit_id)) row_no3 
                FROM debit_credit 
                LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                WHERE debit_credit.tran_id=$id AND commission IS NULL AND credit!=0)
                t3 ON t1.row_no1 = t3.row_no3
                LEFT JOIN (SELECT debit_credit.debit_credit_id AS commission_id,debit_credit.debit AS commission_debit ,debit_credit.credit AS commission_credit,ledger_head.ledger_name AS commission_ledger_name,debit_credit.ledger_head_id AS commission_ledger_id,debit_credit.commission AS commission_commission,debit_credit.commission_type as commission_commission_type, (row_number() over (order by debit_credit.debit_credit_id)) row_no2 
                FROM debit_credit 
                LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id WHERE debit_credit.tran_id=$id AND commission_type!=0)
                t2 ON t2.row_no2 = t1.row_no1"
        );
    }

    public function product_wise_debit_data($id)
    {
        return DB::select(
            "SELECT debit_credit.debit_credit_id AS debit_id,debit_credit.debit AS debit_debit,ledger_head.ledger_name AS debit_ledger_name,debit_credit.ledger_head_id AS debit_ledger_id
                FROM debit_credit 
                LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                WHERE debit_credit.tran_id=$id AND commission IS NULL AND debit_credit.debit!=0"
        );
    }
}
