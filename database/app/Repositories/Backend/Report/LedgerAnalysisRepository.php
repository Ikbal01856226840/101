<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class LedgerAnalysisRepository implements LedgerAnalysisInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getLedgerAnalyisOfIndex($request = null)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $ledger_id = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=$request->ledger_id AND";
            $voucher_id_in = implode(',', $request->in_qty);
            $voucher_id_out = implode(',', $request->out_qty);
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
                                        INNER JOIN debit_credit
                                        ON         transaction_master.tran_id=debit_credit.tran_id

                                        WHERE      $ledger_id voucher_setup.voucher_type_id IN($voucher_id_in) AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
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
                                        INNER JOIN debit_credit
                                        ON         transaction_master.tran_id=debit_credit.tran_id

                                        WHERE      $ledger_id voucher_setup.voucher_type_id IN($voucher_id_out) AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                        GROUP BY   stock_out.stock_item_id) AS t1
                    ON        stock_item.stock_item_id=t1.product_out
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
                $obj['stock_qty_in'] = array_sum(array_column($obj['children'], 'stock_qty_in')) + $obj['stock_qty_in'] ?? 0;
                $obj['stock_qty_out'] = array_sum(array_column($obj['children'], 'stock_qty_out')) + $obj['stock_qty_out'] ?? 0;
                $obj['stock_total_in'] = array_sum(array_column($obj['children'], 'stock_total_in')) + $obj['stock_total_in'] ?? 0;
                $obj['stock_total_out'] = array_sum(array_column($obj['children'], 'stock_total_out')) + $obj['stock_total_out'] ?? 0;
            }
        }

        return $arr;
    }
}
