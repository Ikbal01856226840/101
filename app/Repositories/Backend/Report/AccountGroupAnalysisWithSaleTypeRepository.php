<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class AccountGroupAnalysisWithSaleTypeRepository implements AccountGroupAnalysisWithSaleTypeInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getAccountGroupAnalysisWithSaleTypeOfIndex($request = null)
    {
        $params=[];
        $debit_credit_sql='';
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

           if(!empty($request->group_id)){
            $group_chart = explode('-', $request->group_id, 2);
            $data_tree_group = DB::select("with recursive tree as(
                                            SELECT group_chart.group_chart_id FROM group_chart  WHERE FIND_IN_SET(group_chart.group_chart_id,:group_chart)
                                            UNION ALL
                                            SELECT E.group_chart_id FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
                                        )SELECT * FROM tree",['group_chart' => $group_chart[0]]);
              $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'group_chart_id'));

               $debit_credit_sql="LEFT JOIN debit_credit ON transaction_master.tran_id=debit_credit.tran_id   LEFT JOIN ledger_head  ON  ledger_head.ledger_head_id = debit_credit.ledger_head_id";
               $group_sql=  "ledger_head.group_id IN($string_tree_group) AND";
           }

           $stock_group_id = explode('-', $request->stock_group_id,2);
           if ($stock_group_id[0] == 0) {
               $inner_join_item_in = '';
               $stock_group_in = '';
               $stock_group_id= '';
           } else {
               $data_tree_group = DB::select("with recursive tree as(
                                               SELECT stock_group.stock_group_id FROM stock_group  WHERE FIND_IN_SET(stock_group.stock_group_id,:stock_group_id)
                                               UNION ALL
                                               SELECT E.stock_group_id FROM tree H JOIN stock_group E ON H.stock_group_id=E.under
                                           )SELECT * FROM tree",['stock_group_id'=>$stock_group_id[0]]);
               $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
               $inner_join_item_in = 'LEFT JOIN stock_item ON stock_out.stock_item_id=stock_item.stock_item_id';
               $stock_group_in = "stock_item.stock_group_id IN($string_tree_group) AND";
               $stock_group_id="Where stock_item.stock_group_id IN($string_tree_group) ";

           }

        }
       
        // sales credit godown
        if (!empty($request->godown_id[0])) {
                
            $godown_out=$request->godown_id; // Array of IDs
            $placeholders_out_2 = implode(',', array_map(function($i) { return ':godown_out_id_2_'.$i; }, array_keys($godown_out)));

            $godown_2_sql = "stock_out.godown_id IN ($placeholders_out_2) AND";
        
            foreach ($godown_out as $i => $id) {
                $params[':godown_out_id_2_'.$i] = $id;
            }
            $placeholders_out_1 = implode(',', array_map(function($i) { return ':godown_out_id_1_'.$i; }, array_keys($godown_out)));

            $godown_1_sql = "stock_out.godown_id IN ($placeholders_out_1) AND";
        
            foreach ($godown_out as $i => $id) {
                $params[':godown_out_id_1_'.$i] = $id;
            }
        } else {
            $godown_1_sql = "";
            $godown_2_sql = "";
        }

$query = "WITH Sales 
          AS (
                            SELECT
                                stock_out.stock_item_id,
                                SUM(CASE WHEN transaction_master.sale_type = 1 AND voucher_setup.voucher_type_id <>25 THEN stock_out.qty ELSE 0 END) AS stock_qty_sales_cash,
                                SUM(CASE WHEN transaction_master.sale_type = 1 AND voucher_setup.voucher_type_id <>25 THEN stock_out.total ELSE 0 END) AS stock_total_sales_cash,
                                SUM(CASE WHEN transaction_master.sale_type = 2 AND voucher_setup.voucher_type_id <>25 THEN stock_out.qty ELSE 0 END) AS stock_qty_sales_credit,
                                SUM(CASE WHEN transaction_master.sale_type = 2 AND voucher_setup.voucher_type_id <>25 THEN stock_out.total ELSE 0 END) AS stock_total_sales_credit,
                                SUM(CASE WHEN transaction_master.sale_type = 3 AND voucher_setup.voucher_type_id <>25 THEN stock_out.qty ELSE 0 END) AS stock_qty_inter_company_sales,
                                SUM(CASE WHEN transaction_master.sale_type = 3 AND voucher_setup.voucher_type_id <>25 THEN stock_out.total ELSE 0 END) AS stock_total_inter_company_sales,
                                SUM(CASE WHEN voucher_setup.voucher_type_id = 25 THEN stock_out.qty ELSE 0 END) AS stock_qty_sales_return,
                                SUM(CASE WHEN voucher_setup.voucher_type_id = 25 THEN stock_out.total ELSE 0 END) AS stock_total_sales_return
                            FROM
                                transaction_master
                                
                            LEFT JOIN stock_out ON transaction_master.tran_id = stock_out.tran_id
                            $inner_join_item_in
                            LEFT JOIN voucher_setup ON transaction_master.voucher_id = voucher_setup.voucher_id
                            $debit_credit_sql
                            WHERE
                                ( $group_sql $stock_group_in $godown_1_sql transaction_master.sale_type IN (1, 2, 3) AND voucher_setup.voucher_type_id = 19  AND transaction_master.transaction_date BETWEEN :from_date_sales_cash AND :to_date_sales_cash)
                                OR ( $group_sql $stock_group_in  $godown_2_sql  voucher_setup.voucher_type_id = 25  AND transaction_master.transaction_date BETWEEN :from_date_sales_return AND :to_date_sales_return)
                            GROUP BY
                                stock_out.stock_item_id
                        )

                SELECT
                    stock_group.stock_group_id,
                    stock_group.stock_group_name,
                    stock_group.under,
                    stock_item.stock_item_id,
                    stock_item.product_name,
                    unitsof_measure.symbol,
                    COALESCE(s.stock_qty_sales_cash, 0) AS stock_qty_sales_cash,
                    COALESCE(s.stock_qty_sales_cash, 0) AS stock_qty_sales_cash_total,
                    COALESCE(s.stock_total_sales_cash, 0) AS stock_total_sales_cash,
                    COALESCE(s.stock_total_sales_cash, 0) AS stock_total_sales_cash_value,
                    COALESCE(s.stock_qty_sales_credit, 0) AS stock_qty_sales_credit,
                    COALESCE(s.stock_qty_sales_credit, 0) AS stock_qty_sales_credit_total,
                    COALESCE(s.stock_total_sales_credit, 0) AS stock_total_sales_credit,
                    COALESCE(s.stock_total_sales_credit, 0) AS stock_total_sales_credit_value,
                    COALESCE(s.stock_qty_inter_company_sales, 0) AS stock_qty_inter_company_sales,
                    COALESCE(s.stock_qty_inter_company_sales, 0) AS stock_qty_inter_company_sales_total,
                    COALESCE(s.stock_total_inter_company_sales, 0) AS stock_total_inter_company_sales,
                    COALESCE(s.stock_total_inter_company_sales, 0) AS stock_total_inter_company_sales_value,
                    COALESCE(s.stock_qty_sales_return, 0) AS stock_qty_sales_return,
                    COALESCE(s.stock_qty_sales_return, 0) AS stock_qty_sales_return_total,
                    COALESCE(s.stock_total_sales_return, 0) AS stock_total_sales_return,
                    COALESCE(s.stock_total_sales_return, 0) AS stock_total_sales_return_value
                FROM
                    stock_group
                LEFT JOIN stock_item ON stock_group.stock_group_id = stock_item.stock_group_id
                LEFT JOIN Sales s ON stock_item.stock_item_id = s.stock_item_id
                LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
               
                ORDER BY stock_group.stock_group_name DESC, stock_item.product_name DESC;
                    
                ";
         $params['from_date_sales_cash'] = $from_date;
         $params['to_date_sales_cash'] = $to_date;
         
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
