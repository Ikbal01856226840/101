<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class GrodownWiseStockAnalysisRepository implements GrodownWiseStockAnalysisInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getGrodownWiseStockAnalysisOfIndex($request = null)
    {

        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $stock_group_id = explode('-', $request->stock_group_id, 2);
        }
        if ($stock_group_id[0] == 0) {
            $inner_join_item_in = '';
            $stock_group_in = '';
            $group_id = '';
        } else {

            // tree value get sql
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
            // value implode
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
            // condition checking
            $inner_join_item_in = 'INNER JOIN stock_item ON s.stock_item_id=stock_item.stock_item_id';
            $stock_group_in = "WHERE stock_item.stock_group_id IN($string_tree_group) ";
            $group_id = "WHERE stock_group.stock_group_id IN($string_tree_group)";
        }

            $params_without_godown=[];



                $query= "WITH cte
                     AS
                        (
                                    SELECT
                                            stock_item.stock_group_id,
                                            stock_item.stock_item_id,
                                            stock_item.product_name,
                                            godowns.godown_id,
                                            godowns.godown_name,
                                            unitsof_measure.symbol,
                                            sum(
                                            CASE
                                                        WHEN tran_date BETWEEN :from_date AND       :to_date THEN inwards_qty
                                                        ELSE 0
                                            end ) AS stock_qty_in,
                                            sum(
                                            CASE
                                                        WHEN tran_date BETWEEN :from_date_2 AND       :to_date_2 THEN outwards_qty
                                                        ELSE 0
                                            end ) AS stock_qty_out,
                                            sum(
                                            CASE
                                                        WHEN tran_date < :from_date_4 THEN (inwards_qty - outwards_qty)
                                                        ELSE 0
                                            end )  AS stock_qty_opening,


                                            (SELECT SUM(stock.inwards_qty-stock.outwards_qty)
                                                FROM stock
                                                WHERE tran_date BETWEEN :from_date_5 AND       :to_date_5 AND stock.stock_item_id=stock_item.stock_item_id
                                                ORDER BY stock.id DESC LIMIT 1) as stock_in_out_qty,

                                            (SELECT SUM(stock.inwards_qty-stock.outwards_qty)
                                                        FROM stock
                                                        WHERE tran_date <:op_from_date_1 AND stock.stock_item_id=stock_item.stock_item_id
                                                        ORDER BY stock.id DESC LIMIT 1) as stock_op_qty,
                                            (SELECT stock.current_rate
                                                            FROM stock
                                                            WHERE tran_date <=:to_date_4 AND stock.stock_item_id=stock_item.stock_item_id
                                                            ORDER BY stock.id DESC LIMIT 1) AS current_rate,
                                            (SELECT stock.current_rate
                                                FROM stock
                                                WHERE tran_date <:op_from_date_3 AND stock.stock_item_id=stock_item.stock_item_id
                                                ORDER BY  stock.id DESC LIMIT 1) AS op_in_rate

                                    FROM      stock_item
                                    INNER JOIN unitsof_measure
                                    ON        stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                    INNER JOIN stock
                                    ON        stock_item.stock_item_id = stock.stock_item_id
                                    INNER JOIN godowns
                                    ON        godowns.godown_id= stock.godown_id
                                    $stock_group_in
                                    GROUP BY  stock_item.stock_item_id,godowns.godown_id
                                    )

                        SELECT  stock_group.stock_group_id,
                                stock_group.stock_group_name,
                                stock_group.under,
                                cte.stock_item_id,
                                cte.product_name,
                                cte.symbol,
                                GodownWiseRateCal(cte.stock_item_id,cte.godown_id) AS godown_wise_current_rate,
                                OppeningGodownWiseRateCal(cte.stock_item_id,cte.godown_id, '$from_date') as godown_wise_op_rate,
                                cte.stock_in_out_qty,
                                cte.stock_op_qty,
                                cte.current_rate,
                                cte.op_in_rate,
                                cte.stock_qty_in,
                                cte.stock_qty_out,
                                cte.stock_qty_in                AS stock_in_sum_qty,
                                cte.stock_qty_out               AS stock_out_sum_qty,
                                (((Coalesce(cte.stock_qty_in,0)+Coalesce(cte.stock_qty_opening,0))-Coalesce(cte.stock_qty_out,0))*Coalesce(cte.current_rate,0)) AS sum_current_value,
                                (Coalesce(cte.stock_qty_opening,0)*cte.op_in_rate) AS sum_op_value,
                                cte.stock_qty_opening              AS op_qty,
                                cte.stock_qty_opening              AS total_op_qty,
                                cte.godown_name                     AS godown_name
                        FROM    stock_group
                        LEFT JOIN cte
                        ON        stock_group.stock_group_id = cte.stock_group_id

                        ORDER BY stock_group_name DESC, product_name DESC,godown_name DESC
                       ;
            ";
            $params_without_godown['from_date'] = $from_date;
            $params_without_godown['to_date'] = $to_date;
            $params_without_godown['from_date_2'] = $from_date;
            $params_without_godown['to_date_2'] = $to_date;
            $params_without_godown['from_date_4'] = $from_date;
            $params_without_godown['from_date_5'] = $from_date;
            $params_without_godown['to_date_5'] = $to_date;
            $params_without_godown['op_from_date_1'] = $from_date;
            $params_without_godown['to_date_4'] = $to_date;
            $params_without_godown['op_from_date_3'] = $from_date;
            $data = DB::select($query,$params_without_godown);


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
                $obj['sum_current_value'] = array_sum(array_column($obj['children'], 'sum_current_value')) + $obj['sum_current_value'] ?? 0;
            }
        }

        return $arr;
    }



}
