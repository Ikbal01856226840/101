<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;
use App\Services\Tree;
class AccountLedgerDealerRetrailerAnalysisRepository implements AccountLedgerDealerRetrailerAnalysisInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }
    public function DealerRetrailerAnalysisOfIndex($request = null)
    {
        $params=[];
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $ledger_id_in='';
        $ledger_id_out='';
        $debit_credit_sql='';
        $ledger_id_purchase_return='';
        $ledger_id_sales_return_out='';
        if ($request->sort_by == 1) {
            $sort_by = "transaction_date ASC";
        } else {
            if ($request->sort_type == 1) {
                $sort_by = ($request->sort_by == 2 ? 'debit ASC' : ($request->sort_by == 3 ? 'credit ASC' : ($request->sort_by == 4 ? ' dr_cr ASC' : ($request->sort_by == 5 ? 'ledger_name ASC' : ''))));
            } else {
                $sort_by = ($request->sort_by == 2 ? 'debit DESC' : ($request->sort_by == 3 ? 'credit DESC' : ($request->sort_by == 4 ? ' dr_cr DESC' : ($request->sort_by == 5 ? 'ledger_name DESC' : ''))));;
            }
        }


            $from_date = $request->from_date;
            $to_date = $request->to_date;

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


                if(!empty($request->ledger_id)){

                    $debit_credit_sql="INNER JOIN debit_credit  ON  transaction_master.tran_id=debit_credit.tran_id";

                    $ledger_id_in = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id IN($group_id) AND";


                    $ledger_id_out = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id IN($group_id) AND";


                    if(isset($request->purchase_return_in)){
                        $ledger_id_purchase_return = $request->ledger_id == 0 ? '' :"debit_credit.ledger_head_id IN($group_id) AND";

                    }
                    if(isset($request->sales_return_out)){
                        $ledger_id_sales_return_out = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id IN($group_id) AND";

                    }
                }

            // Prepare voucher_id_in
            if (!empty($request->in_qty)) {
                $voucher_id_in = $request->in_qty; // Array of IDs
                $placeholders_in = implode(',', array_map(function($i) { return ':voucher_id_in_'.$i; }, array_keys($voucher_id_in)));

                $voucher_id_in_sql = "voucher_setup.voucher_type_id IN ($placeholders_in)";

                foreach ($voucher_id_in as $i => $id) {
                    $params['voucher_id_in_'.$i] = $id;
                }
            } else {
                $voucher_id_in_sql = "voucher_setup.voucher_type_id IN (0)";
            }

            // Prepare voucher_id_out
            if (!empty($request->out_qty)) {
                $voucher_id_out = $request->out_qty; // Array of IDs
                $placeholders_out = implode(',', array_map(function($i) { return ':voucher_id_out_'.$i; }, array_keys($voucher_id_out)));

                $voucher_id_out_sql = "voucher_setup.voucher_type_id IN ($placeholders_out)";

                foreach ($voucher_id_out as $i => $id) {
                    $params['voucher_id_out_'.$i] = $id;
                }
            } else {
                $voucher_id_out_sql = "voucher_setup.voucher_type_id IN (0)";
            }



                $purchase_return_sql="";
                $sales_return_sql="";
                $include_purchse_return="";
                if(isset($request->purchase_return_in)){

                $purchase_return_sql="LEFT JOIN
                                        (
                                                SELECT     Sum(stock_in.qty)      AS purchase_return_stock_qty_in,
                                                            Sum(stock_in.total)    AS purchase_return_stock_total_in,
                                                            stock_in.stock_item_id AS purchase_return_product_in
                                                FROM       transaction_master
                                                INNER JOIN `stock_in`
                                                ON         transaction_master.tran_id=stock_in.tran_id
                                                INNER JOIN voucher_setup
                                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                                $debit_credit_sql
                                                WHERE   $ledger_id_purchase_return voucher_setup.voucher_type_id=29 AND transaction_master.transaction_date BETWEEN :from_date_purchase_return AND       :to_date_purchase_return
                                                GROUP BY   stock_in.stock_item_id) AS purchase_return
                            ON        stock_item.stock_item_id=purchase_return.purchase_return_product_in";
                            $params['from_date_purchase_return'] = $from_date;
                            $params['to_date_purchase_return'] = $to_date;
                $include_purchse_return="
                                    ABS(Coalesce(t1.stock_qty_out,0)+Coalesce(ABS(purchase_return.purchase_return_stock_qty_in),0)) AS stock_qty_out,
                                    ABS(Coalesce(t1.stock_qty_out,0)+Coalesce(ABS(purchase_return.purchase_return_stock_qty_in),0)) AS stock_qty_total_out,
                                    ABS(Coalesce(t1.stock_total_out,0)+Coalesce(ABS(purchase_return.purchase_return_stock_total_in),0)) AS stock_total_out,
                                    ABS(Coalesce(t1.stock_total_out,0)+Coalesce(ABS(purchase_return.purchase_return_stock_total_in),0)) AS stock_total_value_out
                ";
                }else{
                $include_purchse_return="
                t1.stock_qty_out,
                t1.stock_qty_out AS stock_qty_total_out,
                t1.stock_total_out,
                t1.stock_total_out AS stock_total_value_out
                ";
                }


                $include_sales_return_sql="";
                if(isset($request->sales_return_out)){

                $sales_return_sql="LEFT JOIN
                                    (
                                                        SELECT     sum(stock_out.qty)        AS sales_return_stock_qty_out,
                                                                    sum(stock_out.total)      AS sales_return_stock_total_out,
                                                                    stock_out.stock_item_id  AS sales_return_product_out
                                                        FROM       transaction_master
                                                        INNER JOIN `stock_out`
                                                        ON         transaction_master.tran_id=stock_out.tran_id
                                                        INNER JOIN voucher_setup
                                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                                        $debit_credit_sql
                                                        WHERE      $ledger_id_sales_return_out voucher_setup.voucher_type_id=25 AND transaction_master.transaction_date BETWEEN :from_date_sales_return AND       :to_date_sales_return
                                                        GROUP BY   stock_out.stock_item_id) AS sales_return
                                    ON        stock_item.stock_item_id=sales_return.sales_return_product_out";
                                    $params['from_date_sales_return'] = $from_date;
                                    $params['to_date_sales_return'] = $to_date;
                $include_sales_return_sql="
                                ABS(Coalesce(t.stock_qty_in,0)+Coalesce(ABS(sales_return.sales_return_stock_qty_out),0)) AS stock_qty_in,
                                ABS(Coalesce(t.stock_qty_in,0)+Coalesce(ABS(sales_return.sales_return_stock_qty_out),0)) AS stock_qty_total_in,
                                ABS(Coalesce(t.stock_total_in,0)+Coalesce(ABS(sales_return.sales_return_stock_total_out),0)) AS stock_total_in,
                                ABS(Coalesce(t.stock_total_in,0)+Coalesce(ABS(sales_return.sales_return_stock_total_out),0)) AS stock_total_value_in,
                ";

                }else{
                $include_sales_return_sql="
                t.stock_qty_in,
                t.stock_qty_in AS stock_qty_total_in,
                t.stock_total_in,
                t.stock_total_in AS stock_total_value_in, ";

                }

                $query= " SELECT    stock_group.stock_group_id,
                                stock_group.stock_group_name,
                                stock_group.under,
                                stock_item.stock_item_id,
                                stock_item.product_name,
                                unitsof_measure.symbol,
                                $include_sales_return_sql
                                $include_purchse_return
                        FROM      stock_group
                        LEFT JOIN stock_item
                        ON        stock_group.stock_group_id=stock_item.stock_group_id
                        LEFT JOIN
                                (
                                        SELECT     Sum(stock_in.qty)      AS stock_qty_in,
                                                    Sum(stock_in.total)    AS stock_total_in,
                                                    stock_in.stock_item_id AS product_in
                                        FROM       transaction_master
                                        INNER JOIN `stock_in`
                                        ON         transaction_master.tran_id=stock_in.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE      $ledger_id_in $voucher_id_in_sql AND transaction_master.transaction_date BETWEEN :from_date_in AND       :to_date_in
                                        GROUP BY   stock_in.stock_item_id) AS t
                        ON        stock_item.stock_item_id=t.product_in
                        $purchase_return_sql
                        LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)      AS stock_qty_out,
                                                    sum(stock_out.total)      AS stock_total_out,
                                                    stock_out.stock_item_id AS product_out
                                        FROM       transaction_master
                                        INNER JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE      $ledger_id_out $voucher_id_out_sql AND transaction_master.transaction_date BETWEEN :from_date_out AND        :to_date_out
                                        GROUP BY   stock_out.stock_item_id) AS t1
                        ON        stock_item.stock_item_id=t1.product_out
                        $sales_return_sql
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

        // }
        //     return $data = [
        //         'group_chart_nature' => $group_chart_nature,
        //         'party_ledger' => $party_ledger,
        //         'op_daeler_ledger' => $op_daeler_ledger,
        //         'op_reatrailer_ledger' => $op_reatrailer_ledger,
        //         'op_party_ledger'=>$op_party_ledger,
        //         'description_ledger' => $description_ledger,
        //         'description_stock_in' => $description_stock_in,
        //         'description_stock_out' => $description_stock_out
        //     ];
        }

      public function calculateGroupTotals($arr)
       {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_in'] = array_sum(array_column($obj['children'], 'stock_qty_in')) + $obj['stock_qty_in'] ?? 0;
                $obj['stock_qty_out'] = array_sum(array_column($obj['children'], 'stock_qty_out')) + $obj['stock_qty_out'] ?? 0;
                $obj['stock_total_in'] = array_sum(array_column($obj['children'], 'stock_total_in')) + $obj['stock_total_in'] ?? 0;
                $obj['stock_total_out'] = array_sum(array_column($obj['children'], 'stock_total_out')) + $obj['stock_total_out'] ?? 0;
            }
        }

        return $arr;
    }

    function calculateSumOfChildren(array $nodes) {
        $result = [];
        function processNode($node, &$result) {
            // Initialize result for the current stock_group_id if not already
            if (!isset($result[$node['stock_group_id']])) {
                $result[$node['group_chart_id']] = [
                    'stock_group_id' => $node['stock_group_id'],
                    'stock_qty_in' => 0,
                    'stock_qty_out' => 0,
                    'stock_total_in' => 0,
                    'stock_total_out' => 0,

                ];
            }

            // Accumulate values for the current node
            $result[$node['stock_group_id']]['stock_qty_in'] += $node['stock_qty_in'] ?? 0;
            $result[$node['stock_group_id']]['stock_qty_out'] += $node['stock_qty_out'] ?? 0;
            $result[$node['stock_group_id']]['stock_total_in'] += $node['stock_total_in'] ?? 0;
            $result[$node['group_chart_id']]['stock_total_out'] += $node['stock_total_out'] ?? 0;


            // Process children recursively, if any
            if (isset($node['children']) && is_array($node['children'])) {
                foreach ($node['children'] as $child) {
                    processNode($child, $result);
                }
            }
        }

        // Process all nodes in the input array
        foreach ($nodes as $node) {
            processNode($node, $result);
        }

        // Return the values of the result as an array
        return array_values($result);
   }
    public function DealerAndRetrailerSalesQuantity($request = null)
    {
        $params=[];
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $ledger_id_out='';
        $debit_credit_sql='';
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


                if(!empty($request->ledger_id)){

                    $debit_credit_sql="INNER JOIN debit_credit  ON  transaction_master.tran_id=debit_credit.tran_id
                                       INNER JOIN ledger_head  ON ledger_head.ledger_head_id=debit_credit.ledger_head_id";

                    //ledger type 1 dealer and retrailer both
                    if($request->ledger_type == 1){

                        $ledger_id_out = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id IN($group_id) AND";
                        $ledger_id = $request->ledger_id == 0 ? '' : "ledger_head.ledger_head_id IN($group_id) ";
                    }else{
                         $ledger_id = $request->ledger_id == 0 ? '' : "ledger_head.ledger_head_id IN($group_id) ";
                        $ledger_id_out = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id IN($group_id) AND";
                    }

                }



                $query= " SELECT ledger_head.ledger_type,
                                 ledger_head.ledger_name,
                                 ledger_head.alias,
                                 t1.stock_qty_out AS sales_qty,
                                 t2.stock_qty_out AS sales_return_qty

                        FROM      ledger_head
                        LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)      AS stock_qty_out,
                                                   debit_credit.ledger_head_id
                                        FROM       transaction_master
                                        INNER JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE      $ledger_id_out  voucher_setup.voucher_type_id =19  AND transaction_master.transaction_date BETWEEN :from_date_out AND        :to_date_out
                                        GROUP BY   debit_credit.ledger_head_id) AS t1
                        ON        ledger_head.ledger_head_id=t1.ledger_head_id
                        LEFT JOIN
                                (
                                        SELECT     sum(stock_out.qty)      AS stock_qty_out,
                                                   debit_credit.ledger_head_id
                                        FROM       transaction_master
                                        INNER JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        $debit_credit_sql
                                        WHERE      $ledger_id_out  voucher_setup.voucher_type_id =25 AND transaction_master.transaction_date BETWEEN :from_date_out_1 AND        :to_date_out_1
                                        GROUP BY   debit_credit.ledger_head_id) AS t2
                        ON        ledger_head.ledger_head_id=t2.ledger_head_id
                        WHERE      $ledger_id
                        ";
                    //dd($query);
                    $params['from_date_out'] = $from_date;
                    $params['to_date_out'] = $to_date;
                     $params['from_date_out_1'] = $from_date;
                    $params['to_date_out_1'] = $to_date;

               return DB::select($query,$params);
        }

        public function RetailerActualSalesGetData ($request = null){
                $params = [];
                if (isset($request)) {
                    $from_date = $request->from_date;
                    $to_date = $request->to_date;

                }
               $ledger_id_out='';
               $debit_credit_sql='';
            //    $data_tree_group = DB::select("WITH recursive tree AS
            //                                 (
            //                                                 SELECT ledger_head.ledger_head_id
            //                                                 FROM   ledger_head
            //                                                 WHERE  find_in_set(ledger_head.ledger_head_id ,:ledger_id)
            //                                                 UNION ALL
            //                                                 SELECT e.ledger_head_id
            //                                                 FROM   tree h
            //                                                 JOIN   ledger_head e
            //                                                 ON     h.ledger_head_id =e.under_ledger_id )
            //                                     SELECT *
            //                               FROM   tree",['ledger_id'=>$request->ledger_id]);

            // // ledger_type one all ledger and two dealer and three retrailer
            // if($request->ledger_type == 1){
            //      $group_id = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'ledger_head_id'));
            // }else if($request->ledger_type == 2){
            //      $group_id =$request->ledger_id;
            // }else if($request->ledger_type == 3){
            //      unset($data_tree_group[array_search($request->ledger_id,array_column(json_decode(json_encode($data_tree_group, true), true),'ledger_head_id'))]); // Remove the element
            //      $group_id = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'ledger_head_id'));
            //      if($group_id){
            //         $group_id=$group_id;
            //      }else{
            //         $group_id=$request->ledger_id;
            //      }
            // }


                if(!empty($request->ledger_id)){

                    $debit_credit_sql="INNER JOIN debit_credit  ON  transaction_master.tran_id=debit_credit.tran_id
                                       INNER JOIN ledger_head  ON ledger_head.ledger_head_id=debit_credit.ledger_head_id";

                    //ledger type 1 dealer and retrailer both
                    if($request->ledger_type == 1){

                        $ledger_id_out = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id IN($request->ledger_id) AND";
                        $ledger_id = $request->ledger_id == 0 ? '' : "ledger_head.ledger_head_id IN($request->ledger_id) ";
                    }else{
                         $ledger_id = $request->ledger_id == 0 ? '' : "ledger_head.ledger_head_id IN($request->ledger_id) ";
                        $ledger_id_out = $request->ledger_id == 0 ? '' : "debit_credit.ledger_head_id IN($request->ledger_id) AND";
                    }

                }

                // if(company()->sales_return==2){
                //     $sales_return="SELECT     sum(stock_out.qty)       AS stock_qty_sales_return,
                //                                 stock_out.stock_item_id AS product_sales_return
                //                     FROM       transaction_master
                //                     INNER JOIN `stock_out`
                //                     ON         transaction_master.tran_id=stock_out.tran_id
                //                     INNER JOIN voucher_setup
                //                     ON         transaction_master.voucher_id=voucher_setup.voucher_id
                //                     $debit_credit_sql
                //                     WHERE      $ledger_id_out  voucher_setup.voucher_type_id=25 AND transaction_master.transaction_date BETWEEN :from_date_sales_return AND        :to_date_sales_return
                //                     GROUP BY   stock_out.stock_item_id";
                //     }else if (company()->sales_return==1){
                //         $sales_return="SELECT       sum(stock_in.qty)       AS stock_qty_sales_return,
                //                                     stock_in.stock_item_id AS product_sales_return
                //                         FROM       transaction_master
                //                         INNER JOIN `stock_in`
                //                         ON         transaction_master.tran_id=stock_in.tran_id
                //                         INNER JOIN voucher_setup
                //                         ON         transaction_master.voucher_id=voucher_setup.voucher_id
                //                         $debit_credit_sql
                //                         WHERE      $ledger_id_out  voucher_setup.voucher_type_id=25 AND transaction_master.transaction_date BETWEEN :from_date_sales_return AND        :to_date_sales_return
                //                         GROUP BY   stock_in.stock_item_id";
                //     }
                        $sales_return="SELECT     sum(stock_out.qty)       AS stock_qty_sales_return,
                                                stock_out.stock_item_id AS product_sales_return
                                    FROM       transaction_master
                                    INNER JOIN `stock_out`
                                    ON         transaction_master.tran_id=stock_out.tran_id
                                    INNER JOIN voucher_setup
                                    ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                    $debit_credit_sql
                                    WHERE      $ledger_id_out  voucher_setup.voucher_type_id=25 AND transaction_master.transaction_date BETWEEN :from_date_sales_return AND        :to_date_sales_return
                                    GROUP BY   stock_out.stock_item_id";
                                    
                $query= "SELECT    stock_group.stock_group_id,
                                    stock_group.stock_group_name,
                                    stock_group.under,
                                    stock_item.stock_item_id,
                                    stock_item.product_name,
                                    unitsof_measure.symbol,
                                    t.stock_qty_sales,
                                    t.stock_qty_sales AS stock_qty_sales_total,
                                    t1.stock_qty_sales_return,
                                    t1.stock_qty_sales_return AS stock_qty_sales_return_total
                                FROM      stock_group
                                LEFT JOIN stock_item
                                ON        stock_group.stock_group_id=stock_item.stock_group_id
                                LEFT JOIN
                                        (
                                                SELECT     Sum(stock_out.qty)      AS stock_qty_sales,
                                                        stock_out.stock_item_id AS product_sales
                                                FROM       transaction_master
                                                INNER JOIN `stock_out`
                                                ON         transaction_master.tran_id=stock_out.tran_id
                                                INNER JOIN voucher_setup
                                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                                 $debit_credit_sql
                                                WHERE      $ledger_id_out  voucher_setup.voucher_type_id=19 AND transaction_master.transaction_date BETWEEN :from_date_sales AND        :to_date_sales
                                                GROUP BY   stock_out.stock_item_id) AS t
                                ON        stock_item.stock_item_id=t.product_sales
                                LEFT JOIN ($sales_return) AS t1

                                ON        stock_item.stock_item_id=t1.product_sales_return
                                LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                ORDER BY  stock_group.stock_group_name DESC,stock_item.product_name DESC
                                ";
                $params['from_date_sales'] = $from_date;
                $params['to_date_sales'] = $to_date;
                $params['from_date_sales_return'] = $from_date;
                $params['to_date_sales_return'] = $to_date;

                $data = DB::select($query,$params);


                $group_chart_object_to_array = json_decode(json_encode($data, true), true);
                $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');

                return $this->calculateAccualSalesGroupTotals($tree_data);
        }

         public function calculateAccualSalesGroupTotals($arr)
        {
            foreach ($arr as &$obj) {
                if (isset($obj['children'])) {
                    $obj['children'] = $this->calculateAccualSalesGroupTotals($obj['children']);
                    $obj['stock_qty_sales'] = array_sum(array_column($obj['children'], 'stock_qty_sales')) + $obj['stock_qty_sales'] ?? 0;
                    $obj['stock_qty_sales_return'] = array_sum(array_column($obj['children'], 'stock_qty_sales_return')) + $obj['stock_qty_sales_return'] ?? 0;
                }
            }

            return $arr;
        }

}
