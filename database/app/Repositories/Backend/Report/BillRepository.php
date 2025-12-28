<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class BillRepository implements BillInterface
{
    public function getBillOfIndex($request)
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
                        "SELECT      transaction_master.tran_id,
                                        transaction_master.invoice_no,
                                        transaction_master.transaction_date,
                                        transaction_master.voucher_id,
                                        transaction_master.narration,
                                        voucher_setup.voucher_type_id,
                                        debit_credit.debit_credit_id,
                                        debit_credit.ledger_head_id,
                                        ledger_head.ledger_name,
                                        voucher_setup.voucher_name,
                                        debit_credit.debit,
                                        debit_credit.credit,
                                        debit_credit.dr_cr,
                                        IF(debit_credit.debit !=0,Sum(
                                        CASE
                                                        WHEN voucher_type_id=1
                                                        OR              voucher_type_id =6
                                                        OR              voucher_type_id =8
                                                        OR              voucher_type_id =14 THEN debit_credit.debit
                                        end),0) AS debit_sum,
                                        IF(debit_credit.credit !=0,Sum(
                                        CASE
                                                        WHEN voucher_type_id=1
                                                        OR              voucher_type_id =6
                                                        OR              voucher_type_id =8
                                                        OR              voucher_type_id =14 THEN debit_credit.credit
                                        end),0) AS credit_sum,
                                        IF(voucher_type_id=10
                        OR              voucher_type_id =24
                        OR              voucher_type_id =29
                        OR              voucher_type_id =22
                        OR              voucher_type_id =21,
                                        (
                                            SELECT Sum(st_in.qty)
                                            FROM   stock_in AS st_in
                                            WHERE  st_in.tran_id=transaction_master.tran_id ),'') AS stock_in_sum,
                                        IF(voucher_type_id=19
                        OR              voucher_type_id =23
                        OR              voucher_type_id =25
                        OR              voucher_type_id =22
                        OR              voucher_type_id =21,
                                        (
                                                SELECT   Sum(st_out.qty)
                                                FROM     stock_out AS st_out
                                                WHERE    st_out.tran_id=transaction_master.tran_id
                                                GROUP BY st_out.tran_id ),'') AS stock_out_sum
                        FROM            (transaction_master
                        INNER JOIN      voucher_setup
                        ON              voucher_setup.voucher_id=transaction_master.voucher_id )
                        LEFT OUTER JOIN (debit_credit
                        INNER JOIN      ledger_head
                        ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id )
                        ON              (
                                                        debit_credit.tran_id=transaction_master.tran_id)
                        WHERE           ((voucher_setup.voucher_type_id IN(19,22,24) AND  transaction_master.delivery_status!=0)OR (voucher_setup.voucher_type_id NOT IN(19,22,24)) ) AND transaction_master.transaction_date BETWEEN '$from_date' AND             '$to_date' $voucher_sql
                        GROUP BY        transaction_master.tran_id
                        ORDER BY        transaction_master.tran_id DESC
                        "
        );
    }
}
