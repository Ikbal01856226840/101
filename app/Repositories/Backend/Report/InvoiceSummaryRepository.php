<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class InvoiceSummaryRepository implements InvoiceSummaryInterface
{
    public function getInvoiceSummaryOfIndex($request){

        $voucher_sql = '';
        $params = [];
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if ($request->voucher_id == 0) {
                $voucher_sql = '';
            } else {
                if (strpos($request->voucher_id, 'v') !== false) {
                    $voucher_type_id = str_replace('v', '', $request->voucher_id);
                    $voucher_sql = "AND voucher_setup.voucher_type_id=:voucher_type_id";
                    $params['voucher_type_id'] = $voucher_type_id;
                } else {
                    $voucher_sql = "AND transaction_master.voucher_id=:voucher_id";
                    $params['voucher_id'] = $request->voucher_id;
                }
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $voucher_sql = '';
        }




                 $query="SELECT         transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        transaction_master.user_id,
                                        transaction_master.voucher_id,
                                        transaction_master.invoice_no,
                                        transaction_master.ref_no,
                                         transaction_master.gprf,
                                        transaction_master.narration,
                                        transaction_master.other_details,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        voucher_setup.voucher_name,
                                        debit_credit.dr_cr,
                                        debit_credit.debit,
                                        debit_credit.credit,
                                IF(
                                    voucher_type_id IN (10, 24, 29, 22, 21),
                                    (
                                        SELECT SUM(st_in.qty)
                                        FROM stock_in AS st_in
                                        WHERE st_in.tran_id = transaction_master.tran_id
                                    ),
                                    ''
                                ) AS stock_in_sum,

                                IF(
                                    voucher_type_id IN (10, 24, 29, 22, 21),
                                    (
                                        SELECT SUM(st_in.total)
                                        FROM stock_in AS st_in
                                        WHERE st_in.tran_id = transaction_master.tran_id
                                    ),
                                    ''
                                ) AS stock_in_total_sum,

                                IF(
                                    voucher_type_id IN (19, 23, 25, 22, 21),
                                    (
                                        SELECT SUM(st_out.qty)
                                        FROM stock_out AS st_out
                                        WHERE st_out.tran_id = transaction_master.tran_id
                                    ),
                                    ''
                                ) AS stock_out_sum,

                                IF(
                                    voucher_type_id IN (19, 23, 25, 22, 21),
                                    (
                                        SELECT SUM(st_out.total)
                                        FROM stock_out AS st_out
                                        WHERE st_out.tran_id = transaction_master.tran_id
                                    ),
                                    ''
                                ) AS stock_out_total_sum
                        FROM            (transaction_master
                        INNER JOIN      voucher_setup
                        ON              voucher_setup.voucher_id=transaction_master.voucher_id)
                        LEFT OUTER JOIN (debit_credit
                        INNER JOIN      ledger_head
                        ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id )
                        ON              (
                                                        debit_credit.tran_id=transaction_master.tran_id)
                        WHERE           voucher_setup.voucher_type_id NOT IN(20,30,1,6,8)  AND transaction_master.transaction_date BETWEEN :from_date AND             :to_date   $voucher_sql
                        GROUP BY          transaction_master.tran_id
                        ORDER BY    transaction_master.transaction_date ASC,transaction_master.tran_id ASC

                        ";
                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;
        return DB::select($query,$params);
    }

}
