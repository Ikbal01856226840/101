<?php

namespace App\Services\RateCalculation;
use Illuminate\Support\Facades\DB;


class ClosingBalance
{
    public function ClosingRate($from_date, $to_date)
    {
        $query= "WITH s_summary AS (
            SELECT
                stock_item_id,
                godown_id,
                ABS(SUM(inwards_qty - outwards_qty)) AS stock_qty
            FROM stock
            WHERE tran_date <= :to_date_9
            GROUP BY stock_item_id, godown_id
        )
        ,
        s_latest AS (
            SELECT * FROM (
                SELECT
                    stock_item_id,
                    godown_id,
                    current_rate,
                    ROW_NUMBER() OVER (
                        PARTITION BY stock_item_id, godown_id
                        ORDER BY tran_date DESC, tran_id DESC, id DESC
                    ) AS rn
                FROM stock
                WHERE tran_date <= :to_date_4
            ) ranked
            WHERE rn = 1
        ),
        weighted_rates AS (
            SELECT
                s_summary.stock_item_id,
                SUM(s_summary.stock_qty * s_latest.current_rate) / NULLIF(SUM(s_summary.stock_qty), 0) AS current_rate
            FROM s_summary
            JOIN s_latest
                ON s_summary.stock_item_id = s_latest.stock_item_id
                AND s_summary.godown_id = s_latest.godown_id
            GROUP BY s_summary.stock_item_id
        ),
       
        cte AS (
            SELECT
                si.stock_group_id,
                si.stock_item_id,
                si.product_name,
                um.symbol,
                SUM(CASE WHEN s.tran_date BETWEEN :from_date AND :to_date THEN s.inwards_qty ELSE 0 END) AS stock_qty_in,
                SUM(CASE WHEN s.tran_date BETWEEN :from_date_1 AND :to_date_1 THEN s.inwards_value ELSE 0 END) AS stock_total_in,
                SUM(CASE WHEN s.tran_date BETWEEN :from_date_2 AND :to_date_2 THEN s.outwards_qty ELSE 0 END) AS stock_qty_out,
                SUM(CASE WHEN s.tran_date BETWEEN :from_date_3 AND :to_date_3 THEN s.outwards_value ELSE 0 END) AS stock_total_out,
                SUM(CASE WHEN s.tran_date < :op_from_date_1 THEN (s.inwards_qty - s.outwards_qty) ELSE 0 END) AS stock_qty_opening,
                wr.current_rate
            FROM stock_item si
            JOIN stock s ON s.stock_item_id = si.stock_item_id
            LEFT JOIN unitsof_measure um ON um.unit_of_measure_id = si.unit_of_measure_id
            LEFT JOIN weighted_rates wr ON wr.stock_item_id = si.stock_item_id
            GROUP BY si.stock_item_id, si.stock_group_id, si.product_name, um.symbol, wr.current_rate
        )

       SELECT SUM(closing_balance.current_rate*closing_balance.current_qty) AS total_val FROM(SELECT
                   cte.current_rate,
                   ((COALESCE(cte.stock_qty_in, 0) + COALESCE(cte.stock_qty_opening, 0)) - COALESCE(cte.stock_qty_out, 0)) AS current_qty
        FROM stock_group sg
        LEFT JOIN cte ON sg.stock_group_id = cte.stock_group_id
       
        ORDER BY stock_group_name DESC, product_name DESC) AS closing_balance
   
";
        $params_without_godown['from_date'] = $from_date;
        $params_without_godown['to_date'] = $to_date;
        $params_without_godown['from_date_1'] = $from_date;
        $params_without_godown['to_date_1'] = $to_date;
        $params_without_godown['from_date_2'] = $from_date;
        $params_without_godown['to_date_2'] = $to_date;
        $params_without_godown['from_date_3'] = $from_date;
        $params_without_godown['to_date_3'] = $to_date;
        $params_without_godown['to_date_4'] = $to_date;
        $params_without_godown['to_date_9'] = $to_date;
        $params_without_godown['op_from_date_1'] = $from_date;
       
        return DB::select($query,$params_without_godown);
    }

   
}
