<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class VoucherMonthlySummaryRepository implements VoucherMonthlySummaryInterface
{
    public function getVoucherMonthlySummaryOfIndex($request)
    {
        $params_voucher= [];
        $params_voucher_can=[];
        $exisis_sql="";
        $voucher_type_id_check='';
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if (strpos($request->voucher_id, 'v') !== false) {
            $voucher_type_id = str_replace('v', '', $request->voucher_id);
            $voucher_sql = "AND voucher_setup.voucher_type_id=:voucher_type_id";
            $params_voucher['voucher_type_id'] = $voucher_type_id;
            $params_voucher_can['voucher_type_id'] = $voucher_type_id;
            $voucher_type_id_check=$voucher_type_id;
        } else {
            $voucher_sql = "AND transaction_master.voucher_id=:voucher_id";
            $params_voucher['voucher_id'] = $request->voucher_id;
            $params_voucher_can['voucher_id'] = $request->voucher_id;

            $voucher_type_id_check=DB::table('voucher_setup')->where('voucher_id',$request->voucher_id)->first()->voucher_type_id;

        }

        // VOUCHER TYPE CHECKING
       if($voucher_type_id_check==1||$voucher_type_id_check==6||$voucher_type_id_check==8||$voucher_type_id_check==14){

        $exisis_sql="SELECT    m.tran_id  FROM  transaction_master AS m
                    LEFT JOIN debit_credit
                    ON        debit_credit.tran_id = m.tran_id
                    WHERE     debit_credit.tran_id IS NULL
                    AND       m.tran_id=transaction_master.tran_id
                    GROUP BY  m.tran_id,m.voucher_id";
       }else{
        $exisis_sql="SELECT    m.tran_id  FROM  transaction_master AS m
                    LEFT JOIN stock_in ON stock_in.tran_id = m.tran_id
                    LEFT JOIN stock_out ON stock_out.tran_id = m.tran_id
                    WHERE     stock_in.tran_id IS NULL
                    AND       stock_out.tran_id IS NULL
                    AND       m.tran_id=transaction_master.tran_id
                    GROUP BY  m.tran_id,m.voucher_id";
       }
        $query_voucher="SELECT  count(transaction_master.voucher_id) AS tran_id_count,
                                transaction_master.transaction_date,
                                transaction_master.tran_id
                        FROM             voucher_setup
                        INNER JOIN      transaction_master
                        ON              voucher_setup.voucher_id=transaction_master.voucher_id
                        WHERE           transaction_master.transaction_date BETWEEN :from_date AND             :to_date $voucher_sql
                        GROUP BY  year(transaction_master.transaction_date),
                                month(transaction_master.transaction_date)
                    ";
        $params_voucher['from_date'] = $from_date;
        $params_voucher['to_date'] = $to_date;
        $month_wise_voucher= DB::select($query_voucher,$params_voucher);

        $query_calcelled="SELECT  count(transaction_master.voucher_id) AS tran_id_can_count,
                                    transaction_master.transaction_date,
                                    transaction_master.tran_id

                            FROM             voucher_setup
                            INNER JOIN      transaction_master
                            ON              voucher_setup.voucher_id=transaction_master.voucher_id
                            WHERE           transaction_master.transaction_date BETWEEN :from_date_1 AND    :to_date_1 $voucher_sql AND  EXISTS($exisis_sql)

                            GROUP BY  year(transaction_master.transaction_date),
                                    month(transaction_master.transaction_date)
                    ";

             $params_voucher_can['from_date_1'] = $from_date;
             $params_voucher_can['to_date_1'] = $to_date;

            $month_wise_voucher_cal= DB::select($query_calcelled,$params_voucher_can);

        return ['month_wise_voucher'=>$month_wise_voucher,'month_wise_voucher_cal'=>$month_wise_voucher_cal];
    }
}
