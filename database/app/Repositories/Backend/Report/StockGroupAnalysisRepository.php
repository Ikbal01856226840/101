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
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $godown = $request->godown_id == 0 ? '' : "godowns.godown_id=$request->godown_id AND";
            $stock_group_id = explode('-', $request->stock_group_id, 2);
            $voucher_id_in = implode(',', $request->in_qty);
            $voucher_id_out = implode(',', $request->out_qty);
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
                                                    WHERE  find_in_set(stock_group.stock_group_id,$stock_group_id[0])
                                                    UNION ALL
                                                    SELECT e.stock_group_id
                                                    FROM   tree h
                                                    JOIN   stock_group e
                                                    ON     h.stock_group_id=e.under )
                                            SELECT *
                                            FROM   tree");
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
            $inner_join_item_in = 'INNER JOIN stock_item ON stock_in.stock_item_id=stock_item.stock_item_id';
            $stock_group_in = "stock_item.stock_group_id IN($string_tree_group) AND";
            $inner_join_item_out = 'INNER JOIN stock_item ON stock_out.stock_item_id=stock_item.stock_item_id';
            $group_id = "WHERE stock_group.stock_group_id IN($string_tree_group)";
        }

        $data = DB::select(
               "SELECT    stock_group.stock_group_id,
                               stock_group.stock_group_name,
                               stock_group.under,
                               stock_item.stock_item_id,
                               stock_item.product_name,
                               t.stock_qty_in,
                               t.stock_qty_in AS stock_qty_total_in,
                               t.stock_total_in,
                               t.stock_total_in AS stock_total_value_in,
                               t1.stock_qty_out,
                               t1.stock_qty_out AS stock_qty_total_out,
                               t1.stock_total_out,
                               t1.stock_total_out AS stock_total_value_out
                    FROM      stock_group
                    LEFT JOIN stock_item
                    ON        stock_group.stock_group_id=stock_item.stock_group_id
                    LEFT JOIN
                                (
                                        SELECT     Sum(stock_in.qty)      AS stock_qty_in,
                                                   Sum(stock_in.total)    AS stock_total_in,
                                                    stock_in.stock_item_id AS product_in
                                        FROM       transaction_master
                                        INNER JOIN `stock_in`
                                        ON         transaction_master.tran_id=stock_in.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        INNER JOIN godowns
                                        ON         stock_in.godown_id=godowns.godown_id $inner_join_item_in
                                        WHERE      $godown $stock_group_in voucher_setup.voucher_type_id IN($voucher_id_in) AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock_in.stock_item_id) AS t
                    ON        stock_item.stock_item_id=t.product_in
                    LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)      AS stock_qty_out,
                                                   sum(stock_out.total)      AS stock_total_out,
                                                    stock_out.stock_item_id AS product_out
                                        FROM       transaction_master
                                        INNER JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        INNER JOIN godowns
                                        ON         stock_out.godown_id=godowns.godown_id $inner_join_item_out
                                        WHERE      $godown $stock_group_in voucher_setup.voucher_type_id IN($voucher_id_out) AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock_out.stock_item_id) AS t1
                    ON        stock_item.stock_item_id=t1.product_out $group_id
                    "
        );

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
