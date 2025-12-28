<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class StockItemRegisterRepository implements StockItemRegisterInterface
{
    public function getStockItemRegisterOfIndex($request = null)
    {

        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $godown = $request->godown_id == 0 ? '' : "stock.godown_id=$request->godown_id AND";

            $stock_item_id = $request->stock_item_id;
            if (($request->voucher_id == 0) || ($request->voucher_id == null)) {
                $voucher_id = 0;
                $voucher_sql_type = 0;
                $voucher_sql = '';
            } else {
                if (strpos($request->voucher_id, 'v') !== false) {
                    $voucher_sql_type = 2;
                    $voucher_id = str_replace('v', '', $request->voucher_id);
                    $voucher_sql = " AND  voucher_setup.voucher_type_id='$voucher_id'";
                } else {
                    $voucher_sql_type = 1;
                    $voucher_id = $request->voucher_id;
                    $voucher_sql = " AND  transaction_master.voucher_id='$request->voucher_id'";
                }
            }
        }
        if ($request->godown_id == 0) {
        $current_stock = DB::select("SELECT transaction_master.invoice_no,
                                               transaction_master.tran_id,
                                               transaction_master.transaction_date,
                                               voucher_setup.voucher_name,
                                               voucher_setup.voucher_type_id,
                                               ledger_head.ledger_name,
                                               inwards_qty,
                                               inwards_value,
                                               outwards_qty,
                                               outwards_value,
                                               current_qty,
                                               current_rate
                                    FROM       transaction_master
                                    INNER JOIN stock
                                    ON         transaction_master.tran_id=stock.tran_id
                                    INNER JOIN debit_credit
                                    ON         transaction_master.tran_id=debit_credit.tran_id
                                    INNER JOIN ledger_head
                                    ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                    LEFT JOIN  voucher_setup
                                    ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                    WHERE      $godown stock.stock_item_id=$stock_item_id
                                    AND        transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date' $voucher_sql
                                    GROUP BY   transaction_master.tran_id
                       ");
        } else {
            $current_stock = DB::select("CALL GrodownWiseCalProcedure($stock_item_id,$request->godown_id,'$from_date','$to_date',$voucher_sql_type,$voucher_id)");
        }

        $oppening_stock = DB::select(" SELECT Sum(opening_value.stock_total_value_sum_opening) AS total_stock_total_out_opening,
                                                Sum(op_qty)                                      AS total_stock_total_opening_qty
                                        FROM   (
                                                        SELECT     ( Ifnull(Sum(stock.inwards_qty), 0) - Ifnull( Sum(stock.outwards_qty), 0) ) *
                                                                    (
                                                                            SELECT   s.current_rate
                                                                            FROM     stock AS s
                                                                            WHERE    s.stock_item_id = $stock_item_id
                                                                            AND      s.tran_date < '$from_date'
                                                                            ORDER BY id DESC
                                                                            LIMIT    1 ) AS stock_total_value_sum_opening, 
                                                                    ( Ifnull(Sum(stock.inwards_qty), 0) - Ifnull( Sum(stock.outwards_qty), 0) ) AS op_qty
                                                        FROM       transaction_master
                                                        INNER JOIN stock
                                                        ON         transaction_master.tran_id = stock.tran_id
                                                        WHERE      $godown stock.stock_item_id=$stock_item_id
                                                        AND        transaction_master.transaction_date <'$from_date'
                                                        GROUP BY   stock.stock_item_id) AS opening_value
                        ");

        return $stock = ['current_stock' => $current_stock, 'oppening_stock' => $oppening_stock];
    }
}
