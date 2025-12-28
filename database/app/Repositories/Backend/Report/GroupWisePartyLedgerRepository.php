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
                                            ledger_head.alias,
                                            ledger_head.opening_balance,
                                            t1.total_debit,
                                            t1.total_credit,
                                            t1.total_debit  AS group_debit,
                                            t1.total_credit AS group_credit,
                                            op.total_debit  AS op_total_debit,
                                            op.total_credit AS op_total_credit,
                                            IF(group_chart.nature_group=1
                                OR        group_chart.nature_group =3 ,(IFNULL(op.total_debit,0)+IFNULL(ledger_head.opening_balance,0)),IFNULL(op.total_debit,0)) AS op_group_debit,
                                            IF(group_chart.nature_group=2
                                OR        group_chart.nature_group=4 ,(IFNULL(op.total_credit,0)+IFNULL(ledger_head.opening_balance,0)),IFNULL(op.total_credit,0)) AS op_group_credit
                                FROM      tree                                                                          AS group_chart
                                LEFT JOIN ledger_head
                                ON        group_chart.group_chart_id=ledger_head.group_id
                                LEFT JOIN
                                            (
                                                    SELECT    debit_credit.ledger_head_id,
                                                                sum(debit_credit.debit)  AS total_debit,
                                                                sum(debit_credit.credit) AS total_credit
                                                    FROM      debit_credit
                                                    INNER JOIN  transaction_master
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
                                                    INNER JOIN  transaction_master
                                                    ON        debit_credit.tran_id=transaction_master.tran_id
                                                    WHERE     transaction_master.transaction_date <'$from_date'
                                                    GROUP BY  debit_credit.ledger_head_id) AS op
                                ON        ledger_head.ledger_head_id=op.ledger_head_id
                                ORDER BY  group_chart_name DESC
        ");
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $ledger_data = $this->tree->buildTree($group_chart_object_to_array, $group_chart[1], 0, 'group_chart_id', 'under', 'ledger_head_id');

        return $this->calculateGroupTotals($ledger_data);
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
                                OR        group_chart.nature_group =3 ,(Coalesce(op.total_debit,0)+Coalesce(ledger_head.opening_balance,0)),Coalesce(op.total_debit,0)) AS op_group_debit,
                                            IF(group_chart.nature_group=2
                                OR        group_chart.nature_group=4 ,(Coalesce(op.total_credit,0)+Coalesce(ledger_head.opening_balance,0)),Coalesce(op.total_credit,0)) AS op_group_credit
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
                                ORDER BY  group_chart_name DESC
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
}
