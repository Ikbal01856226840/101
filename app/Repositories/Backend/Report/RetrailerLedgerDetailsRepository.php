<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class RetrailerLedgerDetailsRepository implements RetrailerLedgerDetailsInterface
{
    public function RetrailerLedgerDetailsOfIndex($request = null)
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

            $data_tree_group = DB::select("WITH recursive tree AS
                                            (
                                                            SELECT ledger_head.ledger_head_id
                                                            FROM   ledger_head
                                                            WHERE  find_in_set(ledger_head.ledger_head_id ,:ledger_id)
                                                            UNION ALL
                                                            SELECT e.ledger_head_id
                                                            FROM   tree h
                                                            JOIN   ledger_head e
                                                            ON     h.ledger_head_id =e.under_ledger_id )
                                                SELECT *
                                          FROM   tree",['ledger_id'=>$request->ledger_id]);

            // ledger_type one all ledger and two dealer and three retrailer
            if($request->ledger_type == 1){
                 $group_id = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'ledger_head_id'));
            }else if($request->ledger_type == 2){
                 $group_id =$request->ledger_id;
            }else if($request->ledger_type == 3){
                 unset($data_tree_group[array_search($request->ledger_id,array_column(json_decode(json_encode($data_tree_group, true), true),'ledger_head_id'))]); // Remove the element
                 $group_id = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'ledger_head_id'));
                 if($group_id){
                    $group_id=$group_id;
                 }else{
                    $group_id=$request->ledger_id;
                 }
            }

            $ledger_id = "debit_credit.ledger_head_id IN($group_id) AND";

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
                        ledger_head.ledger_type,
                        ledger_head.drcr,
                        voucher_setup.voucher_name,
                        debit_credit.debit,
                        debit_credit.credit,
                        debit_credit.dr_cr,
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
                WHERE $ledger_id  transaction_master.transaction_date BETWEEN :from_date AND :to_date

                GROUP  BY transaction_master.tran_id
                ORDER  BY $sort_by

            ",
                ['from_date' => $from_date, 'to_date' => $to_date]
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
                        WHERE debit_credit.ledger_head_id IN($group_id) AND (transaction_master.transaction_date <:from_date )
            ",
                ['from_date' => $from_date]
            );

                $op_daeler_ledger = DB::select(
                    "SELECT debit_credit.ledger_head_id,group_chart.nature_group,ledger_head.ledger_name,transaction_master.transaction_date,
                              IF(group_chart.nature_group=1 OR group_chart.nature_group =3 , SUM(debit_credit.debit),'') AS op_total_debit1,
                              IF(group_chart.nature_group=1 OR group_chart.nature_group =3 , SUM(debit_credit.credit),'') AS op_total_credit1,
                              IF(group_chart.nature_group=2 OR group_chart.nature_group =4 , SUM(debit_credit.debit),'') AS op_total_debit2,
                              IF(group_chart.nature_group=2 OR group_chart.nature_group =4 , SUM(debit_credit.credit),'') AS op_total_credit2 FROM debit_credit
                            LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                            LEFT JOIN group_chart ON ledger_head.group_id=group_chart.group_chart_id
                            LEFT JOIN transaction_master ON debit_credit.tran_id=transaction_master.tran_id
                            WHERE debit_credit.ledger_head_id=:ledger_id AND (transaction_master.transaction_date <:from_date )
                ",
                    ['from_date' => $from_date,'ledger_id'=>$request->ledger_id]
                );



                unset($data_tree_group[array_search($request->ledger_id,array_column(json_decode(json_encode($data_tree_group, true), true),'ledger_head_id'))]); // Remove the element
                 $group_id = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'ledger_head_id'));
                if($group_id){
                  $retrailer_group= $group_id;
                }else{
                    $retrailer_group= $request->ledger_id;
                }
                $op_reatrailer_ledger = DB::select(
                    "SELECT debit_credit.ledger_head_id,group_chart.nature_group,ledger_head.ledger_name,transaction_master.transaction_date,
                              IF(group_chart.nature_group=1 OR group_chart.nature_group =3 , SUM(debit_credit.debit),'') AS op_total_debit1,
                              IF(group_chart.nature_group=1 OR group_chart.nature_group =3 , SUM(debit_credit.credit),'') AS op_total_credit1,
                              IF(group_chart.nature_group=2 OR group_chart.nature_group =4 , SUM(debit_credit.debit),'') AS op_total_debit2,
                              IF(group_chart.nature_group=2 OR group_chart.nature_group =4 , SUM(debit_credit.credit),'') AS op_total_credit2 FROM debit_credit
                            LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                            LEFT JOIN group_chart ON ledger_head.group_id=group_chart.group_chart_id
                            LEFT JOIN transaction_master ON debit_credit.tran_id=transaction_master.tran_id
                            WHERE debit_credit.ledger_head_id IN($retrailer_group) AND (transaction_master.transaction_date <:from_date )
                ",
                    ['from_date' => $from_date]
                );


            $group_chart_nature = DB::table('group_chart')->join('ledger_head', 'group_chart.group_chart_id', '=', 'ledger_head.group_id')->where('ledger_head.ledger_head_id', $request->ledger_id)->first(['ledger_head.opening_balance', 'group_chart.nature_group']);

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
                            'debit_credit.dr_cr'
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
                            'debit_credit.dr_cr'
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
                            'unitsof_measure.symbol'
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
                            'unitsof_measure.symbol'
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
                'op_daeler_ledger' => $op_daeler_ledger,
                'op_reatrailer_ledger' => $op_reatrailer_ledger,
                'op_party_ledger'=>$op_party_ledger,
                'description_ledger' => $description_ledger,
                'description_stock_in' => $description_stock_in,
                'description_stock_out' => $description_stock_out
            ];
        }
}
