<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class StockItemAnalysisRepository implements StockItemAnalysisInterface
{
    public function stockItemAnalysisOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $godown_in = $request->godown_id == 0 ? '' : "stock_in.godown_id=$request->godown_id AND";
        $godown_out = $request->godown_id == 0 ? '' : "stock_out.godown_id=$request->godown_id AND";
        $stock_item_id = $request->stock_item_id;

        // stock in
        if ($request->purchase) {
            $purchase = $this->stock_in_analysis_query($request->purchase, $godown_in, $stock_item_id, $from_date, $to_date);
        }if ($request->grn) {
            $grn = $this->stock_in_analysis_query($request->grn, $godown_in, $stock_item_id, $from_date, $to_date);
        }if ($request->purchase_return) {
            $purchase_return = $this->stock_in_analysis_query($request->purchase_return, $godown_in, $stock_item_id, $from_date, $to_date);
        }if ($request->journal_in) {
            $journal_in = $this->stock_in_analysis_query($request->journal_in, $godown_in, $stock_item_id, $from_date, $to_date);
        }if ($request->stock_journal_in) {
            $stock_journal_in = $this->stock_in_analysis_query($request->stock_journal_in, $godown_in, $stock_item_id, $from_date, $to_date);
        }
        // stock out
        if ($request->sales_return) {
            $sales_return = $this->stock_out_analysis_query($request->sales_return, $godown_out, $stock_item_id, $from_date, $to_date);
        }if ($request->gtn) {
            $gtn = $this->stock_out_analysis_query($request->gtn, $godown_out, $stock_item_id, $from_date, $to_date);
        }if ($request->sales) {
            $sales = $this->stock_out_analysis_query($request->sales, $godown_out, $stock_item_id, $from_date, $to_date);
        }if ($request->journal_out) {
            $journal_out = $this->stock_out_analysis_query($request->journal_out, $godown_out, $stock_item_id, $from_date, $to_date);
        }if ($request->stock_journal_out) {
            $stock_journal_out = $this->stock_out_analysis_query($request->stock_journal_out, $godown_out, $stock_item_id, $from_date, $to_date);
        }

        return ['purchase' => $purchase ?? '', 'grn' => $grn ?? '', 'purchase_return' => $purchase_return ?? '', 'journal_in' => $journal_in ?? '', 'stock_journal_in' => $stock_journal_in ?? '', 'sales_return' => $sales_return ?? '', 'gtn' => $gtn ?? '', 'sales' => $sales ?? '', 'journal_out' => $journal_out ?? '', 'stock_journal_out' => $stock_journal_out ?? ''];
    }

    public function stock_in_analysis_query($voucher_id, $godown, $stock_item_id, $from_date, $to_date)
    {

        return DB::select("     SELECT    transaction_master.invoice_no,
                                        transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        voucher_setup.voucher_name,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        Sum(stock_in.qty)   AS stock_in_qty,
                                        Sum(stock_in.total) AS stock_in_total
                                FROM       transaction_master
                                INNER JOIN stock_in
                                ON         transaction_master.tran_id=stock_in.tran_id
                                INNER JOIN
                                        (
                                                SELECT   tran_id,
                                                        ledger_head_id
                                                FROM     debit_credit
                                                GROUP BY tran_id ) AS debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                INNER JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      $godown stock_in.stock_item_id=$stock_item_id
                                AND        voucher_setup.voucher_type_id IN($voucher_id)
                                AND        transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                GROUP BY   ledger_head.ledger_head_id
              ");
    }

    public function stock_out_analysis_query($voucher_id, $godown, $stock_item_id, $from_date, $to_date)
    {

        return DB::select("    SELECT      transaction_master.invoice_no,
                                        transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        voucher_setup.voucher_name,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        SUM(stock_out.qty) AS  stock_out_qty,
                                        SUM(stock_out.total) AS  stock_out_total

                                FROM       transaction_master
                                INNER JOIN stock_out
                                ON         transaction_master.tran_id=stock_out.tran_id
                                INNER JOIN (
                                                SELECT   tran_id,
                                                        ledger_head_id
                                                FROM     debit_credit
                                                GROUP BY tran_id ) AS debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                INNER JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      $godown stock_out.stock_item_id=$stock_item_id
                                AND        voucher_setup.voucher_type_id IN($voucher_id)
                                AND        transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                GROUP BY   ledger_head.ledger_head_id
        ");
    }
}
