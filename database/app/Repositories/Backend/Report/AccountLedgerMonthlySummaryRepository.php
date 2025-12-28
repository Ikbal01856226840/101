<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class AccountLedgerMonthlySummaryRepository implements AccountLedgerMonthlySummaryInterface
{
    public function getAccountLedgerMonthlySummaryOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $party_ledger = DB::select(
            "SELECT transaction_master.tran_id,
                        transaction_master.transaction_date,
                        transaction_master.voucher_id,
                        voucher_setup.voucher_type_id,
                        ledger_head.ledger_head_id,
                        SUM(debit_credit.debit)  AS debit_sum,
                        SUM(debit_credit.credit) AS credit_sum
                FROM   (transaction_master
                        INNER JOIN voucher_setup
                                ON voucher_setup.voucher_id = transaction_master.voucher_id )
                        LEFT OUTER JOIN (debit_credit
                                        INNER JOIN ledger_head
                                                ON ledger_head.ledger_head_id =
                                                    debit_credit.ledger_head_id )
                                    ON ( debit_credit.tran_id = transaction_master.tran_id )
                WHERE  debit_credit.ledger_head_id =$request->ledger_id  AND transaction_master.transaction_date BETWEEN '$from_date' AND '$to_date'

                GROUP  BY  year(transaction_master.transaction_date),month(transaction_master.transaction_date)"
        );

        $op_party_ledger = DB::select(
            "   SELECT     Sum(debit_credit.debit)AS op_total_debit,
                                           sum(debit_credit.credit)AS op_total_credit
                                FROM       debit_credit
                                INNER JOIN ledger_head
                                ON         debit_credit.ledger_head_id=ledger_head.ledger_head_id
                                INNER JOIN transaction_master
                                ON         debit_credit.tran_id=transaction_master.tran_id
                                WHERE     debit_credit.ledger_head_id =$request->ledger_id  AND  (transaction_master.transaction_date <'$from_date' )
                                GROUP BY  debit_credit.ledger_head_id

                            "
        );
        $group_chart_nature = DB::table('group_chart')->join('ledger_head', 'group_chart.group_chart_id', '=', 'ledger_head.group_id')->where('ledger_head.ledger_head_id', $request->ledger_id)->first(['ledger_head.opening_balance', 'group_chart.nature_group']);

        return ['party_ledger' => $party_ledger, 'op_party_ledger' => $op_party_ledger, 'group_chart_nature' => $group_chart_nature];
    }
}
