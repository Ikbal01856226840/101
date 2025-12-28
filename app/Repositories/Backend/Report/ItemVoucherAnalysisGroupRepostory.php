<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class ItemVoucherAnalysisGroupRepostory implements ItemVoucherAnalysisGroupInterface
{
    public function getItemVoucherAnalysisGroupOfIndex($request = null)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $stock_item_id = $request->stock_item_id;
        $group_chart = $request->group_id;
        $data_tree_group = DB::select("with recursive tree as(
                                        SELECT group_chart.group_chart_id FROM group_chart  WHERE FIND_IN_SET(group_chart.group_chart_id,:group_chart)
                                        UNION ALL
                                        SELECT E.group_chart_id FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
                                    )SELECT * FROM tree", ['group_chart' => $request->group_id]);

        $group_id = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'group_chart_id'));

        // stock in
        if ($request->purchase) {
            $purchase = $this->stock_in_analysis_query($request->purchase, $stock_item_id, $from_date, $to_date, $group_id);
        }
        if ($request->grn) {
            $grn = $this->stock_in_analysis_query($request->grn, $stock_item_id, $from_date, $to_date, $group_id);
        }
        if ($request->purchase_return) {
            $purchase_return = $this->stock_in_analysis_query($request->purchase_return, $stock_item_id, $from_date, $to_date, $group_id);
        }
        if ($request->journal_in) {
            $journal_in = $this->stock_in_analysis_query($request->journal_in, $stock_item_id, $from_date, $to_date, $group_id);
        }
        if ($request->stock_journal_in) {
            $stock_journal_in = $this->stock_in_analysis_query($request->stock_journal_in, $stock_item_id, $from_date, $to_date, $group_id);
        }
        // stock out
        if ($request->sales_return) {
            $sales_return = $this->stock_out_analysis_query($request->sales_return, $stock_item_id, $from_date, $to_date,  $group_id);
        }
        if ($request->gtn) {
            $gtn = $this->stock_out_analysis_query($request->gtn, $stock_item_id, $from_date, $to_date, $group_id);
        }
        if ($request->sales) {
            $sales = $this->stock_out_analysis_query($request->sales, $stock_item_id, $from_date, $to_date, $group_id);
        }
        if ($request->journal_out) {
            $journal_out = $this->stock_out_analysis_query($request->journal_out, $stock_item_id, $from_date, $to_date, $group_id);
        }
        if ($request->stock_journal_out) {
            $stock_journal_out = $this->stock_out_analysis_query($request->stock_journal_out, $stock_item_id, $from_date, $to_date, $group_id);
        }

        $unit_of_measure = DB::table('stock_item')->select('symbol')->join('unitsof_measure', 'unitsof_measure.unit_of_measure_id', '=', 'stock_item.unit_of_measure_id')->where('stock_item.stock_item_id', $request->stock_item_id)->first();

        return ['unit_of_measure' => $unit_of_measure ?? 0, 'purchase' => $purchase ?? '', 'grn' => $grn ?? '', 'purchase_return' => $purchase_return ?? '', 'journal_in' => $journal_in ?? '', 'stock_journal_in' => $stock_journal_in ?? '', 'sales_return' => $sales_return ?? '', 'gtn' => $gtn ?? '', 'sales' => $sales ?? '', 'journal_out' => $journal_out ?? '', 'stock_journal_out' => $stock_journal_out ?? ''];
    }

    public function stock_in_analysis_query($voucher_id, $stock_item_id, $from_date, $to_date, $group_id)
    {

        return DB::select("SELECT    
                                        transaction_master.tran_id,
                                        voucher_setup.voucher_type_id,
                                        transaction_master.invoice_no,
                                        ledger_head.ledger_name,
                                        Sum(stock_in.qty) AS stock_in_qty,
                                        Sum(stock_in.total) AS stock_in_total
                                FROM       transaction_master
                                INNER JOIN   stock_in
                                      
                                ON         transaction_master.tran_id=stock_in.tran_id
                                INNER JOIN  debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                INNER JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      ledger_head.group_id IN($group_id)
                                AND        voucher_setup.voucher_type_id =$voucher_id
                                AND        stock_in.stock_item_id=:stock_item_id
                                AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                GROUP BY   ledger_head.ledger_head_id
                               
              ", ['stock_item_id' => $stock_item_id, 'from_date' => $from_date, 'to_date' => $to_date]);
    }

    public function stock_out_analysis_query($voucher_id, $stock_item_id, $from_date, $to_date, $group_id)
    {

        return DB::select("SELECT
                                        transaction_master.tran_id,
                                        transaction_master.invoice_no,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        Sum(stock_out.qty) AS stock_out_qty,
                                        Sum(stock_out.total) AS stock_out_total

                                FROM       transaction_master
                                INNER JOIN  stock_out
                                ON         transaction_master.tran_id=stock_out.tran_id
                                INNER JOIN debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                INNER JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      ledger_head.group_id IN($group_id)
                                AND        voucher_setup.voucher_type_id=$voucher_id
                                AND        stock_out.stock_item_id=:stock_item_id
                                AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                GROUP BY  ledger_head.ledger_head_id
        ", ['stock_item_id' => $stock_item_id, 'from_date' => $from_date, 'to_date' => $to_date]);
    }
}
