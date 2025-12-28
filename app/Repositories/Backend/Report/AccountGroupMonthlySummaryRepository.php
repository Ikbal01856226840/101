<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class AccountGroupMonthlySummaryRepository implements AccountGroupMonthlySummaryInterface
{
    public function getAccountMonthlySummaryOfIndex($request = null)
    {
        $group_chart = explode('-', $request->group_id, 2);
        $data_tree_group = DB::select("WITH recursive tree
                                        AS
                                        (
                                                SELECT group_chart.group_chart_id
                                                FROM   group_chart
                                                WHERE  find_in_set(group_chart.group_chart_id,:group_chart_id)
                                                UNION ALL
                                                SELECT e.group_chart_id
                                                FROM   tree h
                                                JOIN   group_chart e
                                                ON     h.group_chart_id=e.under )
                                        SELECT *
                                        FROM   tree",['group_chart_id'=>$group_chart[0]]);


       $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'group_chart_id'));

      $oppening_ledger_blance=DB::select("SELECT
                                                SUM(
                                                    CASE
                                                        WHEN group_chart.nature_group IN (1, 3) AND ledger_head.DrCr = 'Cr' THEN -IFNULL(ledger_head.opening_balance, 0)
                                                        WHEN group_chart.nature_group IN (2, 4) AND ledger_head.DrCr = 'Dr' THEN -IFNULL(ledger_head.opening_balance, 0)
                                                        ELSE IFNULL(ledger_head.opening_balance, 0)
                                                    END
                                                ) AS opening_balance,
                                                group_chart.nature_group,
                                                ledger_head.DrCr
                                            FROM ledger_head
                                            LEFT JOIN group_chart ON ledger_head.group_id = group_chart.group_chart_id
                                            WHERE ledger_head.group_id IN ($string_tree_group)
                                            ;
                                      ");

      $oppening_balance = DB::select(
                   "SELECT
                              Sum(debit_credit.debit)AS op_total_debit,
                              sum(debit_credit.credit)AS op_total_credit
                    FROM   ledger_head
                            LEFT JOIN debit_credit
                            ON        ledger_head.ledger_head_id = debit_credit.ledger_head_id
                            LEFT JOIN transaction_master
                            ON        debit_credit.tran_id = transaction_master.tran_id
                    WHERE  ledger_head.group_id IN ($string_tree_group) AND transaction_master.transaction_date < :op_from_date

                        ",['op_from_date'=> $request->from_date]);



          $monthWiseDebitCredit= DB::select(
                        "SELECT transaction_master.transaction_date,
                                Year(transaction_master.transaction_date)     AS transaction_year,
                                Month(transaction_master.transaction_date)    AS transaction_month,
                                SUM(debit_credit.debit)  AS debit_sum,
                                SUM(debit_credit.credit) AS credit_sum
                        FROM   transaction_master
                                LEFT JOIN voucher_setup
                                ON        voucher_setup.voucher_id = transaction_master.voucher_id
                                LEFT JOIN debit_credit
                                ON        transaction_master.tran_id = debit_credit.tran_id
                                LEFT JOIN ledger_head
                                ON        debit_credit.ledger_head_id = ledger_head.ledger_head_id
                        WHERE  ledger_head.group_id IN ($string_tree_group)
                                AND transaction_master.transaction_date BETWEEN :sales_from_date AND :sales_to_date
                        GROUP  BY transaction_year,
                                transaction_month
                        ORDER  BY transaction_year,
                                transaction_month;

        ",['sales_from_date' => $request->from_date,'sales_to_date'=> $request->to_date]);
         return ['monthWiseDebitCredit' =>  $monthWiseDebitCredit,'ledger_opening_balance'=>$oppening_ledger_blance,'opening_balance'=>$oppening_balance];

    }
}
