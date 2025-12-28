<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class ChallanRepository implements ChallanInterface
{
    public function getChallanfIndex($request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $delivery_status = $request->delivery_status ? "delivery_status=$request->delivery_status AND" : 'delivery_status IN(1,2) AND';
        $godown_sql = "AND godowns.godown_id=$request->godown_id";

        return DB::select(
                   "SELECT         transaction_master.tran_id,
                                    transaction_master.invoice_no,
                                    transaction_master.transaction_date,
                                    transaction_master.voucher_id,
                                    transaction_master.other_details,
                                    transaction_master.delivery_status,
                                    voucher_setup.voucher_type_id,
                                    ledger_head.ledger_name,
                                    voucher_setup.voucher_name,
                                    order_approver.other_details AS order_approver
                    FROM            (transaction_master
                    INNER JOIN      voucher_setup
                    ON              voucher_setup.voucher_id=transaction_master.voucher_id )
                    LEFT OUTER JOIN (debit_credit
                    INNER JOIN      ledger_head
                    ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id )
                    ON              (
                                                    debit_credit.tran_id=transaction_master.tran_id)
                    INNER JOIN      stock_out
                    ON              transaction_master.tran_id=stock_out.tran_id
                    INNER JOIN      godowns
                    ON              stock_out.godown_id=godowns.godown_id
                    INNER JOIN      order_approver
                    ON              order_approver.tran_id=transaction_master.tran_id
                    WHERE           $delivery_status voucher_setup.voucher_type_id IN(19,22,24)
                    AND             transaction_master.transaction_date BETWEEN '$from_date' AND             '$to_date' $godown_sql
                    AND             order_approver.order_approve_status=transaction_master.delivery_status
                    GROUP BY        transaction_master.tran_id
                    ORDER BY        transaction_master.tran_id DESC"
        );
    }
}
