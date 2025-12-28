<?php

namespace App\Repositories\Backend\Master;

use App\Models\StockItem;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockItemRepository implements StockItemInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getStockItemOfIndex()
    {
        return DB::select('SELECT stock_item.bangla_product_name,stock_item.other_details,stock_item.user_name,stock_item.alias, stock_item.stock_item_id ,stock_item.stock_group_id, stock_item.product_name,stock_item.unit_of_measure_id,stock_group.stock_group_name,unitsof_measure.symbol FROM stock_item LEFT JOIN stock_group ON stock_group.stock_group_id=stock_item.stock_group_id LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id ORDER BY stock_item.product_name ASC');
    }

    public function storeStockItem($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new StockItem();
        $data->stock_group_id = $request->stock_group_id;
        $data->product_name = $request->product_name;
        $data->bangla_product_name = $request->bangla_product_name;
        $data->unit_of_measure_id = $request->unit_of_measure_id;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->alias = $request->alias;
        $data->rateofduty = $request->rateofduty;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getStockItemId($id)
    {
        return StockItem::where('stock_item_id', $id)->first();
    }

    public function updateStockItem(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = StockItem::findOrFail($id);
        $data->stock_group_id = $request->stock_group_id;
        $data->product_name = $request->product_name;
        $data->bangla_product_name = $request->bangla_product_name;
        $data->unit_of_measure_id = $request->unit_of_measure_id;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->rateofduty = $request->rateofduty;
        $data->alias = $request->alias;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->save();

        return $data;
    }

    public function deleteStockItem($id)
    {
        DB::table('stock')->where('stock_item_id', $id)->delete();

        return StockItem::where('stock_item_id', $id)->delete();
    }

    public function getTreeItem()
    {
        $stock_group = $this->getStockItemData();

        $stock_group_object_to_array = json_decode(json_encode($stock_group, true), true);

        return $this->tree->buildTree($stock_group_object_to_array, 0, 0, 'stock_group_id', 'under');
    }

    public function getStockItemData()
    {
        return DB::select('SELECT stock_item.bangla_product_name,stock_item.other_details,stock_item.user_name,stock_item.alias, 
                                        stock_item.stock_item_id ,stock_item.stock_group_id, stock_item.product_name,
                                        stock_item.unit_of_measure_id,unitsof_measure.symbol,stock_group.stock_group_id,
                                        stock_group.stock_group_name,stock_group.under 
                                        FROM stock_group  
                                        LEFT JOIN stock_item ON stock_item.stock_group_id=stock_group.stock_group_id 
                                        LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id ORDER BY stock_group.stock_group_name DESC');
    }

    public function getSpecificItemData()
    {
        $stock_group = DB::table('stock_group')->select('stock_group.stock_group_id', 'stock_group.stock_group_name', 'stock_group.under', 'stock_item.stock_item_id', 'stock_item.product_name')->leftJoin('stock_item', 'stock_group.stock_group_id', '=', 'stock_item.stock_group_id')->orderBy('stock_group.stock_group_id', 'DESC')->get();
        $stock_group_object_to_array = json_decode(json_encode($stock_group, true), true);

        return $this->tree->buildTree($stock_group_object_to_array, 1, 0, 'stock_group_id', 'under');

    }
}
