<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class StockGroupSummaryRepository implements StockGroupSummaryInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getStockGroupSummaryOfIndex($request = null)
    {

        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $godown = $request->godown_id == 0 ? '' : "godowns.godown_id=$request->godown_id AND";
            $stock_group_id = explode('-', $request->stock_group_id, 2);
        }
        if ($stock_group_id[0] == 0) {
            $inner_join_item_in = '';
            $stock_group_in = '';
            $group_id = '';
        } else {

            $data_tree_group = DB::select("with recursive tree as(
                                            SELECT stock_group.stock_group_id FROM stock_group  WHERE FIND_IN_SET(stock_group.stock_group_id,$stock_group_id[0])
                                            UNION ALL
                                            SELECT E.stock_group_id FROM tree H JOIN stock_group E ON H.stock_group_id=E.under
                                        )SELECT * FROM tree");
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
            $inner_join_item_in = 'INNER JOIN stock_item ON stock.stock_item_id=stock_item.stock_item_id';
            $stock_group_in = "stock_item.stock_group_id IN($string_tree_group) AND";
            $group_id = "WHERE stock_group.stock_group_id IN($string_tree_group)";
        }
        if ($request->godown_id == 0) {
            $data = DB::select(
                " SELECT stock_group.stock_group_id,
                            stock_group.stock_group_name,
                            stock_group.under,
                            stock_item.stock_item_id,
                            stock_item.product_name,
                            t.stock_qty_in,
                            t.stock_qty_out,
                            t.stock_total_in,
                            t.stock_total_out,
                            t.stock_qty_in                AS stock_in_sum_qty,
                            t.stock_qty_out               AS stock_out_sum_qty,
                            t.stock_total_in              AS stock_total_sum_in,
                            t.stock_total_out             AS stock_total_sum_out,
                            current_stock.current_rate,
                            current_stock.current_qty,
                            (current_stock.current_qty*current_stock.current_rate) AS sum_current_value,
                            (op_in.stock_qty_opening*op_in_rate.current_rate) AS sum_op_value,
                            op_in.stock_qty_opening              AS op_qty,
                            op_in.stock_qty_opening              AS total_op_qty,
                            op_in_rate.current_rate             AS op_rate
                    FROM      stock_group
                    LEFT JOIN stock_item
                    ON        stock_group.stock_group_id=stock_item.stock_group_id
                    LEFT JOIN
                                (
                                        SELECT      Sum(stock.inwards_qty)    AS stock_qty_in,
                                                    Sum(stock.inwards_value)  AS stock_total_in,
                                                    Sum(stock.outwards_qty)   AS stock_qty_out,
                                                    Sum(stock.outwards_value) AS stock_total_out,
                                                    stock.stock_item_id       AS product_id
                                        FROM        stock
                                        $inner_join_item_in
                                        WHERE      $stock_group_in tran_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock.stock_item_id) AS t
                    ON        stock_item.stock_item_id=t.product_id
                    LEFT JOIN
                                (
                                        SELECT      sum(stock.inwards_qty)-sum(stock.outwards_qty)   AS stock_qty_opening,
                                                    stock.stock_item_id      AS product_id_opening
                                        FROM        stock
                                        $inner_join_item_in
                                        WHERE      $stock_group_in $godown tran_date<'$from_date'
                                        GROUP BY   stock.stock_item_id) AS op_in
                                        ON        stock_item.stock_item_id=op_in.product_id_opening
                    LEFT JOIN
                                (
                                    SELECT t1.stock_item_id,
                                            t1.current_rate
                                    FROM   (
                                                    SELECT  stock.stock_item_id,
                                                            stock.current_rate,
                                                            row_number() over(partition BY stock_item_id ORDER BY id DESC) rn
                                                    FROM      stock   $inner_join_item_in WHERE  $stock_group_in   stock.tran_date<'$from_date') AS t1
                                    WHERE  t1.rn = 1  ) AS op_in_rate
                    ON        stock_item.stock_item_id=op_in_rate.stock_item_id
                    LEFT JOIN
                                (

                                    SELECT t1.stock_item_id,
                                            t1.current_qty,
                                            t1.current_rate
                                    FROM   (
                                                    SELECT  stock.stock_item_id,
                                                            stock.current_qty,
                                                            stock.current_rate,
                                                            row_number() over(partition BY stock_item_id ORDER BY id DESC) rn
                                                    FROM     stock  ) AS t1
                                    WHERE  t1.rn = 1  ) AS current_stock
                    ON        stock_item.stock_item_id=current_stock.stock_item_id $group_id
                ORDER BY  stock_group.stock_group_id DESC"
            );
        } else {

            $data = DB::select(

                "SELECT        stock_group.stock_group_id,
                                                stock_group.stock_group_name,
                                                stock_group.under,
                                                stock_item.stock_item_id,
                                                stock_item.product_name,
                                                t.stock_qty_in,
                                                t.stock_qty_out,
                                                t.stock_total_in,
                                                t.stock_total_out,
                                                t.stock_qty_in                AS stock_in_sum_qty,
                                                t.stock_qty_out               AS stock_out_sum_qty,
                                                t.stock_total_in              AS stock_total_sum_in,
                                                t.stock_total_out             AS stock_total_sum_out,
                                                GodownWiseRateCal(stock_item.stock_item_id,$request->godown_id) AS current_rate,
                                                (((Coalesce(op_in.stock_qty_in_opening,0)-Coalesce(op_in.stock_qty_out_opening,0))+Coalesce(t.stock_qty_in,0))-Coalesce(t.stock_qty_out,0))  AS current_qty,
                                                ((((Coalesce(op_in.stock_qty_in_opening,0)-Coalesce(op_in.stock_qty_out_opening,0))+Coalesce(t.stock_qty_in,0))-Coalesce(t.stock_qty_out,0))*GodownWiseRateCal(stock_item.stock_item_id,$request->godown_id)) AS sum_current_value,
                                                ((op_in.stock_qty_in_opening-op_in.stock_qty_out_opening)*OppeningGodownWiseRateCal(stock_item.stock_item_id,$request->godown_id, '$from_date')) AS sum_op_value,
                                                (op_in.stock_qty_in_opening-op_in.stock_qty_out_opening) AS op_qty,
                                                (op_in.stock_qty_in_opening-op_in.stock_qty_out_opening) AS total_op_qty,
                                                OppeningGodownWiseRateCal(stock_item.stock_item_id,$request->godown_id, '$from_date') AS op_rate

                                    FROM      stock_group
                                    LEFT JOIN stock_item
                                    ON        stock_group.stock_group_id=stock_item.stock_group_id
                                    LEFT JOIN
                                                (
                                                        SELECT      Sum(stock.inwards_qty)    AS stock_qty_in,
                                                                    Sum(stock.inwards_value)  AS stock_total_in,
                                                                    Sum(stock.outwards_qty)   AS stock_qty_out,
                                                                    Sum(stock.outwards_value) AS stock_total_out,
                                                                    stock.stock_item_id       AS product_id
                                                        FROM        stock
                                                        INNER JOIN godowns
                                                        ON         stock.godown_id=godowns.godown_id $inner_join_item_in
                                                        WHERE      $godown $stock_group_in tran_date BETWEEN '$from_date' AND        '$to_date'
                                                        GROUP BY   stock.stock_item_id) AS t
                                    ON        stock_item.stock_item_id=t.product_id
                                    LEFT JOIN
                                                (
                                                        SELECT      sum(stock.inwards_qty)   AS stock_qty_in_opening,
                                                                    sum(stock.outwards_qty)  AS stock_qty_out_opening,
                                                                    stock.stock_item_id      AS product_id_opening
                                                        FROM        stock
                                                        INNER JOIN godowns
                                                        ON         stock.godown_id=godowns.godown_id $inner_join_item_in
                                                        WHERE      $stock_group_in $godown tran_date<'$from_date'
                                                        GROUP BY   stock.stock_item_id) AS op_in
                                    ON        stock_item.stock_item_id=op_in.product_id_opening   $group_id
                                ORDER BY  stock_group.stock_group_id DESC
                                "
            );
        }
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, $stock_group_id[1] ?? 0, 0, 'stock_group_id', 'under', 'stock_item_id');

        return $this->calculateGroupTotals($tree_data);
    }

    // stock  group calculation
    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_in'] = array_sum(array_column($obj['children'], 'stock_qty_in')) + $obj['stock_qty_in'] ?? 0;
                $obj['stock_qty_out'] = array_sum(array_column($obj['children'], 'stock_qty_out')) + $obj['stock_qty_out'] ?? 0;
                $obj['total_op_qty'] = array_sum(array_column($obj['children'], 'total_op_qty')) + $obj['total_op_qty'] ?? 0;
                $obj['sum_op_value'] = array_sum(array_column($obj['children'], 'sum_op_value')) + $obj['sum_op_value'] ?? 0;
                $obj['stock_total_sum_in'] = array_sum(array_column($obj['children'], 'stock_total_sum_in')) + $obj['stock_total_sum_in'] ?? 0;
                $obj['stock_total_sum_out'] = array_sum(array_column($obj['children'], 'stock_total_sum_out')) + $obj['stock_total_sum_out'] ?? 0;
                $obj['sum_current_value'] = array_sum(array_column($obj['children'], 'sum_current_value')) + $obj['sum_current_value'] ?? 0;
            }
        }

        return $arr;
    }
}
