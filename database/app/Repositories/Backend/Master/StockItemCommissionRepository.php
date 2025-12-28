<?php

namespace App\Repositories\Backend\Master;

use App\Models\StockItemCommission;
use App\Services\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockItemCommissionRepository implements StockItemCommissionInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getStockItemCommissionOfIndex()
    {
        return $this->getStockItemCommissionData();
    }

    public function StoreStockItemCommission($request)
    {
        $stockItemCommission = new StockItemCommission();
        $stockItemCommission->stock_item_id = $request->stock_item_id;
        $stockItemCommission->setup_date = $request->setup_date;
        $stockItemCommission->commission = $request->commission;
        $stockItemCommission->user_id = Auth::id();
        $stockItemCommission->user_name = Auth::user()->user_name;
        $stockItemCommission->updated_history = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Commission: '.$request->commission);
        $stockItemCommission->save();

        return $stockItemCommission;
    }

    public function getStockItemCommissionId($id)
    {
        return StockItemCommission::where('Commission_id', $id)->first();
    }

    public function updateStockItemCommission($request, $id)
    {
        $stockItemCommission = StockItemCommission::findOrFail($id);
        $stockItemCommission->stock_item_id = $request->stock_item_id;
        $stockItemCommission->setup_date = $request->setup_date;
        $stockItemCommission->commission = $request->commission;
        $data = json_decode($stockItemCommission->updated_history);
        $stockItemCommission->updated_history = json_encode($data.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Commission: '.$request->commission);
        $stockItemCommission->save();

        return $stockItemCommission;

    }

    public function deleteStockItemCommission($id)
    {

        return StockItemCommission::findOrFail($id)->delete();
    }

    public function getTree($tree_id)
    {
        $group_chart = $this->getStockItemCommissionData();
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, $tree_id->stock_group_id, 0, 'stock_group_id', 'under', 'stock_item_id');

    }

    public function getTreeSelectOption()
    {
        $stock_group = $this->getStockItemCommissionData();
        $stock_group_object_to_array = json_decode(json_encode($stock_group, true), true);
        $build_group_tree = $this->tree->buildTree($stock_group_object_to_array, 0, 0, 'stock_group_id', 'under');

        return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'stock_group_id', 'under', 'group_chart_name');
    }

    public function getStockItemCommissionData()
    {

        $query = DB::table('stock_item')
            ->select('stock_group.stock_group_id', 'stock_group_name', 'under', 'stock_item.stock_item_id', 'product_name', 'stock_item_commission_setup.commission', 'stock_item_commission_setup.setup_date', 'stock_item_commission_setup.item_commission_id', 'stock_item_commission_setup.updated_history', 'stock_item_commission_setup.user_name')
            ->rightJoin('stock_group', 'stock_item.stock_group_id', '=', 'stock_group.stock_group_id')
            ->leftJoin('stock_item_commission_setup', 'stock_item.stock_item_id', '=', 'stock_item_commission_setup.stock_item_id');
        $data = $query->orderBy('stock_group.stock_group_id', 'DESC')
            ->orderBy('stock_item.stock_item_id', 'DESC')
            ->get();

        return $data;

    }
}
