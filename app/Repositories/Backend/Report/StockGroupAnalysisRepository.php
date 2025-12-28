<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class StockGroupAnalysisRepository implements StockGroupAnalysisInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function stockGroupAnalysisOfIndex($request = null)
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

            $stock_group_id = explode('-', $request->stock_group_id, 2);
               
               // Prepare voucher_id_in
               if (!empty($request->in_qty)) {
                    $voucher_id_in = $request->in_qty; // Array of IDs
                    $placeholders_in = implode(',', array_map(function($i) { return ':voucher_id_in_'.$i; }, array_keys($voucher_id_in)));

                    $voucher_id_in_sql = "voucher_setup.voucher_type_id IN ($placeholders_in)";
                
                    foreach ($voucher_id_in as $i => $id) {
                        $params['voucher_id_in_'.$i] = $id;
                    }
                } else {
                    $voucher_id_in_sql = "voucher_setup.voucher_type_id IN (0)";
                }
                
            // Prepare voucher_id_out
            if (!empty($request->out_qty)) {
                $voucher_id_out = $request->out_qty; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':voucher_id_out_'.$i; }, array_keys($voucher_id_out)));
                $voucher_id_out_sql = "voucher_setup.voucher_type_id IN ($placeholders_out)";
                foreach ($voucher_id_out as $i => $id) {
                    $params['voucher_id_out_'.$i] = $id;
                }
            } else {
                $voucher_id_out_sql = "voucher_setup.voucher_type_id IN (0)";
            }
           
        }
        if ($stock_group_id[0] == 0) {
            $inner_join_item_in = '';
            $inner_join_item_out = '';
            $stock_group_in = '';
            $group_id = '';
        } else {
            $data_tree_group = DB::select("WITH recursive tree
                                            AS
                                            (
                                                    SELECT stock_group.stock_group_id
                                                    FROM   stock_group
                                                    WHERE  find_in_set(stock_group.stock_group_id,:stock_group_id)
                                                    UNION ALL
                                                    SELECT e.stock_group_id
                                                    FROM   tree h
                                                    JOIN   stock_group e
                                                    ON     h.stock_group_id=e.under )
                                            SELECT *
                                            FROM   tree",['stock_group_id'=>$stock_group_id[0]]);
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
            $inner_join_item_in = 'INNER JOIN stock_item ON stock_in.stock_item_id=stock_item.stock_item_id';
            $stock_group_in = "stock_item.stock_group_id IN($string_tree_group) AND";
            $inner_join_item_out = 'INNER JOIN stock_item ON stock_out.stock_item_id=stock_item.stock_item_id';
            $group_id = "WHERE stock_group.stock_group_id IN($string_tree_group)";
        }

            $select_value_sql='';
            if(company()->sales_return==2){

                $select_value_sql=" coalesce(si.stock_qty_in, 0)    + abs(coalesce(so.stock_qty_sales_return, 0))      AS stock_qty_in,
                                    coalesce(si.stock_qty_in, 0)    + abs(coalesce(so.stock_qty_sales_return, 0))      AS stock_qty_total_in,
                                    coalesce(si.stock_total_in, 0)  + abs(coalesce(so.stock_total_sales_return, 0))    AS stock_total_in,
                                    coalesce(si.stock_total_in, 0)  + abs(coalesce(so.stock_total_sales_return, 0))    AS stock_total_value_in,
                                    coalesce(so.stock_qty_out, 0)   + abs(coalesce(si.stock_qty_purchase_return, 0))   AS stock_qty_out,
                                    coalesce(so.stock_qty_out, 0)   + abs(coalesce(si.stock_qty_purchase_return, 0))   AS stock_qty_total_out,
                                    coalesce(so.stock_total_out, 0) + abs(coalesce(si.stock_total_purchase_return, 0)) AS stock_total_out,
                                    coalesce(so.stock_total_out, 0) + abs(coalesce(si.stock_total_purchase_return, 0)) AS stock_total_value_out
                ";
            }else if(company()->sales_return==1){
                $select_value_sql=" COALESCE(si.stock_qty_in, 0)       AS stock_qty_in,
                                    COALESCE(si.stock_qty_in, 0)       AS stock_qty_total_in,
                                    COALESCE(si.stock_total_in, 0)     AS stock_total_in,
                                    COALESCE(si.stock_total_in, 0)     AS stock_total_value_in,
                                    COALESCE(so.stock_qty_out, 0)      AS stock_qty_out,
                                    COALESCE(so.stock_qty_out, 0)      AS stock_qty_total_out,
                                    COALESCE(so.stock_total_out, 0)    AS stock_total_out,
                                    COALESCE(so.stock_total_out, 0)    AS stock_total_value_out

                        ";
                        
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
                                INNER JOIN stock_in
                                ON         transaction_master.tran_id = stock_in.tran_id $inner_join_item_in
                                INNER JOIN voucher_setup
                                ON         transaction_master.voucher_id = voucher_setup.voucher_id
                                INNER JOIN godowns
                                ON         stock_in.godown_id = godowns.godown_id
                                WHERE      $godown_in_sql $stock_group_in $voucher_id_in_sql
                                AND        transaction_master.transaction_date BETWEEN :from_date_in AND        :to_date_in
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
                            INNER JOIN stock_out
                            ON         transaction_master.tran_id = stock_out.tran_id $inner_join_item_out
                            INNER JOIN voucher_setup
                            ON         transaction_master.voucher_id = voucher_setup.voucher_id
                            INNER JOIN godowns
                            ON         stock_out.godown_id = godowns.godown_id
                            WHERE      $godown_out_sql $stock_group_in $voucher_id_out_sql
                            AND        transaction_master.transaction_date BETWEEN :from_date_out AND        :to_date_out
                            GROUP BY   stock_out.stock_item_id )
                SELECT    stock_group.stock_group_id,
                            stock_group.stock_group_name,
                            stock_group.under,
                            stock_item.stock_item_id,
                            stock_item.product_name,
                            unitsof_measure.symbol,
                            $select_value_sql
                FROM      stock_group
                LEFT JOIN stock_item
                ON        stock_group.stock_group_id = stock_item.stock_group_id
                LEFT JOIN stockin si
                ON        stock_item.stock_item_id = si.product_in
                LEFT JOIN stockout so
                ON        stock_item.stock_item_id = so.product_out
                LEFT JOIN unitsof_measure
                ON        stock_item.unit_of_measure_id = unitsof_measure.unit_of_measure_id $group_id
                ORDER BY  stock_group.stock_group_name DESC,
                            stock_item.product_name DESC
                        ";
                    
        $params['from_date_in'] = $from_date;
        $params['to_date_in'] = $to_date;
        $params['from_date_out'] = $from_date;
        $params['to_date_out'] = $to_date;
        $data = DB::select($query,$params);
               
      

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, $stock_group_id[1] ?? 0, 0, 'stock_group_id', 'under', 'stock_item_id');

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
