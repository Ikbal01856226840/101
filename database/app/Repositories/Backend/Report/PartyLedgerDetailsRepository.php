<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class PartyLedgerDetailsRepository implements PartyLedgerDetailsInterface
{
    public function PartyLedgerInDetails($request = null)
    {

        if ($request->sort_by != 1) {
            $sort_by = ($request->sort_by == 2 ? 'debit_credit.debit DESC' : ($request->sort_by == 3 ? 'debit_credit.credit DESC' : ($request->sort_by == 4 ? ' dr_cr DESC' : '')));
        } else {
            $sort_by = 'transaction_master.tran_id ASC';
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
        if ($request->ledger_id == 0) {

            return DB::select("   SELECT  group_chart.group_chart_id,
                                        group_chart.nature_group,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        ledger_head.alias,
                                        ledger_head.opening_balance,
                                        t1.total_debit,
                                        t1.total_credit,
                                        op.total_debit AS op_group_debit,
                                        op.total_credit AS op_group_credit
                                FROM      group_chart
                                INNER JOIN ledger_head
                                ON        group_chart.group_chart_id=ledger_head.group_id
                                LEFT JOIN
                                            (
                                                    SELECT    debit_credit.ledger_head_id,
                                                                sum(debit_credit.debit)  AS total_debit,
                                                                sum(debit_credit.credit) AS total_credit
                                                    FROM      debit_credit
                                                    INNER JOIN transaction_master
                                                    ON        debit_credit.tran_id=transaction_master.tran_id
                                                    WHERE     transaction_master.transaction_date BETWEEN '$from_date' AND       '$to_date'
                                                    GROUP BY  debit_credit.ledger_head_id) AS t1
                                ON        ledger_head.ledger_head_id=t1.ledger_head_id
                                LEFT JOIN
                                            (
                                                    SELECT    debit_credit.ledger_head_id,
                                                                sum(debit_credit.debit)  AS total_debit,
                                                                sum(debit_credit.credit) AS total_credit
                                                    FROM      debit_credit
                                                    INNER JOIN transaction_master
                                                    ON        debit_credit.tran_id=transaction_master.tran_id
                                                    WHERE     transaction_master.transaction_date <'$from_date'
                                                    GROUP BY  debit_credit.ledger_head_id) AS op
                                ON        ledger_head.ledger_head_id=op.ledger_head_id
                                    ");
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
                        debit_credit.dr_cr,
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
                WHERE  transaction_master.transaction_date BETWEEN '$from_date' AND '$to_date'
                        AND debit_credit.ledger_head_id =$request->ledger_id
                GROUP  BY transaction_master.tran_id
                ORDER  BY $sort_by

            "
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
                        WHERE $ledger_id (transaction_master.transaction_date <'$from_date' ) GROUP by ledger_head.ledger_head_id
            "
            );
            $group_chart_nature = DB::table('group_chart')->join('ledger_head', 'group_chart.group_chart_id', '=', 'ledger_head.group_id')->where('ledger_head.ledger_head_id', $request->ledger_id)->first(['ledger_head.opening_balance', 'group_chart.nature_group']);
            //tran id  wise ledger
            if (($request->description == 4) || ($request->description == 5)) {
                if (array_filter($party_ledger)) {
                    foreach ($party_ledger as $op_party_ledger_tran_id) {
                        $description_ledger[] = DB::select(
                            "SELECT debit_credit.tran_id,debit_credit.ledger_head_id,ledger_head.ledger_name,debit_credit.debit,debit_credit.credit,debit_credit.dr_cr FROM debit_credit
                                    LEFT JOIN ledger_head ON debit_credit.ledger_head_id=ledger_head.ledger_head_id
                                    WHERE  debit_credit.tran_id=$op_party_ledger_tran_id->tran_id
                        "
                        );
                    }
                } else {
                    $description_ledger[] = '';
                }
            } else {
                $description_ledger[] = '';
            }

            // tran id  wise product
            if ($request->description == 5) {
                if (array_filter($party_ledger)) {
                    foreach ($party_ledger as $op_party_ledger_tran_id_in) {
                        if (($op_party_ledger_tran_id_in->voucher_type_id == 10) || ($op_party_ledger_tran_id_in->voucher_type_id == 24) || ($op_party_ledger_tran_id_in->voucher_type_id == 29) || ($op_party_ledger_tran_id_in->voucher_type_id == 22) || ($op_party_ledger_tran_id_in->voucher_type_id == 21) || ($op_party_ledger_tran_id_in->voucher_type_id == 6)) {
                            $description_stock_in[] = DB::select(
                                "SELECT stock_item.product_name,stock_in.tran_id AS stock_in_tran_id,stock_in.qty,stock_in.rate,stock_in.total,unitsof_measure.symbol FROM stock_item
                                        LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                        LEFT JOIN  stock_in ON stock_item.stock_item_id=stock_in.stock_item_id WHERE stock_in.tran_id=$op_party_ledger_tran_id_in->tran_id;
                            "
                            );
                        } else {
                            $description_stock_in[] = '';
                        }

                        if (($op_party_ledger_tran_id_in->voucher_type_id == 19) || ($op_party_ledger_tran_id_in->voucher_type_id == 23) || ($op_party_ledger_tran_id_in->voucher_type_id == 22) || ($op_party_ledger_tran_id_in->voucher_type_id == 21) || ($op_party_ledger_tran_id_in->voucher_type_id == 25 || ($op_party_ledger_tran_id_in->voucher_type_id == 6))) {
                            $description_stock_out[] = DB::select(
                                "SELECT stock_item.product_name,stock_out.tran_id AS stock_out_tran_id,stock_out.qty,stock_out.rate,stock_out.total,unitsof_measure.symbol  FROM stock_item
                                LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                LEFT JOIN stock_out on stock_item.stock_item_id=stock_out.stock_item_id WHERE stock_out.tran_id=$op_party_ledger_tran_id_in->tran_id
                            "
                            );
                        } else {
                            $description_stock_out[] = '';
                        }
                    }
                } else {
                    $description_stock_in[] = '';
                    $description_stock_out[] = '';
                }
            } else {
                $description_stock_in[] = '';
                $description_stock_out[] = '';
            }

            return $data = ['group_chart_nature' => $group_chart_nature, 'party_ledger' => $party_ledger, 'op_party_ledger' => $op_party_ledger, 'description_ledger' => $description_ledger, 'description_stock_in' => $description_stock_in, 'description_stock_out' => $description_stock_out];
        }
    }
}
