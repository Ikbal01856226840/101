<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\Master\GodownRepository;

class StockGroupSummaryRepository implements StockGroupSummaryInterface
{
    private $tree;

    private $godownRepository;

    public function __construct(Tree $tree ,GodownRepository $godownRepository)
    {
         $this->tree = $tree;
         $this->godownRepository = $godownRepository;
    }

    public function getStockGroupSummaryOfIndex($request = null)
    {
        $params_in_godown=[];
        $godown='';
        $godown_op='';
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if(!empty($request->godown_id)){
                $godown = $request->godown_id == 0 ? '' : "godowns.godown_id=:godown_id AND";
                $params_in_godown['godown_id']=$request->godown_id;
                $godown_op = $request->godown_id == 0 ? '' : "godowns.godown_id=:godown_id_op AND";
                $params_in_godown['godown_id_op']=$request->godown_id;
            }

            $stock_group_id = explode('-', $request->stock_group_id, 2);
        }
        if ($stock_group_id[0] == 0) {
            $inner_join_item_in = '';
            $stock_group_in = '';
            $inner_join_item_group='';
            $group_id = '';
            $stock_group_group='';
        } else {

            // tree value get sql
            $data_tree_group = DB::select("WITH recursive tree
                                            AS
                                            (
                                                    SELECT stock_group.stock_group_id
                                                    FROM   stock_group
                                                    WHERE  find_in_set(stock_group.stock_group_id,:stock_group_id)
                                                    UNION ALL
                                                    SELECT e.stock_group_id
                                                    FROM   tree h
                                                    JOIN   stock_group e
                                                    ON     h.stock_group_id=e.under )
                                            SELECT *
                                            FROM   tree",['stock_group_id'=>$stock_group_id[0]]);
            // value implode
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
            // condition checking
            $inner_join_item_in = 'INNER JOIN stock_item ON s.stock_item_id=stock_item.stock_item_id';
            $inner_join_item_group = 'INNER JOIN stock_item ON stock.stock_item_id=stock_item.stock_item_id';
            $stock_group_in = "WHERE stock_item.stock_group_id IN($string_tree_group) ";
            $stock_group_group = "stock_item.stock_group_id IN($string_tree_group) AND";
            $group_id = "WHERE stock_group.stock_group_id IN($string_tree_group)";
        }

        if ($request->godown_id == 0) {
            $params_without_godown=[];
            $exists_sql="";
            if($request->initial==0){}else{
                // exits item checking
                $exists_sql="WHERE EXISTS
                            (
                                SELECT s.stock_item_id
                                FROM   stock AS s
                                WHERE  s.tran_date BETWEEN :from_date_5 AND    :to_date_5
                                AND    stock.stock_item_id=s.stock_item_id)";
                $params_without_godown['from_date_5'] = $from_date;
                $params_without_godown['to_date_5'] = $to_date;
            }

          
                $query= "WITH cte
                        AS
                        (
                                    SELECT
                                            stock_item.stock_group_id,
                                            stock_item.stock_item_id,
                                            stock_item.product_name,
                                            unitsof_measure.symbol,
                                            sum(
                                            CASE
                                                        WHEN tran_date BETWEEN :from_date AND       :to_date THEN inwards_qty
                                                        ELSE 0
                                            end ) AS stock_qty_in,
                                            sum(
                                            CASE
                                                        WHEN tran_date BETWEEN :from_date_1 AND       :to_date_1 THEN inwards_value
                                                        ELSE 0
                                            end ) AS stock_total_in,
                                            sum(
                                            CASE
                                                        WHEN tran_date BETWEEN :from_date_2 AND       :to_date_2 THEN outwards_qty
                                                        ELSE 0
                                            end ) AS stock_qty_out,
                                            sum(
                                            CASE
                                                        WHEN tran_date BETWEEN :from_date_3 AND       :to_date_3 THEN outwards_value
                                                        ELSE 0
                                            end ) AS stock_total_out,
                                            sum(
                                            CASE
                                                        WHEN tran_date < :op_from_date_1 THEN (inwards_qty - outwards_qty)
                                                        ELSE 0
                                            end )  AS stock_qty_opening,
                                            (SELECT stock.current_rate
                                                FROM stock
                                                WHERE tran_date <=:to_date_4 AND stock.stock_item_id=stock_item.stock_item_id
                                                ORDER BY stock.id DESC LIMIT 1) AS current_rate,
                                            (SELECT stock.current_rate
                                                FROM stock
                                                WHERE tran_date < :op_from_date_2 AND stock.stock_item_id=stock_item.stock_item_id
                                                ORDER BY  stock.id DESC LIMIT 1) AS op_in_rate
                                    FROM      stock_item
                                    LEFT JOIN unitsof_measure
                                    ON        stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                    INNER JOIN stock
                                    ON        stock_item.stock_item_id = stock.stock_item_id
                                    $stock_group_in
                                    $exists_sql
                                    GROUP BY  stock_item.stock_item_id
                                    )

                        SELECT  stock_group.stock_group_id,
                                stock_group.stock_group_name,
                                stock_group.under,
                                cte.stock_item_id,
                                cte.product_name,
                                cte.symbol,
                                cte.stock_qty_in,
                                cte.stock_qty_out,
                                cte.stock_total_in,
                                cte.stock_total_out,
                                cte.stock_qty_in                AS stock_in_sum_qty,
                                cte.stock_qty_out               AS stock_out_sum_qty,
                                cte.stock_total_in              AS stock_total_sum_in,
                                cte.stock_total_out             AS stock_total_sum_out,
                                cte.current_rate,
                                ((Coalesce(cte.stock_qty_in,0)+Coalesce(cte.stock_qty_opening,0))-Coalesce(cte.stock_qty_out,0)) AS current_qty,
                                (((Coalesce(cte.stock_qty_in,0)+Coalesce(cte.stock_qty_opening,0))-Coalesce(cte.stock_qty_out,0))*Coalesce(cte.current_rate,0)) AS sum_current_value,
                                (cte.stock_qty_opening*cte.op_in_rate) AS sum_op_value,
                                cte.stock_qty_opening              AS op_qty,
                                cte.stock_qty_opening              AS total_op_qty,
                                cte.op_in_rate                     AS op_rate
                        FROM    stock_group
                        LEFT JOIN cte
                        ON        stock_group.stock_group_id = cte.stock_group_id
                        $group_id
                        ORDER BY stock_group_name DESC, product_name DESC
                       ;
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
            $params_without_godown['op_from_date_1'] = $from_date;
            $params_without_godown['op_from_date_2'] = $from_date;

            $data = DB::select($query,$params_without_godown);

        } else {
               $query_in="SELECT                stock_group.stock_group_id,
                                                stock_group.stock_group_name,
                                                stock_group.under,
                                                stock_item.stock_item_id,
                                                stock_item.product_name,
                                                unitsof_measure.symbol,
                                                t.stock_qty_in,
                                                t.stock_qty_out,
                                                t.stock_total_in,
                                                t.stock_total_out,
                                                t.stock_qty_in                AS stock_in_sum_qty,
                                                t.stock_qty_out               AS stock_out_sum_qty,
                                                t.stock_total_in              AS stock_total_sum_in,
                                                t.stock_total_out             AS stock_total_sum_out,
                                            
                                                (((Coalesce(op_in.stock_qty_in_opening,0)-Coalesce(op_in.stock_qty_out_opening,0))+Coalesce(t.stock_qty_in,0))-Coalesce(t.stock_qty_out,0))  AS current_qty,
                                              
                                                ((op_in.stock_qty_in_opening-op_in.stock_qty_out_opening)*OppeningGodownWiseRateCal(stock_item.stock_item_id,$request->godown_id, '$from_date')) AS sum_op_value,
                                                  ((((Coalesce(op_in.stock_qty_in_opening,0)-Coalesce(op_in.stock_qty_out_opening,0))+Coalesce(t.stock_qty_in,0))-Coalesce(t.stock_qty_out,0))*New_GodownWiseRateCal(stock_item.stock_item_id,$request->godown_id,'$to_date')) AS sum_current_value,
                                                (op_in.stock_qty_in_opening-op_in.stock_qty_out_opening) AS op_qty,
                                                (op_in.stock_qty_in_opening-op_in.stock_qty_out_opening) AS total_op_qty,
                                               New_GodownWiseRateCal(stock_item.stock_item_id,$request->godown_id,'$to_date') AS current_rate,
                                                OppeningGodownWiseRateCal(stock_item.stock_item_id,$request->godown_id, '$from_date') AS op_rate
                                                

                                    FROM      stock_group
                                    LEFT JOIN stock_item
                                    ON        stock_group.stock_group_id=stock_item.stock_group_id
                                    LEFT JOIN
                                                (
                                                        SELECT      Sum(stock.inwards_qty)    AS stock_qty_in,
                                                                    Sum(stock.inwards_value)  AS stock_total_in,
                                                                    Sum(stock.outwards_qty)   AS stock_qty_out,
                                                                    Sum(stock.outwards_value) AS stock_total_out,
                                                                    stock.stock_item_id       AS product_id
                                                        FROM        stock
                                                        INNER JOIN godowns
                                                        ON         stock.godown_id=godowns.godown_id $inner_join_item_group
                                                        WHERE      $godown  $stock_group_group  tran_date BETWEEN :from_date AND        :to_date
                                                        GROUP BY   stock.stock_item_id) AS t
                                    ON        stock_item.stock_item_id=t.product_id
                                    LEFT JOIN
                                                (
                                                        SELECT      sum(stock.inwards_qty)   AS stock_qty_in_opening,
                                                                    sum(stock.outwards_qty)  AS stock_qty_out_opening,
                                                                    stock.stock_item_id      AS product_id_opening
                                                        FROM        stock
                                                        INNER JOIN godowns
                                                        ON         stock.godown_id=godowns.godown_id $inner_join_item_group
                                                        WHERE      $godown_op $stock_group_group   tran_date<:op_from_date_1
                                                        GROUP BY   stock.stock_item_id) AS op_in
                                    ON        stock_item.stock_item_id=op_in.product_id_opening
                                    LEFT JOIN unitsof_measure
                                    ON        stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                    $group_id
                                ORDER BY   stock_group.stock_group_name DESC,stock_item.product_name DESC
                                ";
                    $params_in_godown['from_date'] = $from_date;
                    $params_in_godown['to_date'] = $to_date;
                    $params_in_godown['op_from_date_1'] = $from_date;
            $data = DB::select($query_in,$params_in_godown);


        }
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);

        $tree_data = $this->tree->buildTree($group_chart_object_to_array, $stock_group_id[1] ?? 0, 0, 'stock_group_id', 'under', 'stock_item_id');
         $stock_group_summary=$this->calculateGroupTotals($tree_data);
         $sum_of_children=$this->calculateSumOfChildren($stock_group_summary);
         return ['stock_group_summary'=>$stock_group_summary,'sum_of_children'=>$sum_of_children];
    }

    public function getStockGroupSummaryStoreOfIndex($request = null)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $stock_group_in_godown = "";
            if(!empty($request->godown_id)){
                $godown = $request->godown_id == 0 ? '' : "stock.godown_id = :godown_id ";
                $params_with_godown['godown_id']=$request->godown_id;
            }else{
                  $godowns=implode(',', $this->godownRepository->godownAccess()->pluck('godown_id')->toArray());
                  if(!empty($godowns)){
                    $godown =  "stock.godown_id IN($godowns) "; 
                  }
                  
            }
            $stock_group_id = explode('-', $request->stock_group_id, 2);
        }

        if ($stock_group_id[0] == 0) {
            $group_id = '';
            $stock_group_in_godown = "";
        } else {

            // tree value get sql
            $data_tree_group = DB::select("WITH recursive tree
                                            AS
                                            (
                                                    SELECT stock_group.stock_group_id
                                                    FROM   stock_group
                                                    WHERE  find_in_set(stock_group.stock_group_id,:stock_group_id)
                                                    UNION ALL
                                                    SELECT e.stock_group_id
                                                    FROM   tree h
                                                    JOIN   stock_group e
                                                    ON     h.stock_group_id=e.under )
                                            SELECT *
                                            FROM   tree",['stock_group_id'=>$stock_group_id[0]]);
            // value implode
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
            // condition checking
            $group_id = "WHERE stock_group.stock_group_id IN($string_tree_group)";
            $stock_group_in_godown = "AND stock_item.stock_group_id IN($string_tree_group) ";
        }

        
     

                                $query= "WITH cte
                                AS
                                (
                                            SELECT
                                                    stock_item.stock_group_id,
                                                    stock_item.stock_item_id,
                                                    stock_item.product_name,
                                                    unitsof_measure.symbol,
                                                    sum(
                                                    CASE
                                                                WHEN tran_date BETWEEN :from_date AND       :to_date THEN inwards_qty
                                                                ELSE 0
                                                    end ) AS stock_qty_in,
                                                    sum(
                                                    CASE
                                                                WHEN tran_date BETWEEN :from_date_2 AND       :to_date_2 THEN outwards_qty
                                                                ELSE 0
                                                    end ) AS stock_qty_out,
                                                    sum(
                                                    CASE
                                                                WHEN tran_date < :op_from_date_1 THEN (inwards_qty - outwards_qty)
                                                                ELSE 0
                                                    end )  AS stock_qty_opening
                                                   
                                            FROM      stock_item
                                            LEFT JOIN unitsof_measure
                                            ON        stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                            INNER JOIN stock
                                            ON        stock_item.stock_item_id = stock.stock_item_id
                                            WHERE     $godown  $stock_group_in_godown
                                            GROUP BY  stock_item.stock_item_id
                                            )

                                SELECT  stock_group.stock_group_id,
                                        stock_group.stock_group_name,
                                        stock_group.under,
                                        cte.stock_item_id,
                                        cte.product_name,
                                        cte.symbol,
                                        cte.stock_qty_in,
                                        cte.stock_qty_out,
                                        cte.stock_qty_in                AS stock_in_sum_qty,
                                        cte.stock_qty_out               AS stock_out_sum_qty,
                                        cte.stock_qty_opening              AS op_qty,
                                        cte.stock_qty_opening              AS total_op_qty
                                FROM    stock_group
                                LEFT JOIN cte
                                ON        stock_group.stock_group_id = cte.stock_group_id
                                $group_id
                                ORDER BY stock_group_name DESC, product_name DESC
                            ;
                    ";
                    
                    $params_with_godown['from_date'] = $from_date;
                    $params_with_godown['to_date'] = $to_date;
               
                    $params_with_godown['from_date_2'] = $from_date;
                    $params_with_godown['to_date_2'] = $to_date;
                    $params_with_godown['op_from_date_1'] = $from_date;
                    $data = DB::select($query,$params_with_godown);

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, $stock_group_id[1] ?? 0, 0, 'stock_group_id', 'under', 'stock_item_id');
         $stock_group_summary=$this->calculateGroupTotalsStore($tree_data);
         $sum_of_children=$this->calculateSumOfChildrenStore($stock_group_summary);
         return ['stock_group_summary'=>$stock_group_summary,'sum_of_children'=>$sum_of_children];
    }

    // stock  group calculation
    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_in'] = array_sum(array_column($obj['children'], 'stock_qty_in')) + $obj['stock_qty_in'] ?? 0;
                $obj['stock_qty_out'] = array_sum(array_column($obj['children'], 'stock_qty_out')) + $obj['stock_qty_out'] ?? 0;
                $obj['total_op_qty'] = array_sum(array_column($obj['children'], 'total_op_qty')) + $obj['total_op_qty'] ?? 0;
                $obj['sum_op_value'] = array_sum(array_column($obj['children'], 'sum_op_value')) + $obj['sum_op_value'] ?? 0;
                $obj['stock_total_sum_in'] = array_sum(array_column($obj['children'], 'stock_total_sum_in')) + $obj['stock_total_sum_in'] ?? 0;
                $obj['stock_total_sum_out'] = array_sum(array_column($obj['children'], 'stock_total_sum_out')) + $obj['stock_total_sum_out'] ?? 0;
                $obj['sum_current_value'] = array_sum(array_column($obj['children'], 'sum_current_value')) + $obj['sum_current_value'] ?? 0;
            }
        }

        return $arr;
    }


    function calculateSumOfChildren($array)
        {
            $result = [];

            function sumProperties($array, $prop)
            {
                return array_reduce($array, function ($acc, $val) use ($prop) {
                    return $acc + ($val[$prop] ?? 0);
                }, 0);
            }

            function processNode($node, &$result)
            {
                if (!isset($result[$node['stock_group_id']])) {
                    $result[$node['stock_group_id']] = [
                        'stock_group_id' => $node['stock_group_id'],
                        'stock_qty_in' => 0,
                        'stock_qty_out' => 0,
                        'total_op_qty' => 0,
                        'sum_op_value' => 0,
                        'stock_total_sum_in' => 0,
                        'stock_total_sum_out' => 0,
                        'sum_current_value' => 0,
                    ];
                }

                $currentNode = &$result[$node['stock_group_id']];

                $currentNode['stock_qty_in'] += $node['stock_qty_in'] ?? 0;
                $currentNode['stock_qty_out'] += $node['stock_qty_out'] ?? 0;
                $currentNode['total_op_qty'] += $node['total_op_qty'] ?? 0;
                $currentNode['sum_op_value'] += $node['sum_op_value'] ?? 0;
                $currentNode['stock_total_sum_in'] += $node['stock_total_sum_in'] ?? 0;
                $currentNode['stock_total_sum_out'] += $node['stock_total_sum_out'] ?? 0;
                $currentNode['sum_current_value'] += $node['sum_current_value'] ?? 0;

                if (isset($node['children'])) {
                    foreach ($node['children'] as $child) {
                        processNode($child, $result);
                    }
                }
            }

            foreach ($array as $node) {
                processNode($node, $result);
            }

            return array_values($result);
        }

        // stock  group calculation
    public function calculateGroupTotalsStore($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotalsStore($obj['children']);
                $obj['stock_qty_in'] = array_sum(array_column($obj['children'], 'stock_qty_in')) + $obj['stock_qty_in'] ?? 0;
                $obj['stock_qty_out'] = array_sum(array_column($obj['children'], 'stock_qty_out')) + $obj['stock_qty_out'] ?? 0;
                $obj['total_op_qty'] = array_sum(array_column($obj['children'], 'total_op_qty')) + $obj['total_op_qty'] ?? 0;
              
            }
        }

        return $arr;
    }


    function calculateSumOfChildrenStore($array)
        {
            $result = [];

            function sumProperties($array, $prop)
            {
                return array_reduce($array, function ($acc, $val) use ($prop) {
                    return $acc + ($val[$prop] ?? 0);
                }, 0);
            }

            function processNode($node, &$result)
            {
                if (!isset($result[$node['stock_group_id']])) {
                    $result[$node['stock_group_id']] = [
                        'stock_group_id' => $node['stock_group_id'],
                        'stock_qty_in' => 0,
                        'stock_qty_out' => 0,
                        'total_op_qty' => 0
                       
                    ];
                }

                $currentNode = &$result[$node['stock_group_id']];

                $currentNode['stock_qty_in'] += $node['stock_qty_in'] ?? 0;
                $currentNode['stock_qty_out'] += $node['stock_qty_out'] ?? 0;
                $currentNode['total_op_qty'] += $node['total_op_qty'] ?? 0;
                
                if (isset($node['children'])) {
                    foreach ($node['children'] as $child) {
                        processNode($child, $result);
                    }
                }
            }

            foreach ($array as $node) {
                processNode($node, $result);
            }

            return array_values($result);
    }


}
