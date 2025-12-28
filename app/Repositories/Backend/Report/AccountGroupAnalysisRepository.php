<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class AccountGroupAnalysisRepository implements AccountGroupAnalysisInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getAccountGroupAnalysisOfIndex($request = null)
    {
        $params=[];
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $group_chart = explode('-', $request->group_id, 2);
            
                // Prepare voucher_id_in
            if (!empty($request->in_qty)) {
                $voucher_id_in = $request->in_qty; // Array of IDs
                $placeholders_in = implode(',', array_map(function($i) { return ':voucher_id_in_'.$i; }, array_keys($voucher_id_in)));

                $voucher_id_in_sql = "AND voucher_setup.voucher_type_id IN ($placeholders_in)";
            
                foreach ($voucher_id_in as $i => $id) {
                    $params['voucher_id_in_'.$i] = $id;
                }
            } else {
                $voucher_id_in_sql = "AND voucher_setup.voucher_type_id IN (0)";
            }
            // Prepare voucher_id_out
            if (!empty($request->out_qty)) {
                $voucher_id_out = $request->out_qty; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':voucher_id_out_'.$i; }, array_keys($voucher_id_out)));
                $voucher_id_out_sql = "AND voucher_setup.voucher_type_id IN ($placeholders_out)";
                foreach ($voucher_id_out as $i => $id) {
                    $params['voucher_id_out_'.$i] = $id;
                }
            } else {
                $voucher_id_out_sql = "AND voucher_setup.voucher_type_id IN (0)";
            }
           
            $data_tree_group = DB::select("with recursive tree as(
                                            SELECT group_chart.group_chart_id FROM group_chart  WHERE FIND_IN_SET(group_chart.group_chart_id,:group_chart)
                                            UNION ALL
                                            SELECT E.group_chart_id FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
                                        )SELECT * FROM tree",['group_chart' => $group_chart[0]]);
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'group_chart_id'));

           $purchase_return_sql="";
           $sales_return_sql="";
           $include_purchse_return="";
           $include_sales_return_sql="";
           if(isset($request->purchase_return_in)){
                $purchase_return_sql=" LEFT JOIN
                                            (
                                                    SELECT     sum(stock_in.qty)       AS stock_qty_purchase_return,
                                                               sum(stock_in.total)     AS stock_total_purchase_return,
                                                                stock_in.stock_item_id AS product_purchase_return_id
                                                    FROM       transaction_master
                                                    LEFT JOIN `stock_in`
                                                    ON         transaction_master.tran_id=stock_in.tran_id
                                                    LEFT JOIN voucher_setup
                                                    ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                                    LEFT JOIN debit_credit
                                                    ON         transaction_master.tran_id=debit_credit.tran_id
                                                    LEFT JOIN ledger_head
                                                    ON         ledger_head.ledger_head_id = debit_credit.ledger_head_id
                                                    WHERE     ledger_head.group_id IN($string_tree_group) AND voucher_setup.voucher_type_id=29  AND transaction_master.transaction_date BETWEEN :from_date_purchase_return AND        :to_date_purchase_return
                                                    GROUP BY   stock_in.stock_item_id) AS purchase_return
                                ON        stock_item.stock_item_id=purchase_return.product_purchase_return_id";

                                $params['from_date_purchase_return'] = $from_date;
                                $params['to_date_purchase_return'] = $to_date;

                $include_purchse_return="
                                        ABS(Coalesce(t.stock_qty_sales,0)+Coalesce(ABS( purchase_return.stock_qty_purchase_return),0)) AS stock_qty_sales,
                                        ABS(Coalesce(t.stock_qty_sales,0)+Coalesce(ABS( purchase_return.stock_qty_purchase_return),0)) AS stock_qty_sales_total,
                                        ABS(Coalesce(t.stock_total_sales,0)+Coalesce(ABS( purchase_return.stock_total_purchase_return),0)) AS stock_total_sales,
                                        ABS(Coalesce(t.stock_total_sales,0)+Coalesce(ABS( purchase_return.stock_total_purchase_return),0)) AS stock_total_sales_value,
                ";
            }else{
                $include_purchse_return="
                        t.stock_qty_sales,
                        t.stock_qty_sales AS stock_qty_sales_total,
                        t.stock_total_sales,
                        t.stock_total_sales AS stock_total_sales_value,
                ";
            }
           

            if(isset($request->sales_return_out)){
                $sales_return_sql=" LEFT JOIN
                                        (
                                                SELECT     Sum(stock_out.qty)      AS stock_qty_sales_return,
                                                        Sum(stock_out.total)    AS stock_total_sales_return,
                                                        stock_out.stock_item_id AS product_sales_return_id
                                                FROM       transaction_master
                                                LEFT JOIN `stock_out`
                                                ON         transaction_master.tran_id=stock_out.tran_id
                                                LEFT JOIN voucher_setup
                                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                                LEFT JOIN debit_credit
                                                ON         transaction_master.tran_id=debit_credit.tran_id
                                                LEFT JOIN ledger_head
                                                        ON ledger_head.ledger_head_id = debit_credit.ledger_head_id
                                                WHERE   ledger_head.group_id IN($string_tree_group) AND voucher_setup.voucher_type_id=25  AND transaction_master.transaction_date BETWEEN :from_date_sales_return AND        :to_date_sales_return
                                                GROUP BY   stock_out.stock_item_id) AS sales_return
                            ON        stock_item.stock_item_id=sales_return.product_sales_return_id";

                            $params['from_date_sales_return'] = $from_date;
                            $params['to_date_sales_return'] = $to_date;

                $include_sales_return_sql="
                                    ABS(Coalesce(t1.stock_qty_purchase,0)+Coalesce(ABS(sales_return.stock_qty_sales_return),0)) AS stock_qty_purchase,
                                    ABS(Coalesce(t1.stock_qty_purchase,0)+Coalesce(ABS(sales_return.stock_qty_sales_return),0)) AS stock_qty_purchase_total,
                                    ABS(Coalesce(t1.stock_total_purchase,0)+Coalesce(ABS(sales_return.stock_total_sales_return),0)) AS stock_total_purchase,
                                    ABS(Coalesce(t1.stock_total_purchase,0)+Coalesce(ABS(sales_return.stock_total_sales_return),0)) AS stock_total_purchase_value
                ";
               
         }else{

            $include_sales_return_sql="
                    t1.stock_qty_purchase,
                    t1.stock_qty_purchase AS stock_qty_purchase_total,
                    t1.stock_total_purchase,
                    t1.stock_total_purchase AS stock_total_purchase_value
           ";
         }
        
         

        

        }
        $query="SELECT         stock_group.stock_group_id,
                               stock_group.stock_group_name,
                               stock_group.under,
                               stock_item.stock_item_id,
                               stock_item.product_name,
                               unitsof_measure.symbol,
                                $include_purchse_return
                                $include_sales_return_sql
                    FROM      stock_group
                    LEFT JOIN stock_item
                    ON        stock_group.stock_group_id=stock_item.stock_group_id
                    LEFT JOIN
                                (
                                        SELECT     Sum(stock_out.qty)      AS stock_qty_sales,
                                                   Sum(stock_out.total)    AS stock_total_sales,
                                                   stock_out.stock_item_id AS product_sales
                                        FROM       transaction_master
                                        LEFT JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        LEFT JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        LEFT JOIN debit_credit
                                        ON         transaction_master.tran_id=debit_credit.tran_id
                                        LEFT JOIN ledger_head
                                                ON ledger_head.ledger_head_id = debit_credit.ledger_head_id
                                        WHERE   ledger_head.group_id IN($string_tree_group) $voucher_id_out_sql  AND transaction_master.transaction_date BETWEEN :from_date_out AND        :to_date_out
                                        GROUP BY   stock_out.stock_item_id) AS t
                    ON        stock_item.stock_item_id=t.product_sales
                    $sales_return_sql
                    LEFT JOIN
                                (
                                        SELECT     sum(stock_in.qty)       AS stock_qty_purchase,
                                                   sum(stock_in.total)     AS stock_total_purchase,
                                                    stock_in.stock_item_id AS product_purchase
                                        FROM       transaction_master
                                        LEFT JOIN `stock_in`
                                        ON         transaction_master.tran_id=stock_in.tran_id
                                        LEFT JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        LEFT JOIN debit_credit
                                        ON         transaction_master.tran_id=debit_credit.tran_id
                                        LEFT JOIN ledger_head
                                        ON         ledger_head.ledger_head_id = debit_credit.ledger_head_id
                                        WHERE     ledger_head.group_id IN($string_tree_group)   $voucher_id_in_sql AND transaction_master.transaction_date BETWEEN :from_date_in AND        :to_date_in
                                        GROUP BY   stock_in.stock_item_id) AS t1
                    ON        stock_item.stock_item_id=t1.product_purchase
                    $purchase_return_sql
                    LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                    ORDER BY  stock_group.stock_group_name DESC,stock_item.product_name DESC
                    ";
    
        $params['from_date_in'] = $from_date;
        $params['to_date_in'] = $to_date;
        $params['from_date_out'] = $from_date;
        $params['to_date_out'] = $to_date;
       
        
        $data = DB::select($query,$params);
      
       
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');

        return $this->calculateGroupTotals($tree_data);
    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_sales'] = array_sum(array_column($obj['children'], 'stock_qty_sales')) + $obj['stock_qty_sales'] ?? 0;
                $obj['stock_qty_purchase'] = array_sum(array_column($obj['children'], 'stock_qty_purchase')) + $obj['stock_qty_purchase'] ?? 0;
                $obj['stock_total_sales'] = array_sum(array_column($obj['children'], 'stock_total_sales')) + $obj['stock_total_sales'] ?? 0;
                $obj['stock_total_purchase'] = array_sum(array_column($obj['children'], 'stock_total_purchase')) + $obj['stock_total_purchase'] ?? 0;
            }
        }

        return $arr;
    }
}
