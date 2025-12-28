<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class SalesOrderRepository implements SalesOrderInterface
{
    public function SalesOrderOfIndex($request)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if ($request->ledger_id == 0) {
                $ledger_id = '';
            } else {
                $ledger_id = "AND sales_order.credit_ledger_id='$request->ledger_id' ";
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $ledger_id = '';
        }

            return   DB::select(
                           "SELECT          transaction_master.tran_id,
                                            transaction_master.invoice_no,
                                            transaction_master.transaction_date,
                                            transaction_master.voucher_id,
                                            transaction_master.narration,
                                            voucher_setup.voucher_type_id,
                                            ledger_head.ledger_name,
                                            voucher_setup.voucher_name,
                                            sales_order.status
                            FROM            transaction_master
                            INNER JOIN      voucher_setup
                            ON              voucher_setup.voucher_id=transaction_master.voucher_id
                            INNER JOIN      sales_order
                            ON              sales_order.tran_id=transaction_master.tran_id
                            INNER JOIN      ledger_head
                            ON              ledger_head.ledger_head_id=sales_order.credit_ledger_id
                            WHERE           transaction_master.transaction_date BETWEEN '$from_date' AND             '$to_date' $ledger_id
                            GROUP BY        transaction_master.tran_id"
                            
                        
        );
    }
    public function SalesOrderUserWise($request)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if ($request->ledger_id == 0) {
                $ledger_id = '';
            } else {
                $ledger_id = "AND sales_order.credit_ledger_id='$request->ledger_id' ";
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $ledger_id = '';
        }
          $user_id = Auth()->id();
        
            return   DB::select(
                           "SELECT          transaction_master.tran_id,
                                            transaction_master.invoice_no,
                                            transaction_master.transaction_date,
                                            transaction_master.voucher_id,
                                            transaction_master.narration,
                                            voucher_setup.voucher_type_id,
                                            ledger_head.ledger_name,
                                            voucher_setup.voucher_name,
                                            sales_order.status,
                                            (SELECT Sum(sales_order.qty)
                                                FROM   sales_order AS st_qty
                                                WHERE st_qty.tran_id=transaction_master.tran_id group by st_qty.tran_id) AS stock_out_qty,
                                            (SELECT Sum(st_total.total)
                                            FROM   sales_order AS st_total
                                            WHERE  st_total.tran_id=transaction_master.tran_id group by st_total.tran_id) AS stock_out_total
                            FROM            transaction_master
                            INNER JOIN      voucher_setup
                            ON              voucher_setup.voucher_id=transaction_master.voucher_id
                            INNER JOIN      sales_order
                            ON              sales_order.tran_id=transaction_master.tran_id
                            INNER JOIN      ledger_head
                            ON              ledger_head.ledger_head_id=sales_order.credit_ledger_id
                            WHERE           transaction_master.user_id='$user_id' AND transaction_master.transaction_date BETWEEN '$from_date' AND             '$to_date' $ledger_id
                            GROUP BY        transaction_master.tran_id
                            ORDER BY        transaction_master.tran_id DESC
                        "
        );
    }
}
