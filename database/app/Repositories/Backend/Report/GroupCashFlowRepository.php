<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class GroupCashFlowRepository implements GroupCashFlowInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getGroupCashFlowOfIndex($request = null)
    {

        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $group_id = $request->group_id;
        }

        $data = DB::select("WITH recursive tree
                            AS
                            (
                                    SELECT group_chart.group_chart_id,
                                            group_chart.group_chart_name,
                                            group_chart.under,
                                            1 AS lvl
                                    FROM   group_chart
                                    WHERE  find_in_set(group_chart.group_chart_id,$group_id)
                                    UNION
                                    SELECT e.group_chart_id,
                                            e.group_chart_name,
                                            e.under,
                                            h.lvl+1 AS lvl
                                    FROM   tree h
                                    JOIN   group_chart e
                                    ON     h.group_chart_id=e.under )
                            SELECT    group_chart.group_chart_id,
                                        group_chart.under,
                                        group_chart.group_chart_name,
                                        t1.ledger_name,
                                        t1.ledger_head_id,
                                        t1.op_total_debit,
                                        t1.op_total_credit,
                                        t1.op_total_debit  AS group_debit,
                                        t1.op_total_credit AS group_credit
                            FROM      tree               AS group_chart
                            LEFT JOIN
                                        (
                                                SELECT    ledger_head.group_id,
                                                            debit_credit.ledger_head_id ,
                                                            ledger_head.ledger_name,
                                                            sum(debit_credit.debit)  AS op_total_debit,
                                                            sum(debit_credit.credit) AS op_total_credit
                                                FROM      debit_credit
                                                LEFT JOIN ledger_head
                                                ON        debit_credit.ledger_head_id=ledger_head.ledger_head_id
                                                LEFT JOIN transaction_master
                                                ON        debit_credit.tran_id=transaction_master.tran_id
                                                WHERE     transaction_master.transaction_date BETWEEN '$from_date' AND       '$to_date'
                                                GROUP BY  debit_credit.ledger_head_id) AS t1
                            ON        group_chart.group_chart_id=t1.group_id
                            ORDER BY  group_chart_name DESC");
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, $group_id, 0, 'group_chart_id', 'under', 'ledger_head_id');

    }
}
