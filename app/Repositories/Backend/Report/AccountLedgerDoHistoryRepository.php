<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class AccountLedgerDoHistoryRepository implements AccountLedgerDoHistoryInterface
{
    public function getAccountLedgerDoHistoryOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;


       // stock in
        $stock_in= $this->stock_in_analysis_query( $from_date, $to_date, $request);
       // stock out

         $stock_out= $this->stock_out_analysis_query( $from_date, $to_date,$request);

        return ['stock_in' => $stock_in ?? '', 'stock_out' => $stock_out ?? ''];
    }

    public function stock_in_analysis_query( $from_date, $to_date,$request)
    {
        $params = [];
        $ledger_head_id='';
        if($request->ledger_head_id!=0){
            $ledger_head_id = "ledger_head.ledger_head_id = :ledger_head_id AND";
            $params['ledger_head_id']=$request->ledger_head_id;
        }

        $query_in = "SELECT transaction_master.invoice_no,
                            transaction_master.narration,
                            transaction_master.tran_id,
                            transaction_master.transaction_date,
                            voucher_setup.voucher_name,
                            voucher_setup.voucher_type_id,
                            ledger_head.ledger_name,
                            stock_in.stock_in_qty,
                            stock_in.stock_in_total
                    FROM    transaction_master
                    INNER JOIN
                            (
                                    SELECT   tran_id,
                                            Sum(stock_in.qty) AS stock_in_qty,
                                            Sum(stock_in.total) AS stock_in_total
                                    FROM     stock_in
                                    GROUP BY   tran_id) AS stock_in

                    ON         transaction_master.tran_id=stock_in.tran_id
                    INNER JOIN  debit_credit
                    ON         transaction_master.tran_id=debit_credit.tran_id
                    INNER JOIN ledger_head
                    ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                    LEFT JOIN  voucher_setup
                    ON         transaction_master.voucher_id=voucher_setup.voucher_id
                    WHERE      $ledger_head_id
                        transaction_master.transaction_date BETWEEN :from_date AND :to_date
                    GROUP BY   transaction_master.tran_id";

                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;

                return DB::select($query_in,$params);

    }

    public function stock_out_analysis_query( $from_date, $to_date,$request)
    {
        $params = [];
        $ledger_head_id='';
        if($request->ledger_head_id!=0){
            $ledger_head_id = "ledger_head.ledger_head_id = :ledger_head_id AND";
            $params['ledger_head_id']=$request->ledger_head_id;
        }


                 $query_out = "SELECT   transaction_master.invoice_no,
                                        transaction_master.narration,
                                        transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        voucher_setup.voucher_name,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        stock_out.stock_out_qty,
                                        stock_out.stock_out_total

                                FROM       transaction_master
                                INNER JOIN
                                        (
                                                SELECT   tran_id,
                                                         Sum(stock_out.qty) AS stock_out_qty,
                                                         Sum(stock_out.total) AS stock_out_total
                                                FROM     stock_out
                                                GROUP BY   tran_id) AS stock_out
                                ON         transaction_master.tran_id=stock_out.tran_id
                                INNER JOIN debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                INNER JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      $ledger_head_id
                                           transaction_master.transaction_date BETWEEN :from_date AND :to_date
                                GROUP BY   transaction_master.tran_id";
                                $params['from_date'] = $from_date;
                                $params['to_date'] = $to_date;

                        return DB::select($query_out,$params);

    }
}
