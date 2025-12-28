<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class AccountLedgerVoucherRepository implements AccountLedgerVoucherInterface
{
    public function getAccountLedgerVoucherOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;

        return DB::select(
            "SELECT transaction_master.tran_id,
                                        transaction_master.invoice_no,
                                        transaction_master.transaction_date,
                                        transaction_master.voucher_id,
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
                                WHERE  debit_credit.ledger_head_id =$request->ledger_id  AND transaction_master.transaction_date BETWEEN '$from_date' AND '$to_date'

                                Group by transaction_master.tran_id"
        );

    }
}
