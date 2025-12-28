<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class VoucherRegisterRepository implements VoucherRegisterInterface
{
    public function getVoucherRegisterOfIndex($request = null)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if ($request->voucher_id == 0) {
                $voucher_sql = '';
            } else {
                if (strpos($request->voucher_id, 'v') !== false) {
                    $voucher_type_id = str_replace('v', '', $request->voucher_id);

                    $voucher_sql = "AND voucher_setup.voucher_type_id='$voucher_type_id'";
                } else {
                    $voucher_sql = "AND transaction_master.voucher_id='$request->voucher_id'";
                }
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $voucher_sql = '';
        }

        return DB::select(
                       "SELECT
                            transaction_master.tran_id,
                            transaction_master.invoice_no,
                            transaction_master.transaction_date,
                            transaction_master.voucher_id,
                            transaction_master.narration,
                            voucher_setup.voucher_type_id,
                            debit_credit.ledger_head_id,
                            ledger_head.ledger_name,
                            voucher_setup.voucher_name,
                            debit_credit.dr_cr,
                            debit_credit.debit,
                            debit_credit.credit,
                            SUM(debit_credit.debit) AS debit_sum,
                            SUM(debit_credit.credit) AS credit_sum
                        FROM (transaction_master
                                INNER JOIN voucher_setup
                                ON voucher_setup.voucher_id=transaction_master.voucher_id
                            )
                        LEFT OUTER JOIN
                        (debit_credit INNER JOIN ledger_head
                            ON ledger_head.ledger_head_id=debit_credit.ledger_head_id
                        )
                        ON (debit_credit.tran_id=transaction_master.tran_id)
                        WHERE transaction_master.transaction_date BETWEEN '$from_date' AND '$to_date'  $voucher_sql
                        Group by transaction_master.tran_id
                        ORDER BY transaction_master.tran_id DESC
                    "
        );
    }
}
