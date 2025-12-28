<?php

namespace App\Services\ErrorConsole;
use Illuminate\Support\Facades\DB;
class ErrorConsole
{
    public function errorConsoleGetData($request)
    {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $query =      "(SELECT  1  AS type,
                            dc.tran_id,
                            transaction_master.invoice_no,
                            transaction_master.narration,
                            transaction_master.transaction_date,
                            voucher_setup.voucher_type_id,
                            voucher_setup.voucher_name,
                            NULL AS total_debit,
                            NULL AS total_credit,
                            NULL AS balance,
                            NULL AS min_ledger_id,
                            CASE
                            WHEN dc.commission_type = 3
                                AND dc.comm_level = 2
                                AND dc.stock_item_id = so.stock_item_id THEN (
                            dc.debit - ( dc.commission
                                        * so.qty ) )
                            ELSE 0
                            end  AS adjusted_debit
                    FROM   debit_credit dc
                            INNER JOIN stock_out so
                                    ON so.tran_id = dc.tran_id
                            LEFT JOIN transaction_master
                                ON transaction_master.tran_id = dc.tran_id
                            LEFT JOIN voucher_setup
                                ON voucher_setup.voucher_id = transaction_master.voucher_id
                    WHERE  dc.commission_type = 3
                            AND dc.comm_level = 2
                            AND dc.stock_item_id = so.stock_item_id
                            AND Abs(dc.debit - ( dc.commission * so.qty )) > 1
                            AND transaction_master.transaction_date BETWEEN   :from_date AND             :to_date )
                                
                    UNION ALL
                    (SELECT 2                                               AS type,
                            d1.tran_id,
                            t4.invoice_no,
                            t4.narration,
                            t4.transaction_date,
                            v4.voucher_type_id,
                            v4.voucher_name,
                            Sum(Ifnull(d1.debit, 0))                        AS total_debit,
                            Sum(Ifnull(d1.credit, 0))                       AS total_credit,
                            Sum(Ifnull(d1.debit, 0) - Ifnull(d1.credit, 0)) AS balance,
                            Min(d1.ledger_head_id)                          AS min_ledger_id,
                            NULL                                            AS adjusted_debit
                    FROM   debit_credit AS d1
                            INNER JOIN transaction_master t4
                                ON t4.tran_id = d1.tran_id
                            INNER JOIN voucher_setup AS v4
                                ON v4.voucher_id = t4.voucher_id
                    WHERE  t4.transaction_date BETWEEN   :from_date_1 AND             :to_date_1
                    GROUP  BY d1.tran_id
                    HAVING Abs(total_debit - total_credit) > 1
                            OR min_ledger_id = 0)
                    UNION ALL
                    (SELECT 3                         AS type,
                            s_in.tran_id              AS tran_id,
                            t1.invoice_no,
                            t1.narration,
                            t1.transaction_date,
                            v1.voucher_type_id,
                            v1.voucher_name,
                            SUM(IFNULL(s_in.qty, 0) * IFNULL(s_in.rate, 0)) AS total_debit,
                            Sum(s_in.total)           AS total_credit,
                            Min(s_in.godown_id)       AS balance,
                            Min(s_in.stock_item_id)   AS min_ledger_id,
                            NULL                      AS adjusted_debit
                    FROM   stock_in AS s_in
                            INNER JOIN transaction_master AS t1
                                ON t1.tran_id = s_in.tran_id
                            INNER JOIN voucher_setup AS v1
                                ON v1.voucher_id = t1.voucher_id
                    WHERE  t1.transaction_date BETWEEN   :from_date_2 AND             :to_date_2
                    GROUP  BY s_in.tran_id
                    HAVING Abs(total_debit - total_credit) > 1
                            OR min_ledger_id = 0
                            OR balance = 0)
                    UNION ALL
                    (SELECT 4                           AS type,
                            s_out.tran_id               AS tran_id,
                            t2.invoice_no,
                            t2.narration,
                            t2.transaction_date,
                            v2.voucher_type_id,
                            v2.voucher_name,
                            Abs(Sum(s_out.qty * s_out.rate)) AS total_debit,
                            Abs(Sum(s_out.total))            AS total_credit,
                            Min(s_out.godown_id)        AS balance,
                            Min(s_out.stock_item_id)    AS min_ledger_id,
                            NULL                        AS adjusted_debit
                    FROM   stock_out AS s_out
                            INNER JOIN transaction_master AS t2
                                ON t2.tran_id = s_out.tran_id
                            INNER JOIN voucher_setup AS v2
                                ON v2.voucher_id = t2.voucher_id
                    WHERE  t2.transaction_date BETWEEN   :from_date_3 AND             :to_date_3
                    GROUP  BY s_out.tran_id
                    HAVING Abs(total_debit - total_credit) > 1
                            OR min_ledger_id = 0
                            OR balance = 0)
                    UNION ALL
                    (SELECT 5                  AS type,
                            t3.tran_id         AS tran_id,
                            t3.invoice_no,
                            t3.narration,
                            t3.transaction_date,
                            v3.voucher_type_id,
                            v3.voucher_name,
                            NULL               AS total_debit,
                            NULL               AS total_credit,
                            NULL               AS balance,
                            Min(t3.voucher_id) AS min_ledger_id,
                            NULL               AS adjusted_debit
                    FROM   transaction_master AS t3
                             INNER JOIN voucher_setup AS v3
                                ON v3.voucher_id = t3.voucher_id
                    WHERE  t3.transaction_date BETWEEN   :from_date_4 AND             :to_date_4
                    GROUP  BY t3.tran_id
                    HAVING min_ledger_id = 0)
                    ORDER  BY tran_id DESC";

                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;
                $params['from_date_1'] = $from_date;
                $params['to_date_1'] = $to_date;
                $params['from_date_2'] = $from_date;
                $params['to_date_2'] = $to_date;
                $params['from_date_3'] = $from_date;
                $params['to_date_3'] = $to_date;
                $params['from_date_4'] = $from_date;
                $params['to_date_4'] = $to_date;
             return   DB::select($query, $params);
                   
    }
  
}
