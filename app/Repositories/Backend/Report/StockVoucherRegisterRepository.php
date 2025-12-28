<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class StockVoucherRegisterRepository implements StockVoucherRegisterInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getStockVoucherRegisterOfIndex($request = null)
    {
        $params=[];
        $godown_in ='';
        $godown_out ='';
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            if (!empty($request->godown_id[0])) {

                $godown_in_id=$request->godown_id; // Array of IDs
                $placeholders_in = implode(',', array_map(function($i) { return ':godown_in_id_'.$i; }, array_keys($godown_in_id)));

                $godown_in_sql = "godowns.godown_id IN ($placeholders_in) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params['godown_in_id_'.$i] = $id;
                }
            } else {
                $godown_in_sql = "";
            }

            if (!empty($request->godown_id[0])) {
                $godown_out_id=$request->godown_id; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':godown_out_id_'.$i; }, array_keys($godown_out_id)));

                $godown_out_sql = "godowns.godown_id IN ($placeholders_out) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params['godown_out_id_'.$i] = $id;
                }
            } else {
                $godown_out_sql = "";
            }

          
            if ($request->voucher_id == 0) {
                $voucher_sql_in = '';
                $voucher_sql_out='';
            } else {
                if (strpos($request->voucher_id, 'v') !== false) {
                    $voucher_type_id = str_replace('v', '', $request->voucher_id);
                    $voucher_sql_in = "voucher_setup.voucher_type_id=:voucher_type_id_in AND ";
                    $params['voucher_type_id_in'] = $voucher_type_id;
                    $voucher_sql_out = "voucher_setup.voucher_type_id=:voucher_type_id_out AND ";
                    $params['voucher_type_id_out'] = $voucher_type_id;
                } else {
                    $voucher_sql_in = "voucher_setup.voucher_id=:voucher_id_in AND";
                    $params['voucher_id_in'] = $request->voucher_id;
                    $voucher_sql_out = "voucher_setup.voucher_id=:voucher_id_out AND";
                    $params['voucher_id_out'] = $request->voucher_id;
                }
            }

        }
            
             $query="WITH stockin
                    AS
                    (
                                SELECT     stock_in.stock_item_id AS product_in,
                                            sum(
                                            CASE
                                                    WHEN stock_in.qty >= 0 THEN stock_in.qty
                                                    ELSE 0
                                            end) AS stock_qty_in,
                                            sum(
                                            CASE
                                                    WHEN stock_in.qty >= 0 THEN stock_in.total
                                                    ELSE 0
                                            end) AS stock_total_in,
                                            sum(
                                            CASE
                                                    WHEN stock_in.qty < 0 THEN stock_in.qty
                                                    ELSE 0
                                            end) AS stock_qty_purchase_return,
                                            sum(
                                            CASE
                                                    WHEN stock_in.qty < 0 THEN stock_in.total
                                                    ELSE 0
                                            end) AS stock_total_purchase_return
                                FROM       transaction_master
                                LEFT JOIN stock_in
                                ON         transaction_master.tran_id = stock_in.tran_id 
                                LEFT JOIN voucher_setup
                                ON         transaction_master.voucher_id = voucher_setup.voucher_id
                                LEFT JOIN godowns
                                ON         stock_in.godown_id = godowns.godown_id
                                WHERE      $godown_in_sql  $voucher_sql_in
                                          transaction_master.transaction_date BETWEEN :from_date_in AND        :to_date_in
                                GROUP BY   stock_in.stock_item_id ),
                stockout
                AS
                (
                            SELECT     stock_out.stock_item_id AS product_out,
                                        sum(
                                        CASE
                                                WHEN stock_out.qty >= 0 THEN stock_out.qty
                                                ELSE 0
                                        end) AS stock_qty_out,
                                        sum(
                                        CASE
                                                WHEN stock_out.qty >= 0 THEN stock_out.total
                                                ELSE 0
                                        end) AS stock_total_out,
                                        sum(
                                        CASE
                                                WHEN stock_out.qty < 0 THEN stock_out.qty
                                                ELSE 0
                                        end) AS stock_qty_sales_return,
                                        sum(
                                        CASE
                                                WHEN stock_out.qty < 0 THEN stock_out.total
                                                ELSE 0
                                        end) AS stock_total_sales_return
                            FROM       transaction_master
                            LEFT JOIN stock_out
                            ON         transaction_master.tran_id = stock_out.tran_id 
                            LEFT JOIN voucher_setup
                            ON         transaction_master.voucher_id = voucher_setup.voucher_id
                            LEFT JOIN godowns
                            ON         stock_out.godown_id = godowns.godown_id
                            WHERE      $godown_out_sql  $voucher_sql_out
                                        transaction_master.transaction_date BETWEEN :from_date_out AND        :to_date_out
                            GROUP BY   stock_out.stock_item_id )
                SELECT    stock_group.stock_group_id,
                            stock_group.stock_group_name,
                            stock_group.under,
                            stock_item.stock_item_id,
                            stock_item.product_name,
                            unitsof_measure.symbol,
                            coalesce(si.stock_qty_in, 0)    + abs(coalesce(so.stock_qty_sales_return, 0))      AS stock_qty_in,
                            coalesce(si.stock_qty_in, 0)    + abs(coalesce(so.stock_qty_sales_return, 0))      AS stock_qty_total_in,
                            coalesce(si.stock_total_in, 0)  + abs(coalesce(so.stock_total_sales_return, 0))    AS stock_total_in,
                            coalesce(si.stock_total_in, 0)  + abs(coalesce(so.stock_total_sales_return, 0))    AS stock_total_value_in,
                            coalesce(so.stock_qty_out, 0)   + abs(coalesce(si.stock_qty_purchase_return, 0))   AS stock_qty_out,
                            coalesce(so.stock_qty_out, 0)   + abs(coalesce(si.stock_qty_purchase_return, 0))   AS stock_qty_total_out,
                            coalesce(so.stock_total_out, 0) + abs(coalesce(si.stock_total_purchase_return, 0)) AS stock_total_out,
                            coalesce(so.stock_total_out, 0) + abs(coalesce(si.stock_total_purchase_return, 0)) AS stock_total_value_out
                FROM      stock_group
                LEFT JOIN stock_item
                ON        stock_group.stock_group_id = stock_item.stock_group_id
                LEFT JOIN stockin si
                ON        stock_item.stock_item_id = si.product_in
                LEFT JOIN stockout so
                ON        stock_item.stock_item_id = so.product_out
                LEFT JOIN unitsof_measure
                ON        stock_item.unit_of_measure_id = unitsof_measure.unit_of_measure_id 
                ORDER BY  stock_group.stock_group_name DESC,
                            stock_item.product_name DESC
                        ";
           
        $params['from_date_in'] = $from_date;
        $params['to_date_in'] = $to_date;
        $params['from_date_out'] = $from_date;
        $params['to_date_out'] = $to_date;
    
        $data = DB::select($query,$params);



        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');

        return $this->calculateGroupTotals($tree_data);

    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_in'] = array_sum(array_column($obj['children'], 'stock_qty_in')) + $obj['stock_qty_in'] ?? 0;
                $obj['stock_qty_out'] = array_sum(array_column($obj['children'], 'stock_qty_out')) + $obj['stock_qty_out'] ?? 0;
                $obj['stock_total_in'] = array_sum(array_column($obj['children'], 'stock_total_in')) + $obj['stock_total_in'] ?? 0;
                $obj['stock_total_out'] = array_sum(array_column($obj['children'], 'stock_total_out')) + $obj['stock_total_out'] ?? 0;
            }
        }

        return $arr;
    }
}
