<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class AccountGroupSummaryRepository implements AccountGroupSummaryInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getAccountGroupSummaryOfIndex($request = null)
    {


        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        }

        $op_sql="";
        $select_op_sql="";
        $group_chart="";

        if($request->group_id==0){
            $sql_group="";
            $sql_group_chart="group_chart";
            $order_by="ORDER BY  group_chart.group_chart_name DESC, ledger_name DESC";
        }else{
            $group_chart = explode('-', $request->group_id, 2);
            $order_by="ORDER BY
                        CASE 
                            WHEN group_chart.group_chart_id = $group_chart[0] THEN 0 
                            ELSE 1
                        END DESC,
                    group_chart.group_chart_name DESC,
                    ledger_name DESC";

            $sql_group="WITH recursive tree
                AS
                (
                        SELECT group_chart_id,
                                group_chart_name,
                                nature_group,
                                under
                        FROM   group_chart
                        WHERE  find_in_set(group_chart_id,$group_chart[0])
                        UNION ALL
                        SELECT e.group_chart_id,
                                e.group_chart_name,
                                e.nature_group,
                                e.under
                        FROM   tree h
                        JOIN   group_chart e
                        ON     h.group_chart_id = e.under )";

            $sql_group_chart="tree";
        }

        if($request->without_op_bla==1){
            $op_sql="";
            $select_op_sql="
                            0 AS opening_balance,
                            0 AS op_total_debit,
                            0 AS op_total_credit,
                            0 AS op_group_debit,
                            0 AS op_group_credit";
        }else{
            $op_sql=" LEFT JOIN
                                    (
                                            SELECT    debit_credit.ledger_head_id,
                                                        sum(debit_credit.debit)  AS total_debit,
                                                        sum(debit_credit.credit) AS total_credit
                                            FROM      debit_credit
                                            INNER JOIN transaction_master
                                            ON        debit_credit.tran_id=transaction_master.tran_id
                                            WHERE     transaction_master.transaction_date <:from_date_op
                                            GROUP BY  debit_credit.ledger_head_id) AS op
                        ON        ledger_head.ledger_head_id=op.ledger_head_id";
            $select_op_sql="
                            ledger_head.opening_balance,
                            op.total_debit  AS op_total_debit,
                                                op.total_credit AS op_total_credit,
                                                IF(group_chart.nature_group=1
                                    OR        group_chart.nature_group =3 ,(IFNULL(op.total_debit,0)+(CASE  WHEN nature_group IN (1, 3) AND  ledger_head.DrCr = 'Cr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),IFNULL(op.total_debit,0)) AS op_group_debit,
                                                IF(group_chart.nature_group=2
                                    OR        group_chart.nature_group=4 ,(IFNULL(op.total_credit,0)++(CASE WHEN nature_group IN (2, 4) AND  ledger_head.DrCr = 'Dr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),IFNULL(op.total_credit,0)) AS op_group_credit";
                                $params['from_date_op'] = $from_date;
        }

        $query=" $sql_group
                                SELECT    group_chart.group_chart_id,
                                            group_chart.nature_group,
                                            group_chart.under,
                                            group_chart.group_chart_name,
                                            ledger_head.ledger_name,
                                            ledger_head.ledger_head_id,
                                            ledger_head.alias,
                                            ledger_head.DrCr,
                                            t1.total_debit,
                                            t1.total_credit,
                                            t1.total_debit  AS group_debit,
                                            t1.total_credit AS group_credit,
                                            $select_op_sql
                                FROM        $sql_group_chart                                                                          AS group_chart
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
                                                    WHERE     transaction_master.transaction_date BETWEEN :from_date AND       :to_date
                                                    GROUP BY  debit_credit.ledger_head_id) AS t1
                                ON        ledger_head.ledger_head_id=t1.ledger_head_id
                                $op_sql
                                $order_by
        ";

        $params['from_date'] = $from_date;
        $params['to_date'] = $to_date;
        $data = DB::select($query,$params);

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $ledger_data = $this->tree->buildTree($group_chart_object_to_array, $group_chart[0]??0, 0, 'group_chart_id', 'under', 'ledger_head_id');

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
