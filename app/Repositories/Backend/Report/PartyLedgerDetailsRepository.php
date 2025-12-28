<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class PartyLedgerDetailsRepository implements PartyLedgerDetailsInterface
{
    public function PartyLedgerInDetails($request = null)
    {

        if ($request->sort_by == 1) {
            $sort_by = "transaction_date ASC";
        } else {
            if ($request->sort_type == 1) {
                $sort_by = ($request->sort_by == 2 ? 'debit ASC' : ($request->sort_by == 3 ? 'credit ASC' : ($request->sort_by == 4 ? ' dr_cr ASC' : ($request->sort_by == 5 ? 'ledger_name ASC' : ''))));
            } else {
                $sort_by = ($request->sort_by == 2 ? 'debit DESC' : ($request->sort_by == 3 ? 'credit DESC' : ($request->sort_by == 4 ? ' dr_cr DESC' : ($request->sort_by == 5 ? 'ledger_name DESC' : ''))));;
            }
        }


        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if ($request->ledger_id == 0) {
                $ledger_id = '';
            } else {
                $ledger_id = "debit_credit.ledger_head_id='$request->ledger_id' AND";
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $ledger_id = '';
        }

        $type_none_query = '';
        if ($request->description == 1) {
            $type_none_query = "(SELECT ledger_head.ledger_name FROM debit_credit
                    LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                    WHERE  debit_credit.tran_id=transaction_master.tran_id AND debit_credit.ledger_head_id NOT IN($request->ledger_id)  group by transaction_master.tran_id) AS party_name,";
        } else if ($request->description == 2 || $request->description == 3) {
            $type_none_query = "(SELECT CONCAT(SUM(inner_debit_credit.debit),'__',SUM(inner_debit_credit.credit),'__',ledger_head.ledger_name,'__',inner_debit_credit.dr_cr) FROM debit_credit AS inner_debit_credit
                LEFT JOIN ledger_head ON inner_debit_credit.ledger_head_id=ledger_head.ledger_head_id
                WHERE  inner_debit_credit.tran_id=transaction_master.tran_id AND inner_debit_credit.ledger_head_id NOT IN($request->ledger_id) AND  debit_credit.dr_cr!=inner_debit_credit.dr_cr  group by transaction_master.tran_id) AS party_name_debit_credit,";
        }

        if ($request->ledger_id == 0) {
            if ($request->is_debit_credit == 0) {
                $tran_left = "LEFT";
               $exists_sql=" WHERE       EXISTS (SELECT  debit_credit.ledger_head_id FROM  debit_credit
                                            INNER JOIN transaction_master
                                            ON debit_credit.tran_id = transaction_master.tran_id
                                            WHERE transaction_master.transaction_date BETWEEN :from_date_2 AND :to_date_2 AND debit_credit.ledger_head_id=lh.ledger_head_id)";
                                            $params['from_date_2'] = $from_date;
                                            $params['to_date_2'] = $to_date;
            } elseif ($request->is_debit_credit == 1) {
                $exists_sql="";
                $tran_left = "LEFT";
            } else {
                $tran_left = "LEFT";
            }
                        $query="    SELECT gc.group_chart_id,
                                        gc.nature_group,
                                        lh.ledger_name,
                                        lh.ledger_head_id,
                                        lh.alias,
                                        lh.DrCr,
                                        lh.opening_balance,
                                        Coalesce(Sum(CASE
                                                        WHEN tm.transaction_date < :op_from_date THEN
                                                            CASE
                                                            WHEN gc.nature_group IN ( 1, 3 ) THEN
                                                            (dc.debit - dc.credit )
                                                            WHEN gc.nature_group IN ( 2, 4 ) THEN
                                                             (dc.credit - dc.debit )
                                                            end
                                                        ELSE 0
                                                        end), 0) AS opening_debit_credit,
                                        Coalesce(Sum(CASE
                                                        WHEN tm.transaction_date BETWEEN :from_date AND :to_date
                                                        THEN
                                                           dc.debit
                                                        ELSE 0
                                                        end), 0) AS total_debit,
                                        Coalesce(Sum(CASE
                                                        WHEN tm.transaction_date BETWEEN :from_date_1 AND :to_date_1 THEN
                                                             dc.credit
                                                        ELSE 0
                                                        end), 0) AS total_credit
                                    FROM   group_chart gc
                                        INNER JOIN ledger_head lh
                                                ON gc.group_chart_id = lh.group_id
                                        LEFT JOIN debit_credit dc
                                                ON lh.ledger_head_id = dc.ledger_head_id
                                        $tran_left JOIN transaction_master tm
                                                ON dc.tran_id = tm.tran_id
                                        $exists_sql
                                    GROUP  BY lh.ledger_head_id
                                    ORDER  BY $sort_by
                  ";
                   $params['from_date'] = $from_date;
                   $params['to_date_1'] = $to_date;
                   $params['from_date_1'] = $from_date;
                   $params['to_date'] = $to_date;
                   $params['op_from_date'] = $from_date;

            return  DB::select($query,$params);

        } else {
            $party_ledger = DB::select(
                "SELECT transaction_master.tran_id,
                        transaction_master.invoice_no,
                        transaction_master.transaction_date,
                        transaction_master.user_id,
                        transaction_master.voucher_id,
                        transaction_master.narration,
                        transaction_master.customer_id,
                        transaction_master.other_details,
                        voucher_setup.voucher_type_id,
                        debit_credit.debit_credit_id,
                        debit_credit.ledger_head_id,
                        ledger_head.ledger_name,
                        ledger_head.drcr,
                        voucher_setup.voucher_name,
                        debit_credit.debit,
                        debit_credit.credit,
                        debit_credit.remark,
                        $type_none_query
                        Sum(debit_credit.debit)  AS debit_sum,
                        Sum(debit_credit.credit) AS credit_sum
                FROM   (transaction_master
                        INNER JOIN voucher_setup
                                ON voucher_setup.voucher_id = transaction_master.voucher_id )
                        LEFT OUTER JOIN (debit_credit
                                        INNER JOIN ledger_head
                                                ON ledger_head.ledger_head_id =
                                                    debit_credit.ledger_head_id )
                                    ON ( debit_credit.tran_id = transaction_master.tran_id )
                WHERE  transaction_master.transaction_date BETWEEN :from_date AND :to_date
                        AND debit_credit.ledger_head_id =:ledger_id
                GROUP  BY transaction_master.tran_id
                ORDER  BY $sort_by

            ",
                ['from_date' => $from_date, 'to_date' => $to_date, 'ledger_id' => $request->ledger_id]
            );
            $op_party_ledger = DB::select(
                "SELECT debit_credit.ledger_head_id,group_chart.nature_group,ledger_head.ledger_name,transaction_master.transaction_date,
                          IF(group_chart.nature_group=1 OR group_chart.nature_group =3 , SUM(debit_credit.debit),'') AS op_total_debit1,
                          IF(group_chart.nature_group=1 OR group_chart.nature_group =3 , SUM(debit_credit.credit),'') AS op_total_credit1,
                          IF(group_chart.nature_group=2 OR group_chart.nature_group =4 , SUM(debit_credit.debit),'') AS op_total_debit2,
                          IF(group_chart.nature_group=2 OR group_chart.nature_group =4 , SUM(debit_credit.credit),'') AS op_total_credit2 FROM debit_credit
                        LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                        LEFT JOIN group_chart ON ledger_head.group_id=group_chart.group_chart_id
                        LEFT JOIN transaction_master ON debit_credit.tran_id=transaction_master.tran_id
                        WHERE  debit_credit.ledger_head_id =:ledger_id AND (transaction_master.transaction_date <:from_date ) GROUP by ledger_head.ledger_head_id
            ",
                ['from_date' => $from_date, 'ledger_id' => $request->ledger_id]
            );
            $group_chart_nature = DB::table('group_chart')->join('ledger_head', 'group_chart.group_chart_id', '=', 'ledger_head.group_id')->where('ledger_head.ledger_head_id', $request->ledger_id)->first(['ledger_head.opening_balance', 'group_chart.nature_group','ledger_head.DrCr']);

            //tran id  wise ledger
            $description_ledger = [];
            if ($request->description == 4) {
                if (array_filter($party_ledger)) {
                    $data = DB::table('debit_credit')
                        ->select(
                            'debit_credit.tran_id',
                            'debit_credit.ledger_head_id',
                            'ledger_head.ledger_name',
                            'debit_credit.debit',
                            'debit_credit.credit',
                            'debit_credit.dr_cr',
                            'debit_credit.remark'
                        )
                        ->leftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
                        ->whereIn('debit_credit.tran_id', array_column($party_ledger, 'tran_id'))
                        ->get();
                        //dd($data);
                    foreach ($data as $row) {
                        $description_ledger[$row->tran_id][] = $row;
                    }

                }
            } else if ($request->description == 5) {
                if (array_filter($party_ledger)) {
                    $data = DB::table('debit_credit')
                        ->select(
                            'debit_credit.tran_id',
                            'debit_credit.ledger_head_id',
                            'ledger_head.ledger_name',
                            DB::raw('SUM(debit_credit.debit) AS debit'),
                            DB::raw('SUM(debit_credit.credit) AS credit'),
                            'debit_credit.dr_cr',
                            'debit_credit.remark'
                        )
                        ->leftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
                        ->whereIn('debit_credit.tran_id', array_column($party_ledger, 'tran_id'))
                        ->groupBy('debit_credit.ledger_head_id', 'debit_credit.tran_id')
                        ->get();

                    foreach ($data as $row) {
                        $description_ledger[$row->tran_id][] = $row;
                    }

                }
            }



            // tran id  wise product
            $description_stock_in = [];
            $description_stock_out = [];
            if ($request->description == 5) {
                if (array_filter($party_ledger)) {
                    $data_stock_in = DB::table('stock_item')
                        ->select(
                            'stock_item.product_name',
                            'stock_in.tran_id AS stock_in_tran_id',
                            'stock_in.qty',
                            'stock_in.rate',
                            'stock_in.total',
                            'unitsof_measure.symbol',
                            'stock_in.remark'
                        )
                        ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
                        ->leftJoin('stock_in', 'stock_item.stock_item_id', '=', 'stock_in.stock_item_id')
                        ->whereIn('stock_in.tran_id', array_column($party_ledger, 'tran_id'))
                        ->get();
                    foreach ($data_stock_in as $row) {
                        $description_stock_in[$row->stock_in_tran_id][] = $row;
                    }

                    $data_stock_out = DB::table('stock_item')
                        ->select(
                            'stock_item.product_name',
                            'stock_out.tran_id AS stock_out_tran_id',
                            'stock_out.qty',
                            'stock_out.rate',
                            'stock_out.total',
                            'unitsof_measure.symbol',
                            'stock_out.remark'
                        )
                        ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
                        ->leftJoin('stock_out', 'stock_item.stock_item_id', '=', 'stock_out.stock_item_id')
                        ->whereIn('stock_out.tran_id', array_column($party_ledger,'tran_id'))
                        ->get();
                    foreach ($data_stock_out as $row) {
                        $description_stock_out[$row->stock_out_tran_id][] = $row;
                    }
                }
            }

            return $data = [
                'group_chart_nature' => $group_chart_nature,
                'party_ledger' => $party_ledger,
                'op_party_ledger' => $op_party_ledger,
                'description_ledger' => $description_ledger,
                'description_stock_in' => $description_stock_in,
                'description_stock_out' => $description_stock_out
            ];
        }
    }
}
