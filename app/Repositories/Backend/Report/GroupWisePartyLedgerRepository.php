<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class GroupWisePartyLedgerRepository implements GroupWisePartyLedgerInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getGroupWisePartyLedgerOfIndex($request = null)
    {
        $params = [];
        if (array_filter($request->all())) {

            $from_date = $request->from_date;
            $to_date = $request->to_date;

            if($request->group_id==0){
                $sql_group="";
                $sql_group_chart="group_chart";


            }else{
                $group_chart = explode('-', $request->group_id, 2);
                $sql_group="tree
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
                            ON     h.group_chart_id = e.under ),";

                $sql_group_chart="tree";
            }

        }

        $query="WITH recursive $sql_group
                    current_period
                    AS
                    (
                                SELECT      dc.ledger_head_id,
                                            Sum(CASE  WHEN voucher_setup.voucher_type_id <> 25 THEN  dc.debit  ELSE 0 end)      AS total_debit,
                                            Sum(CASE  WHEN voucher_setup.voucher_type_id <> 25 THEN  dc.credit  ELSE 0 end)      AS total_credit,
                                            Sum(CASE  WHEN voucher_setup.voucher_type_id = 25 THEN   dc.debit  ELSE 0 end)      AS sales_return_debit_sum,
                                            Sum(CASE  WHEN voucher_setup.voucher_type_id = 25 THEN   dc.credit  ELSE 0  end)    AS sales_return_credit_sum
                                FROM       debit_credit dc
                                INNER JOIN transaction_master tm
                                ON         dc.tran_id = tm.tran_id
                                LEFT JOIN  voucher_setup
                                ON         voucher_setup.voucher_id =tm.voucher_id
                                WHERE      tm.transaction_date BETWEEN :from_date AND        :to_date
                                GROUP BY   dc.ledger_head_id ),
                    opening_balance
                    AS
                    (
                                SELECT     dc.ledger_head_id,
                                            sum(dc.debit)  AS total_debit,
                                            sum(dc.credit) AS total_credit
                                FROM       debit_credit dc
                                INNER JOIN transaction_master tm
                                ON         dc.tran_id = tm.tran_id
                                WHERE      tm.transaction_date < :op_from_date
                                GROUP BY   dc.ledger_head_id )
                    SELECT      gc.group_chart_id,
                                gc.group_chart_name,
                                gc.nature_group,
                                gc.under,
                                lh.ledger_name,
                                lh.ledger_head_id,
                                lh.alias,
                                lh.DrCr,
                                lh.opening_balance,
                                lh.mailing_add,
                                cp.sales_return_debit_sum,
                                cp.sales_return_credit_sum,
                                cp.sales_return_debit_sum  AS sales_return_debit,
                                cp.sales_return_credit_sum AS sales_return_credit,
                                cp.total_debit ,
                                cp.total_credit,
                                cp.total_debit  AS group_debit,
                                cp.total_credit AS group_credit,
                                ob.total_debit  AS op_total_debit,
                                ob.total_credit AS op_total_credit,
                                IF(gc.nature_group IN (1,3), coalesce(ob.total_debit, 0) +(CASE  WHEN nature_group IN (1, 3) AND  lh.DrCr = 'Cr' THEN -Ifnull(lh.opening_balance, 0) ELSE Ifnull(lh.opening_balance, 0) END), coalesce(ob.total_debit, 0)) AS op_group_debit,
                                IF(gc.nature_group IN (2,4), coalesce(ob.total_credit, 0) +(CASE WHEN nature_group IN (2, 4) AND  lh.DrCr = 'Dr' THEN -Ifnull(lh.opening_balance, 0) ELSE Ifnull(lh.opening_balance, 0) END), coalesce(ob.total_credit, 0)) AS op_group_credit
                    FROM      $sql_group_chart gc
                    LEFT JOIN ledger_head lh
                    ON        gc.group_chart_id = lh.group_id
                    LEFT JOIN current_period cp
                    ON        lh.ledger_head_id = cp.ledger_head_id
                    LEFT JOIN opening_balance ob
                    ON        lh.ledger_head_id = ob.ledger_head_id
                    ORDER BY  gc.group_chart_name DESC,  lh.ledger_name DESC


                        ";
         $params['from_date'] = $from_date;
         $params['to_date'] = $to_date;
         $params['op_from_date'] = $from_date;

        $data = DB::select($query,$params);

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $ledger_data = $this->tree->buildTree($group_chart_object_to_array, $group_chart[1]??0, 0, 'group_chart_id', 'under', 'ledger_head_id');

        $group_wise_ledger=$this->calculateGroupTotals($ledger_data);
        $sum_of_children=$this->calculateSumOfChildren( $group_wise_ledger);
        return ['group_wise_ledger'=>$group_wise_ledger,'sum_of_children'=>$sum_of_children];
    }

    public function getGroupWisePartyLedgerCreditLimit($request = null)
    {

        if (array_filter($request->all())) {
            $group_chart = explode('-', $request->group_id, 2);
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $group_id = $group_chart[0];
        }

        $data = DB::select(
            "WITH recursive tree
                                AS
                                (
                                        SELECT group_chart.group_chart_id,
                                                group_chart.group_chart_name,
                                                group_chart.nature_group,
                                                group_chart.under
                                        FROM   group_chart
                                        WHERE  find_in_set(group_chart.group_chart_id,$group_chart[0])
                                        UNION
                                    SELECT e.group_chart_id,
                                                e.group_chart_name,
                                                e.nature_group,
                                                e.under
                                        FROM   tree h
                                        JOIN   group_chart e
                                        ON     h.group_chart_id=e.under )
                                SELECT    group_chart.group_chart_id,
                                            group_chart.nature_group,
                                            group_chart.under,
                                            group_chart.group_chart_name,
                                            ledger_head.ledger_name,
                                            ledger_head.ledger_head_id,
                                            ledger_head.credit_limit,
                                            ledger_head.opening_balance,
                                            ledger_head.DrCr,
                                            t1.total_debit,
                                            t1.total_credit,
                                            t1.total_debit             AS group_debit,
                                            t1.total_credit            AS group_credit,
                                            journal.total_debit        AS journal_debit,
                                            journal.total_credit       AS journal_credit,
                                            journal.total_debit        AS journal_group_debit,
                                            journal.total_credit       AS journal_group_credit,
                                            sales_return.total_credit  AS sales_return_credit,
                                            sales_return.total_credit   AS sales_return_group_credit,
                                            op.total_debit  AS op_total_debit,
                                            op.total_credit AS op_total_credit,
                                            IF(group_chart.nature_group=1
                                OR        group_chart.nature_group =3 ,(Coalesce(op.total_debit,0)+(CASE  WHEN nature_group IN (1, 3) AND ledger_head.DrCr = 'Cr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),Coalesce(op.total_debit,0)) AS op_group_debit,
                                            IF(group_chart.nature_group=2
                                OR        group_chart.nature_group=4 ,(Coalesce(op.total_credit,0)+(CASE WHEN nature_group IN (2, 4) AND  ledger_head.DrCr = 'Dr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),Coalesce(op.total_credit,0)) AS op_group_credit
                                FROM      tree                                                                          AS group_chart
                                LEFT JOIN ledger_head
                                ON        group_chart.group_chart_id=ledger_head.group_id
                                LEFT JOIN
                                            (
                                                    SELECT     debit_credit.ledger_head_id,
                                                               sum(debit_credit.debit)  AS total_debit,
                                                               sum(debit_credit.credit) AS total_credit
                                                    FROM      debit_credit
                                                    INNER JOIN transaction_master
                                                    ON        debit_credit.tran_id=transaction_master.tran_id
                                                    INNER JOIN voucher_setup
                                                    ON         voucher_setup.voucher_id=transaction_master.voucher_id
                                                    WHERE     voucher_setup.voucher_type_id NOT IN(25,6) AND transaction_master.transaction_date BETWEEN '$from_date' AND       '$to_date'
                                                    GROUP BY  debit_credit.ledger_head_id) AS t1
                                ON        ledger_head.ledger_head_id=t1.ledger_head_id
                                LEFT JOIN
                                            (
                                                    SELECT      debit_credit.ledger_head_id,
                                                                sum(debit_credit.debit)  AS total_debit,
                                                                sum(debit_credit.credit) AS total_credit
                                                    FROM      debit_credit
                                                    INNER JOIN transaction_master
                                                    ON        debit_credit.tran_id=transaction_master.tran_id
                                                    INNER JOIN voucher_setup
                                                    ON         voucher_setup.voucher_id=transaction_master.voucher_id
                                                    WHERE     voucher_setup.voucher_type_id=6 AND transaction_master.transaction_date BETWEEN '$from_date' AND       '$to_date'
                                                    GROUP BY  debit_credit.ledger_head_id) AS journal
                                ON        ledger_head.ledger_head_id=journal.ledger_head_id
                                LEFT JOIN
                                            (
                                                    SELECT    debit_credit.ledger_head_id,
                                                              sum(debit_credit.credit) AS total_credit
                                                    FROM      debit_credit
                                                    INNER JOIN transaction_master
                                                    ON        debit_credit.tran_id=transaction_master.tran_id
                                                    INNER JOIN voucher_setup
                                                    ON         voucher_setup.voucher_id=transaction_master.voucher_id
                                                    WHERE     voucher_setup.voucher_type_id=25 AND transaction_master.transaction_date BETWEEN '$from_date' AND       '$to_date'
                                                    GROUP BY  debit_credit.ledger_head_id) AS sales_return
                                ON        ledger_head.ledger_head_id= sales_return.ledger_head_id
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
                                ORDER BY  group_chart_name DESC ,ledger_head.ledger_name DESC
        ");

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $ledger_data = $this->tree->buildTree($group_chart_object_to_array, $group_chart[1], 0, 'group_chart_id', 'under', 'ledger_head_id');

        return $this->calculateGroupVoucherTotals($ledger_data);
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
                $obj['sales_return_debit'] = array_sum(array_column($obj['children'], 'sales_return_debit')) + $obj['sales_return_debit'] ?? 0;
                $obj['sales_return_credit'] = array_sum(array_column($obj['children'], 'sales_return_credit')) + $obj['sales_return_credit'] ?? 0;
            }
        }

        return $arr;
    }

    public function calculateGroupVoucherTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupVoucherTotals($obj['children']);
                $obj['group_debit'] = array_sum(array_column($obj['children'], 'group_debit')) + $obj['group_debit'] ?? 0;
                $obj['group_credit'] = array_sum(array_column($obj['children'], 'group_credit')) + $obj['group_credit'] ?? 0;
                $obj['journal_group_debit'] = array_sum(array_column($obj['children'], 'journal_group_debit')) + $obj['journal_group_debit'] ?? 0;
                $obj['journal_group_credit'] = array_sum(array_column($obj['children'], 'journal_group_credit')) + $obj['journal_group_credit'] ?? 0;
                $obj['sales_return_group_credit'] = array_sum(array_column($obj['children'], 'sales_return_group_credit')) + $obj['sales_return_group_credit'] ?? 0;
                $obj['op_group_debit'] = array_sum(array_column($obj['children'], 'op_group_debit')) + $obj['op_group_debit'] ?? 0;
                $obj['op_group_credit'] = array_sum(array_column($obj['children'], 'op_group_credit')) + $obj['op_group_credit'] ?? 0;
            }
        }

        return $arr;
    }

    function calculateSumOfChildren(array $nodes) {
        $result = [];
        function processNode($node, &$result) {
            // Initialize result for the current group_chart_id if not already
            if (!isset($result[$node['group_chart_id']])) {
                $result[$node['group_chart_id']] = [
                    'group_chart_id' => $node['group_chart_id'],
                    'group_debit' => 0,
                    'sales_return_debit' => 0,
                    'group_credit' => 0,
                    'sales_return_credit' => 0,
                    'op_group_debit' => 0,
                    'op_group_credit' => 0,
                ];
            }

            // Accumulate values for the current node
            $result[$node['group_chart_id']]['group_debit'] += $node['group_debit'] ?? 0;
            $result[$node['group_chart_id']]['sales_return_debit'] += $node['sales_return_debit'] ?? 0;
            $result[$node['group_chart_id']]['group_credit'] += $node['group_credit'] ?? 0;
            $result[$node['group_chart_id']]['sales_return_credit'] += $node['sales_return_credit'] ?? 0;
            $result[$node['group_chart_id']]['op_group_debit'] += $node['op_group_debit'] ?? 0;
            $result[$node['group_chart_id']]['op_group_credit'] += $node['op_group_credit'] ?? 0;

            // Process children recursively, if any
            if (isset($node['children']) && is_array($node['children'])) {
                foreach ($node['children'] as $child) {
                    processNode($child, $result);
                }
            }
        }

        // Process all nodes in the input array
        foreach ($nodes as $node) {
            processNode($node, $result);
        }

        // Return the values of the result as an array
        return array_values($result);
}
}
