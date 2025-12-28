<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class AccountLedgerVoucherRepository implements AccountLedgerVoucherInterface
{
    public function getAccountLedgerVoucherOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $params = [];
        $voucher_sql = '';
        if ($request->voucher_id == 0) {
            $voucher_sql = '';
        } else {
            if (strpos($request->voucher_id, 'v') !== false) {
                $voucher_type_id = str_replace('v', '', $request->voucher_id);
                $voucher_sql = "AND voucher_setup.voucher_type_id=$voucher_type_id";
            } else {
                $voucher_sql = "AND transaction_master.voucher_id=$request->voucher_id";
            }
        }
        // dd($voucher_sql);
        $params['ledger_id'] = $request->ledger_id;
        $params['from_date'] = $request->from_date;
        $params['to_date'] = $request->to_date;
        return DB::select(
            "SELECT transaction_master.tran_id,
                                        transaction_master.invoice_no,
                                        transaction_master.transaction_date,
                                        transaction_master.voucher_id,
                                        transaction_master.narration,
                                        transaction_master.ref_no,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        voucher_setup.voucher_name,
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
                                WHERE  debit_credit.ledger_head_id =:ledger_id  AND transaction_master.transaction_date BETWEEN :from_date AND :to_date $voucher_sql

                                Group by transaction_master.tran_id ORDER BY    transaction_master.transaction_date ASC,transaction_master.tran_id ASC",$params
        );

    }

    public function getAccountLedgerOpeningBalance($request = null){
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $op_party_ledger = DB::select(
            "   SELECT     Sum(debit_credit.debit)AS op_total_debit,
                                           sum(debit_credit.credit)AS op_total_credit
                                FROM       debit_credit
                                INNER JOIN ledger_head
                                ON         debit_credit.ledger_head_id=ledger_head.ledger_head_id
                                INNER JOIN transaction_master
                                ON         debit_credit.tran_id=transaction_master.tran_id
                                WHERE     debit_credit.ledger_head_id =:ledger_id  AND  (transaction_master.transaction_date <:from_date)
                                GROUP BY  debit_credit.ledger_head_id

                            ",['ledger_id'=>$request->ledger_id,'from_date'=>$from_date]
        );
        $group_chart_nature = DB::table('group_chart')
                                ->join('ledger_head', 'group_chart.group_chart_id', '=', 'ledger_head.group_id')
                                ->where('ledger_head.ledger_head_id', $request->ledger_id)
                                ->first([
                                            'ledger_head.opening_balance',
                                            'group_chart.nature_group',
                                            'ledger_head.DrCr'
                                        ]);

        return ['op_party_ledger' => $op_party_ledger, 'group_chart_nature' => $group_chart_nature];

    }
}
