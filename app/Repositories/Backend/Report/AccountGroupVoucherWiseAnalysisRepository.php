<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class AccountGroupVoucherWiseAnalysisRepository implements AccountGroupVoucherWiseAnalysisInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function  getAccountGroupVoucherWiseAnalysisOfIndex($request = null)
    {

        $params=[];
        $params_1=[];
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $group_chart = explode('-', $request->group_id, 2);

            // Prepare voucher_id_out
            if (!empty($request->out_qty)) {
                $voucher_id_out = $request->out_qty; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':voucher_id_out_'.$i; }, array_keys($voucher_id_out)));
                $voucher_id_out_sql = " voucher_setup.voucher_type_id IN ($placeholders_out)";
                foreach ($voucher_id_out as $i => $id) {
                    $params['voucher_id_out_'.$i] = $id;
                }
            } else {
                $voucher_id_out_sql = "voucher_setup.voucher_type_id IN (0)";
            }

        }

         if (array_filter($request->all())) {
            $group_chart = explode('-', $request->group_id, 2);
            $from_date = $request->from_date;
            $to_date = $request->to_date;

        }
        if($request->cash_out==32){
          $cash_query="WITH recursive tree
                                AS
                                (
                                        SELECT group_chart.group_chart_id,
                                                group_chart.group_chart_name,
                                                group_chart.nature_group,
                                                group_chart.under
                                        FROM   group_chart
                                        WHERE  find_in_set(group_chart.group_chart_id,8)
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
                                            ledger_head.DrCr,
                                            SUM(t1.total_debit)        AS total_debit,
                                            SUM(t1.total_credit)            AS group_credit
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
                                                    ON        voucher_setup.voucher_id=transaction_master.voucher_id
                                                    WHERE     voucher_setup.voucher_type_id=19 AND transaction_master.transaction_date BETWEEN :from_date AND       :to_date
                                                     GROUP BY debit_credit.ledger_head_id
                                                ) AS t1
                                ON        ledger_head.ledger_head_id=t1.ledger_head_id
                            ";
                             $params_1['from_date'] = $from_date;
                             $params_1['to_date'] = $to_date;
             $cash_sales= DB::select($cash_query,$params_1);
        }else{
             $cash_sales=[];
        }
          

        $query =
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
                                            ledger_head.DrCr,
                                            t1.total_debit,
                                            t1.total_credit,
                                            t1.total_debit             AS group_debit,
                                            t1.total_credit            AS group_credit
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
                                                    WHERE      $voucher_id_out_sql AND transaction_master.transaction_date BETWEEN :from_date AND       :to_date
                                                    GROUP BY  debit_credit.ledger_head_id) AS t1
                                ON        ledger_head.ledger_head_id=t1.ledger_head_id

                                ORDER BY  group_chart_name DESC ,ledger_head.ledger_name DESC
        ";

        $params['from_date'] = $from_date;
        $params['to_date'] = $to_date;


        $data = DB::select($query,$params);
       
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $ledger_data = $this->tree->buildTree($group_chart_object_to_array, $group_chart[1], 0, 'group_chart_id', 'under', 'ledger_head_id');

        $group_wise_ledger=$this->calculateGroupTotals($ledger_data);
        $sum_of_children=$this->calculateSumOfChildren( $group_wise_ledger);
        return ['group_wise_ledger'=>$group_wise_ledger,'sum_of_children'=>$sum_of_children,'cash_sales'=>$cash_sales];
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


        function calculateSumOfChildren(array $nodes) {
        $result = [];
        function processNode($node, &$result) {
            // Initialize result for the current group_chart_id if not already
            if (!isset($result[$node['group_chart_id']])) {
                $result[$node['group_chart_id']] = [
                    'group_chart_id' => $node['group_chart_id'],
                    'group_debit' => 0,
                    'group_credit' => 0


                ];
            }

            // Accumulate values for the current node
            $result[$node['group_chart_id']]['group_debit'] += $node['group_debit'] ?? 0;
            $result[$node['group_chart_id']]['group_credit'] += $node['group_credit'] ?? 0;

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
