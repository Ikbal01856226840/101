<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class StockItemRegisterRepository implements StockItemRegisterInterface
{
    public function getStockItemRegisterOfIndex($request = null)
    {
        $voucher_sql = '';
        $godown_op ='';
        $godown = '';
        $params=[];
        $params_op=[];
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $mutiple_godown='';
            if (!empty($request->godown_id[0])) {
                $mutiple_godown_string=implode(",",$request->godown_id);
                $mutiple_godown= '"' . $mutiple_godown_string . '"';
                $godown_in_id=$request->godown_id;
                $placeholders_in = implode(',', array_map(function($i) { return ':godown_in_id_'.$i; }, array_keys($godown_in_id)));

                $godown_sql = "stock.godown_id IN ($placeholders_in) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params['godown_in_id_'.$i] = $id;
                }
            } else {
                $godown_sql = "";
            }

            if (!empty($request->godown_id[0])) {
                $godown_out_id=$request->godown_id;
                $placeholders_out = implode(',', array_map(function($i) { return ':godown_out_id_'.$i; }, array_keys($godown_out_id)));

                $godown_op_sql = "stock.godown_id IN ($placeholders_out) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params_op['godown_out_id_'.$i] = $id;
                }
            } else {
                $godown_op_sql = "";
            }

            $stock_item_id = $request->stock_item_id;
            if (($request->voucher_id == 0) || ($request->voucher_id == null)) {
                $voucher_id = 0;
                $voucher_sql_type = 0;
                $voucher_sql = '';
            } else {
                if (strpos($request->voucher_id,'v') !== false) {
                    $voucher_sql_type = 2;
                    $voucher_id = str_replace('v', '', $request->voucher_id);
                    $voucher_sql = " AND  voucher_setup.voucher_type_id=:voucher_id";
                    $params['voucher_id'] = $voucher_id;

                } else {
                    $voucher_sql_type = 1;
                    $voucher_sql = "AND transaction_master.voucher_id = :voucher_id";
                    $params['voucher_id'] = $request->voucher_id;
                }
            }
        }
        $current_rate_sql='';
        if(!empty($request->godown_id[0])){
        //    dd($mutiple_godown);
            $current_rate_sql="stockRegisterRateCal($mutiple_godown,stock.stock_item_id,stock.tran_id) AS current_rate";
            
        }else{
            $current_rate_sql="(SELECT s.current_rate
                                FROM stock AS s
                                WHERE s.tran_id <=stock.tran_id AND stock.stock_item_id=s.stock_item_id
                                ORDER BY s.id DESC LIMIT 1) AS current_rate";

        }
        // dd($params);
        $unit_of_measure = DB::table('stock_item')->select('symbol')->join('unitsof_measure', 'unitsof_measure.unit_of_measure_id', '=', 'stock_item.unit_of_measure_id')->where('stock_item.stock_item_id',$stock_item_id)->first();
        $query="SELECT transaction_master.invoice_no,
                        transaction_master.tran_id,
                        transaction_master.narration,
                        transaction_master.transaction_date,
                        voucher_setup.voucher_name,
                        voucher_setup.voucher_type_id,
                        ledger_head.ledger_name,
                        stock.inwards_qty AS  inwards_qty,
                        stock.inwards_value AS inwards_value,
                        stock.outwards_qty AS outwards_qty,
                        stock.outwards_value AS outwards_value,
                        stock.current_qty,
                        stock.current_rate
                FROM       transaction_master
                INNER JOIN  (SELECT
                                stock.tran_id,
                                SUM(stock.inwards_qty) AS inwards_qty,
                                SUM(stock.inwards_value) AS inwards_value,
                                SUM(stock.outwards_qty) AS outwards_qty,
                                SUM(stock.outwards_value) AS outwards_value ,
                                current_qty,
                                stock.stock_item_id,
                                $current_rate_sql
                        FROM `stock`
                        WHERE $godown_sql stock.stock_item_id=:stock_item_id
                        AND   stock.tran_date BETWEEN :from_date_1 AND        :to_date_1
                        GROUP by stock.stock_item_id,stock.tran_id) AS stock
                ON         transaction_master.tran_id=stock.tran_id
                LEFT JOIN debit_credit
                ON         transaction_master.tran_id=debit_credit.tran_id
                LEFT JOIN ledger_head
                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                LEFT JOIN  voucher_setup
                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                WHERE      transaction_master.transaction_date BETWEEN :from_date AND        :to_date  $voucher_sql
                GROUP BY   transaction_master.tran_id
                
                ";

                $params['stock_item_id'] = $stock_item_id;
                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;
                $params['from_date_1'] = $from_date;
                $params['to_date_1'] = $to_date;

$current_stock = DB::select($query,$params);
        if (!empty($request->godown_id[0])) {

            $query_op ="  SELECT  OppeningGodownWiseRateCal($stock_item_id,$mutiple_godown, '$from_date')* Sum(op_qty) AS total_stock_total_out_opening,
                                          Sum(op_qty)                                      AS total_stock_total_opening_qty
                              FROM   (
                                          SELECT    ( Ifnull(Sum(stock.inwards_qty), 0) - Ifnull( Sum(stock.outwards_qty), 0) ) AS op_qty
                                          FROM       transaction_master
                                          INNER JOIN stock
                                          ON         transaction_master.tran_id = stock.tran_id
                                          WHERE      $godown_op_sql stock.stock_item_id=:stock_item_id_op
                                          AND        transaction_master.transaction_date <:from_date_op
                                          GROUP BY   stock.stock_item_id) AS opening_value
                              ";

                    $params_op['stock_item_id_op'] = $stock_item_id;
                    $params_op['from_date_op'] = $from_date;

                    $oppening_stock = DB::select($query_op,$params_op);


        } else {
            $query_op=" SELECT Sum(opening_value.stock_total_value_sum_opening) AS total_stock_total_out_opening,
                                    Sum(op_qty)                                      AS total_stock_total_opening_qty
                            FROM   (
                                            SELECT     ( Ifnull(Sum(stock.inwards_qty), 0) - Ifnull( Sum(stock.outwards_qty), 0) ) *
                                                        (
                                                                SELECT   s.current_rate
                                                                FROM     stock AS s
                                                                WHERE    s.stock_item_id = :stock_item_id_op_1
                                                                AND      s.tran_date <:from_date_op_1
                                                                ORDER BY id DESC
                                                                LIMIT    1 ) AS stock_total_value_sum_opening,
                                                        ( Ifnull(Sum(stock.inwards_qty), 0) - Ifnull( Sum(stock.outwards_qty), 0) ) AS op_qty
                                            FROM       transaction_master
                                            INNER JOIN stock
                                            ON         transaction_master.tran_id = stock.tran_id
                                            WHERE       $godown_op_sql stock.stock_item_id=:stock_item_id_op_2
                                            AND        transaction_master.transaction_date <:from_date_op_2
                                            GROUP BY   stock.stock_item_id) AS opening_value
                        ";
                        $params_op['stock_item_id_op_1'] = $stock_item_id;
                        $params_op['stock_item_id_op_2'] = $stock_item_id;
                        $params_op['from_date_op_1'] = $from_date;
                        $params_op['from_date_op_2'] = $from_date;

                        $oppening_stock = DB::select($query_op,$params_op);

          

        }



        return $stock = ['current_stock' => $current_stock, 'oppening_stock' => $oppening_stock,'unit_of_measure'=>$unit_of_measure];
    }
}
