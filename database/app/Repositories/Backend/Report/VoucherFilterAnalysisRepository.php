<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class VoucherFilterAnalysisRepository implements VoucherFilterAnalysisInterface
{
    public function getVoucherFilterAnalysisOfIndex($request = null)
    {
        $debit_in="";
        $request_debit="";
        $credit_in="";
        $request_credit="";
        $rate_in_out="";
        $request_rate="";
        $tran_date="";
        $request_date="";
       if (isset($request->date)) {
          $request_date=$request->date;
          $date=implode("','",$request->date);
          $tran_date="AND transaction_master.transaction_date IN('$date')";
       }

       if (isset($request->rate)) {
        $request_rate=$request->rate;
        $rate=implode(",",$request->rate);
        $rate_in_out="AND stock_in.rate IN($rate) OR stock_out.rate IN($rate)" ;
       }

       if (isset($request->debit)) {
          $request_debit=$request->debit;
          $debit= implode("','",$request->debit);
          $debit_in="AND ledger_head.ledger_name IN('$debit')";
       }

       if (isset($request->credit)) {
          $request_credit=$request->credit;
          $credit=implode("','",$request->credit);
          $credit_in="AND ledger_head.ledger_name IN('$credit')" ;
       }
        if (isset($request->voucher_id)) {
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
          $date_between="AND transaction_master.transaction_date BETWEEN '$from_date'AND '$to_date'";
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $voucher_sql = '';
            $date_between=  "";
        }
        // dd($request->all());
         $data= DB::select(
                            "SELECT
                                    transaction_master.tran_id,
                                    transaction_master.invoice_no,
                                    transaction_master.transaction_date,
                                    transaction_master.voucher_id,
                                    transaction_master.narration,
                                    voucher_setup.voucher_id,
                                    voucher_setup.voucher_name,
                                    stock_in.qty AS stock_in_qty,
                                    stock_in.rate AS stock_in_rate,
                                    stock_item_in.product_name AS in_product_name,
                                    godowns_in.godown_name AS godowns_in,
                                    unitsof_measure_in.symbol AS symbol_in,
                                    stock_out.qty AS stock_out_qty,
                                    stock_out.rate AS stock_out_rate,
                                    stock_item_out.product_name AS out_product_name,
                                    godowns_out.godown_name AS godowns_out,
                                    unitsof_measure_out.symbol AS symbol_out,
                                    IF(
                                    voucher_setup.voucher_type_id = 19
                                    AND voucher_setup.commission_is = 1,
                                    (
                                        SELECT
                                        ledger_head.ledger_name
                                        FROM
                                        debit_credit
                                        INNER JOIN ledger_head ON debit_credit.ledger_head_id = ledger_head.ledger_head_id
                                        WHERE
                                        debit_credit.tran_id = transaction_master.tran_id
                                        AND debit_credit.stock_item_id = stock_item_out.stock_item_id
                                        AND debit_credit.dr_cr = 'Cr'
                                        AND debit_credit.comm_level IS NULL $credit_in
                                    ),
                                    (
                                        SELECT
                                        ledger_head.ledger_name
                                        FROM
                                        debit_credit
                                        INNER JOIN ledger_head ON debit_credit.ledger_head_id = ledger_head.ledger_head_id
                                        WHERE
                                        debit_credit.tran_id = transaction_master.tran_id
                                        AND debit_credit.dr_cr = 'Cr'
                                        AND debit_credit.comm_level IS NULL $credit_in
                                    )
                                    ) AS credit_ledger_name,
                                    (
                                    SELECT
                                        ledger_head.ledger_name
                                    FROM
                                        debit_credit
                                        INNER JOIN ledger_head ON debit_credit.ledger_head_id = ledger_head.ledger_head_id
                                    WHERE
                                        debit_credit.tran_id = transaction_master.tran_id
                                        AND debit_credit.dr_cr = 'Dr'
                                        AND debit_credit.comm_level IS NULL $debit_in
                                    ) AS debit_ledger_name
                                FROM
                                    transaction_master
                                    INNER JOIN voucher_setup ON voucher_setup.voucher_id = transaction_master.voucher_id
                                    LEFT JOIN stock_in ON transaction_master.tran_id = stock_in.tran_id
                                    LEFT JOIN stock_item AS stock_item_in ON stock_in.stock_item_id = stock_item_in.stock_item_id
                                    LEFT JOIN godowns AS godowns_in ON stock_in.godown_id = godowns_in.godown_id
                                    LEFT JOIN unitsof_measure AS unitsof_measure_in ON stock_item_in.unit_of_measure_id = unitsof_measure_in.unit_of_measure_id
                                    LEFT JOIN stock_out ON transaction_master.tran_id = stock_out.tran_id
                                    LEFT JOIN stock_item AS stock_item_out ON stock_out.stock_item_id = stock_item_out.stock_item_id
                                    LEFT JOIN godowns AS godowns_out ON stock_out.godown_id = godowns_out.godown_id
                                    LEFT JOIN unitsof_measure AS unitsof_measure_out ON stock_item_out.unit_of_measure_id = unitsof_measure_out.unit_of_measure_id
                                WHERE
                                    voucher_setup.voucher_type_id IN(10, 19, 21, 22, 23, 24, 25, 29)
                                    $date_between $voucher_sql  $tran_date $rate_in_out
                    "
        );

        return ['data'=>$data,'request_debit'=>$request_debit,'request_credit'=>$request_credit,'request_rate'=>$request_rate,'request_date'=>$request_date];
    }


}
