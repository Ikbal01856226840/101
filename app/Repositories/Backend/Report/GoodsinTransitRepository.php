<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class GoodsinTransitRepository implements GoodsinTransitInterface
{
    public function getGoodsinTransitOfIndex($request)
    {
    
        $params = [];
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $dis_cen_id = "AND transaction_master.dis_cen_id=:dis_cen_id";
        $params['dis_cen_id'] =$request->dis_cen_id;

             $query="SELECT         transaction_master.tran_id,
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
                    WHERE           transaction_master.delivery_status IN(2,3) AND
                                    transaction_master.transaction_date BETWEEN :from_date AND             :to_date $dis_cen_id
                    AND             order_approver.order_approve_status=transaction_master.delivery_status AND voucher_setup.voucher_type_id=22
                    GROUP BY        transaction_master.tran_id ";

                  $params['from_date'] = $from_date;
                  $params['to_date'] = $to_date;

        return DB::select($query,$params);



    }
}
