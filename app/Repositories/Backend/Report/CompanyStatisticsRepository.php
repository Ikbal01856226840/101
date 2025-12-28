<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class CompanyStatisticsRepository implements CompanyStatisticsInterface
{
    public function getCompanyStatisticsOfIndex($request)
    {
        $params_voucher= [];
        $params_accounts_ledger= [];
        $params_accounts_group= [];
        $params_voucher_type= [];
        $params_stock_group= [];
        $params_stock_item=[];
        $params_godown=[];
        $params_unitsof_measure=[];
        $from_date = $request->from_date;
        $to_date = $request->to_date;


            $query_voucher="SELECT v.tran_id_count,
                                   voucher_setup.voucher_name,
                                   voucher_setup.voucher_id
                            FROM   voucher_setup
                                            LEFT JOIN (
                                        SELECT  count(transaction_master.tran_id) AS tran_id_count,
                                                    transaction_master.transaction_date,
                                                    transaction_master.voucher_id

                                            FROM             voucher_setup
                                            INNER JOIN      transaction_master
                                            ON              voucher_setup.voucher_id=transaction_master.voucher_id
                                            WHERE           transaction_master.transaction_date BETWEEN :from_date AND             :to_date
                                            GROUP BY        transaction_master.voucher_id) as v
                                ON v.voucher_id=voucher_setup.voucher_id";

                  $params_voucher['from_date'] = $from_date;
                  $params_voucher['to_date'] = $to_date;
            $voucher= DB::select($query_voucher,$params_voucher);

             $query_accounts_ledgeer="      SELECT count(ledger_head.ledger_head_id) AS ledger_head_id_count
                                            FROM  ledger_head
                                            INNER JOIN( SELECT          debit_credit.ledger_head_id
                                                        FROM            transaction_master
                                                        INNER JOIN      debit_credit
                                                        ON              debit_credit.tran_id=transaction_master.tran_id
                                                        WHERE           transaction_master.transaction_date BETWEEN :from_date_1 AND             :to_date_1
                                                        GROUP BY        debit_credit.ledger_head_id  ) AS  l
                                            ON  l.ledger_head_id=ledger_head.ledger_head_id
                                                        ";

            $params_accounts_ledger['from_date_1'] = $from_date;
            $params_accounts_ledger['to_date_1'] = $to_date;
            $accounts_ledger= DB::select($query_accounts_ledgeer,$params_accounts_ledger);

            $query_accounts_group=" SELECT COUNT(g.group_id) AS group_id_count
                                    FROM group_chart
                                    INNER JOIN(
                                            SELECT          ledger_head.group_id
                                            FROM            transaction_master
                                            INNER JOIN      debit_credit
                                            ON              debit_credit.tran_id=transaction_master.tran_id
                                            INNER JOIN      ledger_head
                                            ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                            WHERE           transaction_master.transaction_date BETWEEN :from_date_5 AND             :to_date_5
                                            GROUP BY        ledger_head.group_id) AS g
                                    ON  group_chart.group_chart_id=g.group_id
                                    ";

            $params_accounts_group['from_date_5'] = $from_date;
            $params_accounts_group['to_date_5'] = $to_date;
            $accounts_group= DB::select($query_accounts_group,$params_accounts_group);

            $query_voucher_type="SELECT COUNT(voucher_setup.voucher_id) AS voucher_type_id_count
                                 FROM   voucher_setup
                                 INNER JOIN (
                                    SELECT          transaction_master.voucher_id
                                    FROM            transaction_master
                                    WHERE           transaction_master.transaction_date BETWEEN :from_date_3 AND             :to_date_3
                                    GROUP BY        transaction_master.voucher_id  ) AS vt
                                ON  vt.voucher_id=voucher_setup.voucher_id
                                    ";

                $params_voucher_type['from_date_3'] = $from_date;
                $params_voucher_type['to_date_3'] = $to_date;
            $voucher_type= DB::select($query_voucher_type,$params_voucher_type);

            $query_stock_group="SELECT  count(stock_group.stock_group_id) AS  stock_group_id_count
                                FROM    stock_group
                                    INNER JOIN(
                                        SELECT          stock_item.stock_group_id
                                        FROM            stock
                                        INNER JOIN      stock_item
                                        ON              stock.stock_item_id=stock_item.stock_item_id
                                        WHERE           stock.tran_date BETWEEN :from_date_4 AND             :to_date_4
                                       GROUP BY        stock_item.stock_group_id
                                    ) AS sg
                                ON  sg.stock_group_id=stock_group.stock_group_id
                                    ";


                $params_stock_group['from_date_4'] = $from_date;
                $params_stock_group['to_date_4'] = $to_date;
            $stock_group= DB::select($query_stock_group,$params_stock_group);



            $query_stock_item="SELECT  count(stock_item.stock_item_id) AS  stock_item_id_count
                                FROM    stock_item
                                    INNER JOIN(
                                        SELECT          stock.stock_item_id
                                        FROM            stock
                                        WHERE           stock.tran_date BETWEEN :from_date_7 AND             :to_date_7
                                       GROUP BY         stock.stock_item_id) AS i
                                ON  i.stock_item_id=stock_item.stock_item_id
                                    ";


                $params_stock_item['from_date_7'] = $from_date;
                $params_stock_item['to_date_7'] = $to_date;
            $stock_item= DB::select($query_stock_item,$params_stock_item);

            $query_stock_godowns="SELECT  count(godowns.godown_id) AS  godown_id_count
                                    FROM    godowns
                                        INNER JOIN(
                                            SELECT          stock.godown_id
                                            FROM            stock
                                            WHERE           stock.tran_date BETWEEN :from_date_8 AND             :to_date_8
                                            GROUP BY        stock.godown_id) AS gd
                                    ON  godowns.godown_id=gd.godown_id
                                    ";
            $params_godown['from_date_8'] = $from_date;
            $params_godown['to_date_8'] = $to_date;
            $stock_godowns= DB::select($query_stock_godowns,$params_godown);


            $query_unitsof_measure="SELECT  count(	unitsof_measure.unit_of_measure_id) AS  unit_of_measure_id_count
                                FROM    	unitsof_measure
                                    INNER JOIN(
                                        SELECT         stock_item.unit_of_measure_id
                                        FROM            stock
                                        INNER JOIN      stock_item
                                        ON              stock.stock_item_id=stock_item.stock_item_id
                                        WHERE           stock.tran_date BETWEEN :from_date_6 AND             :to_date_6
                                        GROUP BY        stock_item.unit_of_measure_id) AS mg
                                ON  mg.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                    ";


                $params_unitsof_measure['from_date_6'] = $from_date;
                $params_unitsof_measure['to_date_6'] = $to_date;
            $unitsof_measure= DB::select($query_unitsof_measure,$params_unitsof_measure);

           return ['voucher'=>$voucher,'accounts_ledger'=>$accounts_ledger,'accounts_group'=>$accounts_group,'voucher_type'=>$voucher_type,'stock_group'=>$stock_group,'stock_item'=> $stock_item,'stock_godowns'=>$stock_godowns,'unitsof_measure'=>$unitsof_measure];


    }
}
