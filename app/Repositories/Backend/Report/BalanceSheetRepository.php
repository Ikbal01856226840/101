<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;
use App\Services\RateCalculation\ClosingBalance;
use App\Services\RateCalculation\OpeningBalance;

class BalanceSheetRepository implements BalanceSheetInterface
{
    private $tree;
    private $closingBalance;
    private $openingBalance;

    public function __construct(Tree $tree,ClosingBalance $closingBalance,OpeningBalance $openingBalance)
    {
        $this->tree = $tree;
        $this->closingBalance=$closingBalance;
        $this->openingBalance=$openingBalance;
    }

    public function getBalanceSheetOfIndex($request = null)
    {
        $params = [];

        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = company()->financial_year_start;
            $to_date = date('Y-m-d');
        }
                    $query="SELECT     group_chart.group_chart_id,
                                        group_chart.nature_group,
                                        group_chart.under,
                                        group_chart.group_chart_name,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        ledger_head.opening_balance,
                                        ledger_head.DrCr,
                                        t1.total_debit,
                                        t1.total_credit,
                                        t1.total_debit  AS group_debit,
                                        t1.total_credit AS group_credit,
                                        op.total_debit  AS op_total_debit,
                                        op.total_credit AS op_total_credit,
                                        IF(group_chart.nature_group=1
                            OR          group_chart.nature_group =3 ,(IFNULL(op.total_debit,0)+(CASE  WHEN nature_group IN (1, 3) AND  ledger_head.DrCr = 'Cr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),IFNULL(op.total_debit,0)) AS op_group_debit,
                                        IF(group_chart.nature_group=2
                            OR          group_chart.nature_group=4 ,(IFNULL(op.total_credit,0)+(CASE WHEN nature_group IN (2, 4) AND  ledger_head.DrCr = 'Dr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),IFNULL(op.total_credit,0)) AS op_group_credit
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
                                                WHERE     transaction_master.transaction_date BETWEEN :from_date AND       :to_date
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
                                                WHERE     transaction_master.transaction_date <:op_from_date
                                                GROUP BY  debit_credit.ledger_head_id) AS op
                            ON        ledger_head.ledger_head_id=op.ledger_head_id
                            WHERE     group_chart_name!='Reserved'
                            ORDER BY  group_chart_name DESC
        ";
        $params['from_date'] = $from_date;
        $params['to_date'] = $to_date;
        $params['op_from_date'] = $from_date;
        $data = DB::select($query,$params);
                          

        $group_chart_object_to_array_asset = json_decode(json_encode($data, true), true);
        $ledger_data_asset = $this->tree->buildTree($group_chart_object_to_array_asset, 1, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_asset = $this->calculateGroupTotals($ledger_data_asset);
        $group_chart_object_to_array_liabilities = json_decode(json_encode($data, true), true);
        $ledger_data_liabilities = $this->tree->buildTree($group_chart_object_to_array_liabilities, 2, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_liabilities = $this->calculateGroupTotals($ledger_data_liabilities);

        $oppening_stock = $this->openingBalance->OpeningRate(company()->financial_year_start,$request->to_date);

        $current_stock = $this->closingBalance->ClosingRate(company()->financial_year_start,$request->to_date);
        

        $ledger_data_income = $this->tree->buildTree(json_decode(json_encode($this->group_data($from_date, $to_date, '33,34'), true), true), 4, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_income = $this->calculateGroupTotal($ledger_data_income);
        $ledger_data_sales = $this->tree->buildTree(json_decode(json_encode($this->group_data_salse_and_purchase($from_date, $to_date, 35), true), true), 4, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_sales = $this->calculateGroupTotal($ledger_data_sales);
        $purchase_data = $this->tree->buildTree(json_decode(json_encode($this->group_data_salse_and_purchase($from_date, $to_date, 32), true), true), 3, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_purchase = $this->calculateGroupTotal($purchase_data);
        $ledger_data_expenses = $this->tree->buildTree(json_decode(json_encode($this->group_data($from_date, $to_date, '30,31'), true), true), 3, 0, 'group_chart_id', 'under', 'ledger_head_id');
        $ledger_expenses = $this->calculateGroupTotal($ledger_data_expenses);

        return ['ledger_sales' => $ledger_sales, 'ledger_purchase' => $ledger_purchase, 'ledger_income' => $ledger_income, 'ledger_expenses' => $ledger_expenses, 'oppening_stock' => $oppening_stock, 'current_stock_1' => $current_stock,'assets' => $ledger_asset, 'liabilities' => $ledger_liabilities,'current_stock'=>$this->closingBalance->ClosingRate($request->from_date,$request->to_date)];
    }


    public function getCashBankBookOfIndex($request = null)
    {

        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = company()->financial_year_start;
            $to_date = date('Y-m-d');
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
                                        WHERE  group_chart.group_chart_id IN(8,9)
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
                                            ledger_head.DrCr,
                                            t1.total_debit,
                                            t1.total_credit,
                                            t1.total_debit  AS group_debit,
                                            t1.total_credit AS group_credit,
                                            op.total_debit  AS op_total_debit,
                                            op.total_credit AS op_total_credit,
                                            IF(group_chart.nature_group=1
                                OR        group_chart.nature_group =3 ,(IFNULL(op.total_debit,0)+(CASE WHEN nature_group IN (2, 4) AND  ledger_head.DrCr = 'Dr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),IFNULL(op.total_debit,0)) AS op_group_debit,
                                            IF(group_chart.nature_group=2
                                OR        group_chart.nature_group=4 ,(IFNULL(op.total_credit,0)+(CASE WHEN nature_group IN (2, 4) AND  ledger_head.DrCr = 'Dr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END)),IFNULL(op.total_credit,0)) AS op_group_credit
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
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $ledger_data = $this->tree->buildTree($group_chart_object_to_array, 7, 0, 'group_chart_id', 'under', 'ledger_head_id');
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
                                        0 AS opening_balance,
                                        t1.total_debit,
                                        t1.total_credit,
                                        t1.total_debit  AS group_debit,
                                        t1.total_credit AS group_credit,
                                        0  AS op_total_debit,
                                        0 AS op_total_credit,
                                        0 AS op_group_debit,
                                        0 AS op_group_credit
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
                                                WHERE     transaction_master.transaction_date BETWEEN :from_date AND       :to_date
                                                GROUP BY  debit_credit.ledger_head_id) AS t1
                            ON        ledger_head.ledger_head_id=t1.ledger_head_id
                            ORDER BY  group_chart_name DESC
                ",['from_date'=>company()->financial_year_start,'to_date'=>$to_date]);
    }
    public function group_data_salse_and_purchase($from_date, $to_date, $group_id)
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
                                        0 AS opening_balance,
                                        t1.total_debit,
                                        t1.total_credit,
                                        t1.total_debit  AS group_debit,
                                        t1.total_credit AS group_credit,
                                        0  AS op_total_debit,
                                        0 AS op_total_credit,
                                        0 AS op_group_debit,
                                        0 AS op_group_credit
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
                                                WHERE     transaction_master.transaction_date BETWEEN :from_date AND       :to_date
                                                GROUP BY  debit_credit.ledger_head_id) AS t1
                            ON        ledger_head.ledger_head_id=t1.ledger_head_id
                           
                            ORDER BY  group_chart_name DESC
                ",['from_date'=>company()->financial_year_start,'to_date'=>$to_date]);
    }
    public function calculateGroupTotal($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotal($obj['children']);
                $obj['group_debit'] = array_sum(array_column($obj['children'], 'group_debit')) + $obj['group_debit'] ?? 0;
                $obj['group_credit'] = array_sum(array_column($obj['children'], 'group_credit')) + $obj['group_credit'] ?? 0;
                $obj['op_group_debit'] = array_sum(array_column($obj['children'], 'op_group_debit')) + $obj['op_group_debit'] ?? 0;
                $obj['op_group_credit'] = array_sum(array_column($obj['children'], 'op_group_credit')) + $obj['op_group_credit'] ?? 0;
            }
        }

        return $arr;
    }
}
