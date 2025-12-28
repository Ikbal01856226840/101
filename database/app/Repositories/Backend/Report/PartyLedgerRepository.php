<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class PartyLedgerRepository implements PartyLedgerInterface
{
    public function PartyLedgerOfIndex($request = null)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if ($request->ledger_id == 0) {
                $ledger_id = '';
            } else {
                $ledger_id = "debit_credit.ledger_head_id='$request->ledger_id' AND";
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $ledger_id = '';
        }
        if ($request->ledger_id == 0) {
            return  DB::select('CALL GetLedgerReport(?, ?)', array($from_date,$to_date));
            // return DB::select("SELECT      group_chart.group_chart_id,
            //                                 group_chart.nature_group,
            //                                 ledger_head.ledger_name,
            //                                 ledger_head.ledger_head_id,
            //                                 ledger_head.alias,
            //                                 ledger_head.opening_balance,
            //                                 t1.total_debit,
            //                                 t1.total_credit,
            //                                 op.total_debit AS op_group_debit,
            //                                 op.total_credit AS op_group_credit
            //                     FROM      group_chart
            //                     INNER JOIN ledger_head
            //                     ON        group_chart.group_chart_id=ledger_head.group_id
            //                     LEFT JOIN
            //                                 (
            //                                         SELECT    debit_credit.ledger_head_id,
            //                                                     sum(debit_credit.debit)  AS total_debit,
            //                                                     sum(debit_credit.credit) AS total_credit
            //                                         FROM      debit_credit
            //                                         INNER JOIN transaction_master
            //                                         ON        debit_credit.tran_id=transaction_master.tran_id
            //                                         WHERE     transaction_master.transaction_date BETWEEN '$from_date' AND       '$to_date'
            //                                         GROUP BY  debit_credit.ledger_head_id) AS t1
            //                     ON        ledger_head.ledger_head_id=t1.ledger_head_id
            //                     LEFT JOIN
            //                                 (
            //                                         SELECT    debit_credit.ledger_head_id,
            //                                                     sum(debit_credit.debit)  AS total_debit,
            //                                                     sum(debit_credit.credit) AS total_credit
            //                                         FROM      debit_credit
            //                                         INNER JOIN transaction_master
            //                                         ON        debit_credit.tran_id=transaction_master.tran_id
            //                                         WHERE     transaction_master.transaction_date <'$from_date'
            //                                         GROUP BY  debit_credit.ledger_head_id) AS op
            //                     ON        ledger_head.ledger_head_id=op.ledger_head_id
            //             ");
        } else {
            $party_ledger = DB::select(
                "SELECT transaction_master.tran_id,
                                                transaction_master.invoice_no,
                                                transaction_master.transaction_date,
                                                transaction_master.voucher_id,
                                                voucher_setup.voucher_type_id,
                                                debit_credit.debit_credit_id,
                                                debit_credit.ledger_head_id,
                                                ledger_head.ledger_name,
                                                ledger_head.drcr,
                                                ledger_head.opening_balance,
                                                voucher_setup.voucher_name,
                                                debit_credit.dr_cr,
                                                Sum(debit_credit.debit)  AS debit_sum,
                                                Sum(debit_credit.credit) AS credit_sum
                                        FROM   (transaction_master
                                                INNER JOIN voucher_setup
                                                        ON voucher_setup.voucher_id = transaction_master.voucher_id )
                                                LEFT OUTER JOIN (debit_credit
                                                                INNER JOIN ledger_head
                                                                        ON ledger_head.ledger_head_id =
                                                                            debit_credit.ledger_head_id )
                                                            ON ( debit_credit.tran_id = transaction_master.tran_id )
                                        WHERE  transaction_master.transaction_date BETWEEN '$from_date' AND '$to_date'
                                                AND debit_credit.ledger_head_id =$request->ledger_id
                                        GROUP  BY transaction_master.tran_id"

            );
            $op_party_ledger = DB::select(
                "   SELECT      Sum(debit_credit.debit)AS op_total_debit, sum(debit_credit.credit)AS op_total_credit
                                    FROM        debit_credit
                                    INNER JOIN ledger_head
                                    ON         debit_credit.ledger_head_id=ledger_head.ledger_head_id
                                    INNER JOIN transaction_master
                                    ON         debit_credit.tran_id=transaction_master.tran_id
                                    WHERE      $ledger_id (transaction_master.transaction_date <'$from_date' )
                                    GROUP BY   ledger_head.ledger_head_id
                                "
            );
            $group_chart_nature = DB::table('group_chart')->join('ledger_head', 'group_chart.group_chart_id', '=', 'ledger_head.group_id')->where('ledger_head.ledger_head_id', $request->ledger_id)->first(['ledger_head.opening_balance', 'group_chart.nature_group']);

            return $data = ['party_ledger' => $party_ledger, 'op_party_ledger' => $op_party_ledger, 'group_chart_nature' => $group_chart_nature];
        }
    }
}
