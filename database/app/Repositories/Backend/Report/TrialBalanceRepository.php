<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class TrialBalanceRepository implements TrialBalanceInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getTrialBalanceOfIndex($request = null)
    {

        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = company()->financial_year_start;
            $to_date = date('Y-m-d');
        }
        $data = DB::select(
                           "SELECT     group_chart.group_chart_id,
                                        group_chart.nature_group,
                                        group_chart.under,
                                        group_chart.group_chart_name,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        ledger_head.opening_balance,
                                        t1.total_debit,
                                        t1.total_credit,
                                        t1.total_debit  AS group_debit,
                                        t1.total_credit AS group_credit,
                                        op.total_debit  AS op_total_debit,
                                        op.total_credit AS op_total_credit,
                                        IF(group_chart.nature_group=1
                            OR          group_chart.nature_group =3 ,(IFNULL(op.total_debit,0)+IFNULL(ledger_head.opening_balance,0)),IFNULL(op.total_debit,0)) AS op_group_debit,
                                        IF(group_chart.nature_group=2
                            OR          group_chart.nature_group=4 ,(IFNULL(op.total_credit,0)+IFNULL(ledger_head.opening_balance,0)),IFNULL(op.total_credit,0)) AS op_group_credit
                            FROM      group_chart
                            LEFT JOIN ledger_head
                            ON        group_chart.group_chart_id=ledger_head.group_id
                            LEFT JOIN
                                        (
                                                SELECT    debit_credit.ledger_head_id,
                                                            sum(debit_credit.debit)  AS total_debit,
                                                            sum(debit_credit.credit) AS total_credit
                                                FROM      debit_credit
                                                INNER JOIN transaction_master
                                                ON        debit_credit.tran_id=transaction_master.tran_id
                                                WHERE     transaction_master.transaction_date BETWEEN '$from_date' AND       '$to_date'
                                                GROUP BY  debit_credit.ledger_head_id) AS t1
                            ON        ledger_head.ledger_head_id=t1.ledger_head_id
                            LEFT JOIN
                                        (
                                                SELECT    debit_credit.ledger_head_id,
                                                            sum(debit_credit.debit)  AS total_debit,
                                                            sum(debit_credit.credit) AS total_credit
                                                FROM      debit_credit
                                                INNER JOIN transaction_master
                                                ON        debit_credit.tran_id=transaction_master.tran_id
                                                WHERE     transaction_master.transaction_date <'$from_date'
                                                GROUP BY  debit_credit.ledger_head_id) AS op
                            ON        ledger_head.ledger_head_id=op.ledger_head_id
                            WHERE     group_chart_name!='Reserved'
                            ORDER BY  group_chart_name DESC
        ");

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $ledger_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'group_chart_id', 'under', 'ledger_head_id');

        return $this->calculateGroupTotals($ledger_data);
    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['group_debit'] = array_sum(array_column($obj['children'], 'group_debit')) + $obj['group_debit'] ?? 0;
                $obj['group_credit'] = array_sum(array_column($obj['children'], 'group_credit')) + $obj['group_credit'] ?? 0;
                $obj['op_group_debit'] = array_sum(array_column($obj['children'], 'op_group_debit')) + $obj['op_group_debit'] ?? 0;
                $obj['op_group_credit'] = array_sum(array_column($obj['children'], 'op_group_credit')) + $obj['op_group_credit'] ?? 0;
            }
        }

        return $arr;
    }
}
