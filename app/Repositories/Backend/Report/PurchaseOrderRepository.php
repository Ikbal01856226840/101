<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRepository implements PurchaseOrderInterface
{


    public function getPurchaseOrderOfIndex($request = null)
    {

        if (array_filter($request->all())) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = company()->financial_year_start;
            $to_date = date('Y-m-d');
        }
                       $query="SELECT   order_requisition_transaction_master.id,
                                        order_requisition_transaction_master.invoice_no,
                                        order_requisition_transaction_master.date,
                                        order_requisition_transaction_master.voucher_id,
                                        order_requisition_transaction_master.narration,
                                         order_requisition_transaction_master.reference_no,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        voucher_setup.voucher_name

                                FROM            (order_requisition_transaction_master
                                INNER JOIN      voucher_setup
                                ON              voucher_setup.voucher_id=order_requisition_transaction_master.voucher_id)
                                INNER JOIN      ledger_head
                                ON              ledger_head.ledger_head_id=order_requisition_transaction_master.ledger_id
                                WHERE           order_requisition_transaction_master.date BETWEEN :from_date AND             :to_date
                                GROUP BY        order_requisition_transaction_master.id
                                ORDER BY        order_requisition_transaction_master.date ASC,order_requisition_transaction_master.id ASC

                                ";
                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;
                return DB::select($query,$params);
    }


}
