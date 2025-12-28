<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Master\GodownRepository;

class WarehouseWiseStockRepository implements WarehouseWiseStockInterface
{
    private $tree;
    
    private $authRepository;

    private $godownRepository;

    public function __construct(Tree $tree,AuthRepository $authRepository,GodownRepository $godownRepository)
    {
        $this->tree = $tree;
        $this->authRepository=$authRepository;
        $this->godownRepository=$godownRepository;
        
    }

    public function getWarehouseWiseStockOfIndex($request = null)
    {
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            if(isset($request->godown_id)){
                // main godown
                $godown = $request->godown_id == 0 ? '' : "godown_id=$request->godown_id AND";
            }else if(array_key_exists("godown_without_damage_id",$request->all())){
                  //damage godown
                  $godowns = $this->godownRepository->withoutDamagegodown();
                  $godown_convert_string= implode(", ",array_column(json_decode(json_encode($godowns, true), true),'godown_id'));
                  $godown =$request->godown_without_damage_id ==null ? "godown_id IN( $godown_convert_string) AND":"godown_id IN($request->godown_without_damage_id) AND";
            }else{
                //user wise godown
                $get_user = $this->authRepository->findUserGet(Auth()->user()->id);

                if (array_sum(array_map('intval',explode(' ', $get_user->godown_id))) == 0) {
                        $voucher_godown = DB::table('voucher_setup')
                                            ->select('voucher_setup.godown_id')
                                            ->whereIn('voucher_type_id',[27,30])->get();
                    $godown_convert_string= implode(", ",array_unique(array_column(json_decode(json_encode($voucher_godown, true), true),'godown_id')));
                    $godown ="godown_id IN($godown_convert_string) AND";
                }else{

                    $godown ="godown_id IN($get_user->godown_id) AND";
                }
            }

            $stock_group_id = explode('-', $request->stock_group_id, 2);
        }
        if ($stock_group_id[0] == 0) {
            $inner_join_item_in = '';
            $inner_join_item_out = '';
            $stock_group_in = '';
            $group_id = '';
        } else {
            $data_tree_group = DB::select("WITH recursive tree
                                            AS
                                            (
                                                    SELECT stock_group.stock_group_id
                                                    FROM   stock_group
                                                    WHERE  find_in_set(stock_group.stock_group_id,$stock_group_id[0])
                                                    UNION ALL
                                                    SELECT e.stock_group_id
                                                    FROM   tree h
                                                    JOIN   stock_group e
                                                    ON     h.stock_group_id=e.under )
                                            SELECT *
                                            FROM   tree");
            $string_tree_group = implode(',', array_column(json_decode(json_encode($data_tree_group, true), true), 'stock_group_id'));
            $inner_join_item_in = 'INNER JOIN stock_item ON stock_in.stock_item_id=stock_item.stock_item_id';
            $stock_group_in = "stock_item.stock_group_id IN($string_tree_group) AND";
            $inner_join_item_out = 'INNER JOIN stock_item ON stock_out.stock_item_id=stock_item.stock_item_id';
            $group_id = "WHERE stock_group.stock_group_id IN($string_tree_group)";
        }

        $data = DB::select(
                               "SELECT      stock_group.stock_group_id,
                                            stock_group.stock_group_name,
                                            stock_group.under,
                                            stock_item.stock_item_id,
                                            stock_item.product_name,
                                            unitsof_measure.symbol,
                                            t.stock_qty_in,
                                            t1.stock_qty_out,
                                            op_in.stock_qty_in_opening,
                                            op_out.stock_qty_out_opening,
                                            t.stock_qty_in               AS stock_in_sum_qty,
                                            t1.stock_qty_out             AS stock_out_sum_qty,
                                            op_in.stock_qty_in_opening   AS stock_in_sum_qty_op,
                                            op_out.stock_qty_out_opening AS stock_out_sum_qty_op
                                FROM      stock_group
                                LEFT JOIN stock_item
                                ON        stock_group.stock_group_id=stock_item.stock_group_id
                                LEFT JOIN
                                            (
                                                    SELECT     Sum(stock_in.qty)      AS stock_qty_in,
                                                                stock_in.stock_item_id AS product_in
                                                    FROM       transaction_master
                                                    INNER JOIN `stock_in`
                                                    ON         transaction_master.tran_id=stock_in.tran_id
                                                     $inner_join_item_in
                                                    WHERE      $godown $stock_group_in transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                                    GROUP BY   stock_in.stock_item_id) AS t
                                ON        stock_item.stock_item_id=t.product_in
                                LEFT JOIN
                                            (
                                                    SELECT     sum(stock_out.qty)      AS stock_qty_out,
                                                                stock_out.stock_item_id AS product_out
                                                    FROM       transaction_master
                                                    INNER JOIN `stock_out`
                                                    ON         transaction_master.tran_id=stock_out.tran_id
                                                    $inner_join_item_out
                                                    WHERE      $godown $stock_group_in transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                                    GROUP BY   stock_out.stock_item_id) AS t1
                                ON        stock_item.stock_item_id=t1.product_out
                                LEFT JOIN
                                            (
                                                    SELECT     sum(stock_in.qty)      AS stock_qty_in_opening,
                                                               stock_in.stock_item_id AS product_in_opening
                                                    FROM       transaction_master
                                                    INNER JOIN `stock_in`
                                                    ON         transaction_master.tran_id=stock_in.tran_id
                                                    $inner_join_item_in
                                                    WHERE      $stock_group_in $godown transaction_master.transaction_date<'$from_date'
                                                    GROUP BY   stock_in.stock_item_id) AS op_in
                                ON        stock_item.stock_item_id=op_in.product_in_opening
                                LEFT JOIN
                                            (
                                                    SELECT     sum(stock_out.qty)      AS stock_qty_out_opening,
                                                               stock_out.stock_item_id AS product_out_opening
                                                    FROM       transaction_master
                                                    INNER JOIN `stock_out`
                                                    ON         transaction_master.tran_id=stock_out.tran_id
                                                    $inner_join_item_out
                                                    WHERE      $godown $stock_group_in transaction_master.transaction_date<'$from_date'
                                                    GROUP BY   stock_out.stock_item_id) AS op_out
                                ON        stock_item.stock_item_id=op_out.product_out_opening 
                                LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id
                                $group_id
                                ORDER BY  stock_group.stock_group_name DESC,stock_item.product_name DESC

            "
        );

        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, $stock_group_id[1] ?? 0, 0, 'stock_group_id', 'under', 'stock_item_id');

        return $this->calculateGroupTotals($tree_data);

    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty_in'] = array_sum(array_column($obj['children'], 'stock_qty_in')) + $obj['stock_qty_in'] ?? 0;
                $obj['stock_qty_out'] = array_sum(array_column($obj['children'], 'stock_qty_out')) + $obj['stock_qty_out'] ?? 0;
                $obj['stock_qty_in_opening'] = array_sum(array_column($obj['children'], 'stock_qty_in_opening')) + $obj['stock_qty_in_opening'] ?? 0;
                $obj['stock_qty_out_opening'] = array_sum(array_column($obj['children'], 'stock_qty_out_opening')) + $obj['stock_qty_out_opening'] ?? 0;
            }
        }

        return $arr;
    }
}
