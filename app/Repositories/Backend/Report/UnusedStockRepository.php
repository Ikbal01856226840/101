<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class UnusedStockRepository implements UnusedStockInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getUnusedStockOfIndex($request = null)
    {
        $params=[];

        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            if (!empty($request->godown_id[0])) {

                $godown_in_id=$request->godown_id; // Array of IDs
                $placeholders_in = implode(',', array_map(function($i) { return ':godown_in_id_'.$i; }, array_keys($godown_in_id)));

                $godown_in_sql = "stock_in.godown_id IN ($placeholders_in) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params['godown_in_id_'.$i] = $id;
                }
            } else {
                $godown_in_sql = "";
            }

            if (!empty($request->godown_id[0])) {
                $godown_out_id=$request->godown_id; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':godown_out_id_'.$i; }, array_keys($godown_out_id)));

                $godown_out_sql = "stock_out.godown_id IN ($placeholders_out) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params['godown_out_id_'.$i] = $id;
                }
            } else {
                $godown_out_sql = "";
            }
            if (!empty($request->godown_id[0])) {

                $godown_op_in_id=$request->godown_id; // Array of IDs
                $placeholders_in = implode(',', array_map(function($i) { return ':godown_op_in_id_'.$i; }, array_keys($godown_op_in_id)));

                $godown_op_in_sql = "stock_in.godown_id IN ($placeholders_in) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params['godown_op_in_id_'.$i] = $id;
                }
            } else {
                $godown_op_in_sql = "";
            }
            if (!empty($request->godown_id[0])) {
                $godown_op_out_id=$request->godown_id; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':godown_op_out_id_'.$i; }, array_keys($godown_op_out_id)));

                $godown_op_out_sql = "stock_out.godown_id IN ($placeholders_out) AND";

                foreach ( $godown_in_id as $i => $id) {
                    $params['godown_op_out_id_'.$i] = $id;
                }
            } else {
                $godown_op_out_sql = "";
            }


            $stock_group_id = explode('-', $request->stock_group_id, 2);
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

        }

             $query=
                "SELECT      stock_group.stock_group_id,
                             stock_group.stock_group_name,
                             stock_group.under,
                             stock_item.stock_item_id,
                             stock_item.product_name,
                             unitsof_measure.symbol,
                             t.stock_qty_in,
                             t1.stock_qty_out,
                             op_in.stock_qty_in_opening,
                             op_out.stock_qty_out_opening,
                             t.stock_qty_in               AS stock_in_sum_qty,
                             t1.stock_qty_out             AS stock_out_sum_qty,
                             op_in.stock_qty_in_opening   AS stock_in_sum_qty_op,
                             op_out.stock_qty_out_opening AS stock_out_sum_qty_op
                 FROM      stock_group
                 LEFT JOIN stock_item
                 ON        stock_group.stock_group_id=stock_item.stock_group_id
                 LEFT JOIN
                             (
                                     SELECT     Sum(stock_in.qty)      AS stock_qty_in,
                                                 stock_in.stock_item_id AS product_in
                                     FROM       transaction_master
                                     INNER JOIN `stock_in`
                                     ON         transaction_master.tran_id=stock_in.tran_id
                                      $inner_join_item_in
                                     WHERE      $godown_in_sql $stock_group_in transaction_master.transaction_date BETWEEN :from_date_in AND        :to_date_in
                                     GROUP BY   stock_in.stock_item_id) AS t
                 ON        stock_item.stock_item_id=t.product_in
                 LEFT JOIN
                             (
                                     SELECT     sum(stock_out.qty)      AS stock_qty_out,
                                                 stock_out.stock_item_id AS product_out
                                     FROM       transaction_master
                                     INNER JOIN `stock_out`
                                     ON         transaction_master.tran_id=stock_out.tran_id
                                     $inner_join_item_out
                                     WHERE      $godown_out_sql $stock_group_in transaction_master.transaction_date BETWEEN :from_date_out AND        :to_date_out
                                     GROUP BY   stock_out.stock_item_id) AS t1
                 ON        stock_item.stock_item_id=t1.product_out
                 LEFT JOIN
                             (
                                     SELECT     sum(stock_in.qty)      AS stock_qty_in_opening,
                                                stock_in.stock_item_id AS product_in_opening
                                     FROM       transaction_master
                                     INNER JOIN `stock_in`
                                     ON         transaction_master.tran_id=stock_in.tran_id
                                     $inner_join_item_in
                                     WHERE    $godown_op_in_sql  $stock_group_in  transaction_master.transaction_date<:op_from_date_in
                                     GROUP BY   stock_in.stock_item_id) AS op_in
                 ON        stock_item.stock_item_id=op_in.product_in_opening
                 LEFT JOIN
                             (
                                     SELECT     sum(stock_out.qty)      AS stock_qty_out_opening,
                                                stock_out.stock_item_id AS product_out_opening
                                     FROM       transaction_master
                                     INNER JOIN `stock_out`
                                     ON         transaction_master.tran_id=stock_out.tran_id
                                     $inner_join_item_out
                                     WHERE       $godown_op_out_sql $stock_group_in transaction_master.transaction_date<:op_from_date_out
                                     GROUP BY   stock_out.stock_item_id) AS op_out
                 ON        stock_item.stock_item_id=op_out.product_out_opening
                 LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                 $group_id
                 ORDER BY  stock_group.stock_group_name DESC,stock_item.product_name DESC
                        ";

        $params['from_date_in'] = $from_date;
        $params['to_date_in'] = $to_date;
        $params['from_date_out'] = $from_date;
        $params['to_date_out'] = $to_date;
        $params['op_from_date_in'] = $from_date;
        $params['op_from_date_out'] = $from_date;


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
                $obj['stock_qty_in_opening'] = array_sum(array_column($obj['children'], 'stock_qty_in_opening')) + $obj['stock_qty_in_opening'] ?? 0;
                $obj['stock_qty_out_opening'] = array_sum(array_column($obj['children'], 'stock_qty_out_opening')) + $obj['stock_qty_out_opening'] ?? 0;
            }
        }

        return $arr;
    }
}
