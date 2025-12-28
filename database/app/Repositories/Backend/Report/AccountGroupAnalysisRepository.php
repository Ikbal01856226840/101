<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class AccountGroupAnalysisRepository implements AccountGroupAnalysisInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getAccountGroupAnalysisOfIndex($request = null)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $group_chart = explode('-', $request->group_id, 2);
            $data_tree_group = DB::select("with recursive tree as(
                                            SELECT group_chart.group_chart_id FROM group_chart  WHERE FIND_IN_SET(group_chart.group_chart_id,$group_chart[0])
                                            UNION ALL
                                            SELECT E.group_chart_id FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
                                        )SELECT * FROM tree");
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'group_chart_id'));
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
                               t1.stock_qty_purchase,
                               t1.stock_qty_purchase AS stock_qty_purchase_total,
                               t1.stock_total_purchase,
                               t1.stock_total_purchase AS stock_total_purchase_value
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
                                        INNER JOIN debit_credit
                                        ON         transaction_master.tran_id=debit_credit.tran_id
                                        INNER JOIN ledger_head
                                                ON ledger_head.ledger_head_id = debit_credit.ledger_head_id
                                        WHERE   ledger_head.group_id IN($string_tree_group)  AND voucher_setup.voucher_type_id=19 AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock_out.stock_item_id) AS t
                    ON        stock_item.stock_item_id=t.product_sales
                    LEFT JOIN
                                (
                                        SELECT     sum(stock_in.qty)       AS stock_qty_purchase,
                                                   sum(stock_in.total)     AS stock_total_purchase,
                                                    stock_in.stock_item_id AS product_purchase
                                        FROM       transaction_master
                                        INNER JOIN `stock_in`
                                        ON         transaction_master.tran_id=stock_in.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        INNER JOIN debit_credit
                                        ON         transaction_master.tran_id=debit_credit.tran_id
                                        INNER JOIN ledger_head
                                        ON         ledger_head.ledger_head_id = debit_credit.ledger_head_id
                                        WHERE     ledger_head.group_id IN($string_tree_group)  AND  voucher_setup.voucher_type_id=10 AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock_in.stock_item_id) AS t1
                    ON        stock_item.stock_item_id=t1.product_purchase
                    ORDER BY  stock_group.stock_group_id DESC
                    "
        );
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, $group_chart[1], 0, 'stock_group_id', 'under', 'stock_item_id');

        return $this->calculateGroupTotals($tree_data);

    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_sales'] = array_sum(array_column($obj['children'], 'stock_qty_sales')) + $obj['stock_qty_sales'] ?? 0;
                $obj['stock_qty_purchase'] = array_sum(array_column($obj['children'], 'stock_qty_purchase')) + $obj['stock_qty_purchase'] ?? 0;
                $obj['stock_total_sales'] = array_sum(array_column($obj['children'], 'stock_total_sales')) + $obj['stock_total_sales'] ?? 0;
                $obj['stock_total_purchase'] = array_sum(array_column($obj['children'], 'stock_total_purchase')) + $obj['stock_total_purchase'] ?? 0;
            }
        }

        return $arr;
    }
}
