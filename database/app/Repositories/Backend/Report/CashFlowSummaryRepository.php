<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class CashFlowSummaryRepository implements CashFlowSummaryInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getCashFlowSummaryOfIndex($request = null)
    {

        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = company()->financial_year_start;

            $to_date = date('Y-m-d');
        }

        $data = DB::select(
                           "SELECT group_chart.group_chart_id,
                                    group_chart.under,
                                    group_chart.group_chart_name,
                                    t1.ledger_name,
                                    t1.ledger_head_id,
                                    t1.op_total_debit,
                                    t1.op_total_credit,
                                    t1.op_total_debit  AS group_debit,
                                    t1.op_total_credit AS group_credit
                            FROM   group_chart
                                    LEFT JOIN(SELECT ledger_head.group_id,
                                                    debit_credit.ledger_head_id,
                                                    ledger_head.ledger_name,
                                                    Sum(debit_credit.debit)  AS op_total_debit,
                                                    Sum(debit_credit.credit) AS op_total_credit
                                            FROM   debit_credit
                                                    INNER JOIN ledger_head
                                                            ON debit_credit.ledger_head_id =
                                                                ledger_head.ledger_head_id
                                                    INNER JOIN transaction_master
                                                            ON debit_credit.tran_id =
                                                                transaction_master.tran_id
                                            WHERE  transaction_master.transaction_date BETWEEN
                                                    '$from_date' AND '$to_date'
                                            GROUP  BY debit_credit.ledger_head_id) AS t1
                                        ON group_chart.group_chart_id = t1.group_id
                            ORDER  BY group_chart_name DESC
                        "
        );

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'group_chart_id', 'under');

        return $this->calculateGroupTotals($data);
    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['group_debit'] = array_sum(array_column($obj['children'], 'group_debit')) + $obj['group_debit'] ?? 0;
                $obj['group_credit'] = array_sum(array_column($obj['children'], 'group_credit')) + $obj['group_credit'] ?? 0;
            }
        }

        return $arr;
    }
}
