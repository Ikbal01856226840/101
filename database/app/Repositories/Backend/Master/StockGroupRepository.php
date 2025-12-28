<?php

namespace App\Repositories\Backend\Master;

use App\Models\StockGroup;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockGroupRepository implements StockGroupInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getStockGroupOfIndex()
    {
        return StockGroup::select('stock_group_id', 'stock_group_name', 'alias', 'under', 'other_details', 'user_name')->orderBy('stock_group_name', 'DESC')->get();

    }

    public function storeStockGroup(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new StockGroup();
        $data->stock_group_name = $request->stock_group_name;
        $data->under = $request->under;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->item_add = $request->item_add;
        $data->alias = $request->alias;
        $data->group_category = $request->group_category;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getStockGroupId($id)
    {
        return StockGroup::find($id);
    }

    public function updateStockGroup(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = StockGroup::findOrFail($id);
        $data->stock_group_name = $request->stock_group_name;
        $data->under = $request->under;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->item_add = $request->item_add;
        $data->alias = $request->alias;
        $data->group_category = $request->group_category;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->save();

        return $data;

    }

    public function deleteStockGroup($id)
    {
        return StockGroup::findOrFail($id)->delete();
    }

    public function getTreeStockGroup()
    {
        $group_chart = $this->getStockGroupOfIndex();
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under');
    }

    public function getTreeSelectOption($under_id = null)
    {
        $group_chart = $this->getStockGroupOfIndex();
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);
        $build_group_tree = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under');

        return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'stock_group_id', 'under', 'stock_group_name', $under_id);
    }
}
