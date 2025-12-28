<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class ProfitLossRepository implements ProfitLossInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getProfitLossOfIndex($request = null)
    {

        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = company()->financial_year_start;
            $to_date = date('Y-m-d');
        }

        $oppening_stock = DB::select("  SELECT Sum(opening_value.stock_total_value_sum_opening) AS
                                                total_stock_total_out_opening
                                        FROM   (SELECT ( (SELECT s.current_rate
                                                        FROM   stock AS s
                                                        WHERE  s.stock_item_id = stock.stock_item_id
                                                                AND s.tran_date < '$from_date'
                                                        ORDER  BY id DESC
                                                        LIMIT  1) * ( Coalesce(Sum(stock.inwards_qty), 0) -Coalesce(
                                                                        Sum(stock.outwards_qty), 0) ) ) AS
                                                        stock_total_value_sum_opening
                                                FROM   stock
                                                WHERE  stock.tran_date < '$from_date'
                                                GROUP  BY stock.stock_item_id) AS opening_value
                                                                        ");

        $current_stock = DB::select('SELECT t1.stock_item_id,
                                                SUM(t1.current_qty * t1.current_rate) OVER () AS total_val
                                        FROM (
                                        SELECT stock.stock_item_id,
                                                stock.current_qty,
                                                stock.current_rate,
                                                ROW_NUMBER() OVER (PARTITION BY stock.stock_item_id ORDER BY stock.id DESC) rn
                                        FROM stock
                                        ) AS t1
                                        WHERE t1.rn = 1

                                ');

        $ledger_data_income = $this->tree->buildTree(json_decode(json_encode($this->group_data($from_date, $to_date, '33,34'), true), true), 4, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_income = $this->calculateGroupTotals($ledger_data_income);
        $ledger_data_sales = $this->tree->buildTree(json_decode(json_encode($this->group_data($from_date, $to_date, 35), true), true), 4, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_sales = $this->calculateGroupTotals($ledger_data_sales);
        $purchase_data = $this->tree->buildTree(json_decode(json_encode($this->group_data($from_date, $to_date, 32), true), true), 3, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_purchase = $this->calculateGroupTotals($purchase_data);
        $ledger_data_expenses = $this->tree->buildTree(json_decode(json_encode($this->group_data($from_date, $to_date, '30,31'), true), true), 3, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_expenses = $this->calculateGroupTotals($ledger_data_expenses);

        return ['ledger_sales' => $ledger_sales, 'ledger_purchase' => $ledger_purchase, 'ledger_income' => $ledger_income, 'ledger_expenses' => $ledger_expenses, 'oppening_stock' => $oppening_stock, 'current_stock' => $current_stock];
    }

    public function group_data($from_date, $to_date, $group_id)
    {

        return DB::select(
            "WITH recursive tree
                            AS
                            (
                                    SELECT group_chart.group_chart_id,
                                            group_chart.group_chart_name,
                                            group_chart.nature_group,
                                            group_chart.under
                                    FROM   group_chart
                                    WHERE  group_chart.group_chart_id IN($group_id)
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
                                                LEFT JOIN transaction_master
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
                                                LEFT JOIN transaction_master
                                                ON        debit_credit.tran_id=transaction_master.tran_id
                                                WHERE     transaction_master.transaction_date <'$from_date'
                                                GROUP BY  debit_credit.ledger_head_id) AS op
                            ON        ledger_head.ledger_head_id=op.ledger_head_id
                            ORDER BY  group_chart_name DESC
                ");
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
