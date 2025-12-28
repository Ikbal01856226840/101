<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class AccountLedgerAnalysisWithSaleTypeRepository implements AccountLedgerAnalysisWithSaleTypeInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getAccountLedgerAnalysisWithSaleTypeOfIndex($request = null)
    {
        $params=[];
        $ledger_sql_1 ='';
        $ledger_sql_2 ='';
        $ledger_sql_3 ='';
        $ledger_sql_4 ='';
        $debit_credit_sql='';
        $godown_crdit='';
        $godown_out ='';
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

           if(!empty($request->ledger_id)){
               $debit_credit_sql="LEFT JOIN debit_credit ON transaction_master.tran_id=debit_credit.tran_id   LEFT JOIN ledger_head  ON  ledger_head.ledger_head_id = debit_credit.ledger_head_id";
               $ledger_sql_1 = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=:ledger_head_id_sales_cash AND";
               $params['ledger_head_id_sales_cash']=$request->ledger_id;
               $ledger_sql_2 = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=:ledger_head_id_sales_credit AND";
               $params['ledger_head_id_sales_credit']=$request->ledger_id;
               $ledger_sql_3 = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=:ledger_head_id_sales_inter AND";
               $params['ledger_head_id_sales_inter']=$request->ledger_id;
               $ledger_sql_4 = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=:ledger_head_id_sales_return AND";
               $params['ledger_head_id_sales_return']=$request->ledger_id;
           }
           if(!empty($request->stock_item_id)){
           
            $ledger_sql_1 = "debit_credit.ledger_head_id=:ledger_head_id_sales_cash AND";
            $params['ledger_head_id_sales_cash']=$request->ledger_id;
            $ledger_sql_2 = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=:ledger_head_id_sales_credit AND";
            $params['ledger_head_id_sales_credit']=$request->ledger_id;
            $ledger_sql_3 = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=:ledger_head_id_sales_inter AND";
            $params['ledger_head_id_sales_inter']=$request->ledger_id;
            $ledger_sql_4 = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id=:ledger_head_id_sales_return AND";
            $params['ledger_head_id_sales_return']=$request->ledger_id;
        }
           // sales credit godown
           if (!empty($request->godown_id[0])) {
                
                $godown_credit_id=$request->godown_id; // Array of IDs
                $placeholders_in = implode(',', array_map(function($i) { return ':godown_credit_id_'.$i; }, array_keys($godown_credit_id)));

                $godown_credit_sql = "stock_out.godown_id IN ($placeholders_in) AND";
            
                foreach ($godown_credit_id as $i => $id) {
                    $params[':godown_credit_id_'.$i] = $id;
                }
            } else {
                $godown_credit_sql = "";
            }

         // sales credit godown
        if (!empty($request->godown_id[0])) {
            $godown_cash_id=$request->godown_id; // Array of IDs
            $placeholders_out = implode(',', array_map(function($i) { return ':godown_cash_id_'.$i; }, array_keys($godown_cash_id)));

            $godown_cash_sql = "stock_out.godown_id IN ($placeholders_out) AND";
        
            foreach ($godown_cash_id as $i => $id) {
                $params['godown_cash_id_'.$i] = $id;
            }
        } else {
            $godown_cash_sql = "";
        }

         // sales inter company godown
         if (!empty($request->godown_id[0])) {
                $godown_inter_company_id=$request->godown_id; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':godown_inter_company_id_'.$i; }, array_keys($godown_inter_company_id)));

                $godown_inter_company_sql = "stock_out.godown_id IN ($placeholders_out) AND";
            
                foreach ($godown_inter_company_id as $i => $id) {
                    $params['godown_inter_company_id_'.$i] = $id;
                }
            } else {
                $godown_inter_company_sql = "";
            }

             // sales return godown
            if (!empty($request->godown_id[0])) {
                $godown_return_id=$request->godown_id; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':godown_return_id_'.$i; }, array_keys($godown_return_id)));

                $godown_return_sql = "stock_out.godown_id IN ($placeholders_out) AND";
            
                foreach ($godown_return_id as $i => $id) {
                    $params['godown_return_id_'.$i] = $id;
                }
            } else {
                $godown_return_sql = "";
            }

            $stock_group_id = explode('-', $request->stock_group_id, 2);
            if ($stock_group_id[0] == 0) {
                $inner_join_item_in = '';
                $stock_group_in = '';
                $group_id = '';
            } else {
                $data_tree_group = DB::select("with recursive tree as(
                                                SELECT stock_group.stock_group_id FROM stock_group  WHERE FIND_IN_SET(stock_group.stock_group_id,:stock_group_id)
                                                UNION ALL
                                                SELECT E.stock_group_id FROM tree H JOIN stock_group E ON H.stock_group_id=E.under
                                            )SELECT * FROM tree",['stock_group_id'=>$stock_group_id[0]]);
                $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
                $inner_join_item_in = 'LEFT JOIN stock_item ON stock_out.stock_item_id=stock_item.stock_item_id';
                $stock_group_in = "stock_item.stock_group_id IN($string_tree_group) AND";

            }

        }

          $query = "SELECT     stock_group.stock_group_id,
                               stock_group.stock_group_name,
                               stock_group.under,
                               stock_item.stock_item_id,
                               unitsof_measure.symbol,
                               stock_item.product_name,
                               t.stock_qty_sales_cash,
                               t.stock_qty_sales_cash AS stock_qty_sales_cash_total,
                               t.stock_total_sales_cash,
                               t.stock_total_sales_cash AS stock_total_sales_cash_value,
                               t1.stock_qty_sales_credit,
                               t1.stock_qty_sales_credit AS stock_qty_sales_credit_total,
                               t1.stock_total_sales_credit,
                               t1.stock_total_sales_credit AS stock_total_sales_credit_value,
                               t2.stock_qty_inter_company_sales,
                               t2.stock_qty_inter_company_sales AS stock_qty_inter_company_sales_total,
                               t2.stock_total_inter_company_sales,
                               t2.stock_total_inter_company_sales AS stock_total_inter_company_sales_value,
                               t3.stock_qty_sales_return,
                               t3.stock_qty_sales_return AS stock_qty_sales_return_total,
                               t3.stock_total_sales_return,
                               t3.stock_total_sales_return AS stock_total_sales_return_value
                    FROM      stock_group
                    LEFT JOIN stock_item
                    ON        stock_group.stock_group_id=stock_item.stock_group_id
                    LEFT JOIN
                                (
                                        SELECT     Sum(stock_out.qty)      AS stock_qty_sales_cash,
                                                   Sum(stock_out.total)    AS stock_total_sales_cash,
                                                   stock_out.stock_item_id AS product_id_sales_cash
                                        FROM       transaction_master
                                        LEFT JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        $inner_join_item_in
                                        LEFT JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE   $godown_cash_sql transaction_master.sale_type=1 AND $stock_group_in voucher_setup.voucher_type_id=19 AND $ledger_sql_1  transaction_master.transaction_date BETWEEN :from_date_sales_cash AND :to_date_sales_cash
                                        GROUP BY   stock_out.stock_item_id) AS t
                    ON        stock_item.stock_item_id=t.product_id_sales_cash
                    LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)       AS stock_qty_sales_credit,
                                                   sum(stock_out.total)     AS stock_total_sales_credit,
                                                    stock_out.stock_item_id AS product_id_sales_credit
                                        FROM       transaction_master
                                        LEFT JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        $inner_join_item_in
                                        LEFT JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE  $godown_credit_sql   transaction_master.sale_type=2 AND  $stock_group_in voucher_setup.voucher_type_id=19 AND $ledger_sql_2  transaction_master.transaction_date BETWEEN :from_date_sales_credit AND :to_date_sales_credit
                                        GROUP BY   stock_out.stock_item_id) AS t1
                    ON        stock_item.stock_item_id=t1.product_id_sales_credit
                    LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)       AS stock_qty_inter_company_sales,
                                                   sum(stock_out.total)     AS stock_total_inter_company_sales,
                                                    stock_out.stock_item_id AS product_id_inter_company_sales
                                        FROM       transaction_master
                                        LEFT JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        $inner_join_item_in
                                        LEFT JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE  $godown_inter_company_sql   transaction_master.sale_type=3 AND  $stock_group_in voucher_setup.voucher_type_id=19 AND  $ledger_sql_3 transaction_master.transaction_date BETWEEN :from_date_inter AND :to_date_inter
                                        GROUP BY   stock_out.stock_item_id) AS t2
                    ON        stock_item.stock_item_id=t2.product_id_inter_company_sales
                    LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)       AS stock_qty_sales_return,
                                                   sum(stock_out.total)     AS stock_total_sales_return,
                                                    stock_out.stock_item_id AS product_id_sales_return
                                        FROM       transaction_master
                                        LEFT JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        $inner_join_item_in
                                        LEFT JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE   $godown_return_sql  voucher_setup.voucher_type_id=25 AND  $stock_group_in  $ledger_sql_4 transaction_master.transaction_date BETWEEN :from_date_sales_return AND :to_date_sales_return
                                        GROUP BY   stock_out.stock_item_id) AS t3
                    ON        stock_item.stock_item_id=t3.product_id_sales_return
                    LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                    ORDER BY  stock_group.stock_group_name DESC,stock_item.product_name DESC
                    ";
         $params['from_date_sales_cash'] = $from_date;
         $params['to_date_sales_cash'] = $to_date;
         $params['from_date_sales_credit'] = $from_date;
         $params['to_date_sales_credit'] = $to_date;
         $params['from_date_inter'] = $from_date;
         $params['to_date_inter'] = $to_date;
         $params['from_date_sales_return'] = $from_date;
         $params['to_date_sales_return'] = $to_date;

        $data =DB::select($query,$params);

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');

        return $this->calculateGroupTotals($tree_data);
    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_sales_cash'] = array_sum(array_column($obj['children'], 'stock_qty_sales_cash')) + $obj['stock_qty_sales_cash'] ?? 0;
                $obj['stock_qty_sales_credit'] = array_sum(array_column($obj['children'], 'stock_qty_sales_credit')) + $obj['stock_qty_sales_credit'] ?? 0;
                $obj['stock_qty_inter_company_sales'] = array_sum(array_column($obj['children'], 'stock_qty_inter_company_sales')) + $obj['stock_qty_inter_company_sales'] ?? 0;
                $obj['stock_qty_sales_return'] = array_sum(array_column($obj['children'], 'stock_qty_sales_return')) + $obj['stock_qty_sales_return'] ?? 0;
                $obj['stock_total_sales_cash'] = array_sum(array_column($obj['children'], 'stock_total_sales_cash')) + $obj['stock_total_sales_cash'] ?? 0;
                $obj['stock_total_sales_credit'] = array_sum(array_column($obj['children'], 'stock_total_sales_credit')) + $obj['stock_total_sales_credit'] ?? 0;
                $obj['stock_total_inter_company_sales'] = array_sum(array_column($obj['children'], 'stock_total_inter_company_sales')) + $obj['stock_total_inter_company_sales'] ?? 0;
                $obj['stock_total_sales_return'] = array_sum(array_column($obj['children'], 'stock_total_sales_return')) + $obj['stock_total_sales_return'] ?? 0;
            }
        }

        return $arr;
    }
}
