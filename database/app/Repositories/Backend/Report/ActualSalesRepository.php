<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class ActualSalesRepository implements ActualSalesInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getActualSalesOfIndex($request = null)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $godown = $request->godown_id == 0 ? '' : "godowns.godown_id=$request->godown_id AND";
        }

        $data = DB::select(
                   "SELECT    stock_group.stock_group_id,
                               stock_group.stock_group_name,
                               stock_group.under,
                               stock_item.stock_item_id,
                               stock_item.product_name,
                               t.stock_qty_sales,
                               t.stock_qty_sales AS stock_qty_sales_total,
                               t.stock_total_sales,
                               t.stock_total_sales AS stock_total_sales_value,
                               t1.stock_qty_sales_return,
                               t1.stock_qty_sales_return AS stock_qty_sales_return_total,
                               t1.stock_total_sales_return,
                               t1.stock_total_sales_return AS stock_total_sales_return_value
                    FROM      stock_group
                    LEFT JOIN stock_item
                    ON        stock_group.stock_group_id=stock_item.stock_group_id
                    LEFT JOIN
                                (
                                        SELECT     Sum(stock_out.qty)      AS stock_qty_sales,
                                                   Sum(stock_out.total)    AS stock_total_sales,
                                                   stock_out.stock_item_id AS product_sales
                                        FROM       transaction_master
                                        INNER JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        INNER JOIN godowns
                                        ON         stock_out.godown_id=godowns.godown_id
                                        WHERE      $godown  voucher_setup.voucher_type_id=19 AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock_out.stock_item_id) AS t
                    ON        stock_item.stock_item_id=t.product_sales
                    LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)       AS stock_qty_sales_return,
                                                   sum(stock_out.total)     AS stock_total_sales_return,
                                                    stock_out.stock_item_id AS product_sales_return
                                        FROM       transaction_master
                                        INNER JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        INNER JOIN godowns
                                        ON         stock_out.godown_id=godowns.godown_id
                                        WHERE      $godown  voucher_setup.voucher_type_id=25 AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock_out.stock_item_id) AS t1
                    ON        stock_item.stock_item_id=t1.product_sales_return
                    ORDER BY  stock_group.stock_group_id DESC
                    "
        );
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');

        return $this->calculateGroupTotals($tree_data);

    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_sales'] = array_sum(array_column($obj['children'], 'stock_qty_sales')) + $obj['stock_qty_sales'] ?? 0;
                $obj['stock_qty_sales_return'] = array_sum(array_column($obj['children'], 'stock_qty_sales_return')) + $obj['stock_qty_sales_return'] ?? 0;
                $obj['stock_total_sales'] = array_sum(array_column($obj['children'], 'stock_total_sales')) + $obj['stock_total_sales'] ?? 0;
                $obj['stock_total_sales_return'] = array_sum(array_column($obj['children'], 'stock_total_sales_return')) + $obj['stock_total_sales_return'] ?? 0;
            }
        }

        return $arr;
    }
}
