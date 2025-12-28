<?php

namespace App\Repositories\Backend\Master;

use App\Models\StockCommission;
use App\Services\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockCommissionRepository implements StockCommissionInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getStockCommissionOfIndex()
    {
        return $this->getStockCommissionData();
    }

    public function StoreStockCommission($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $StockCommission = new StockCommission();
        $StockCommission->stock_group_id = $request->stock_group_id;
        $StockCommission->setup_date = $request->setup_date;
        $StockCommission->commission = $request->commission;
        $StockCommission->user_id = Auth::id();
        $StockCommission->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.' Ip:'.$ip);
        $StockCommission->user_name = Auth::user()->user_name;
        $StockCommission->save();

        return $StockCommission;
    }

    public function getStockCommissionId($id)
    {
        return StockCommission::findOrFail($id);
    }

    public function updateStockCommission($request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $StockCommission = StockCommission::findOrFail($id);
        $StockCommission->stock_group_id = $request->stock_group_id;
        $StockCommission->setup_date = $request->setup_date;
        $StockCommission->commission = $request->commission;
        $StockCommission->user_id = Auth::id();
        $update_history = json_decode($StockCommission->other_details);
        $StockCommission->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By:'.Auth::user()->user_name.' Ip: '.$ip);
        $StockCommission->save();

        return $StockCommission;
    }

    public function deleteStockCommission($id)
    {

        return StockCommission::findOrFail($id)->delete();
    }

    public function getTree($tree_id)
    {

        $group_chart = $this->getStockCommissionData();

        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);
        $data = $this->tree->buildTree($group_chart_object_to_array, $tree_id->stock_group_id, 0, 'stock_group_id', 'under', 'stock_item_id');
        if (array_filter($data)) {
            return $data;
        } else {
            return $this->getStockCommissionData($tree_id->stock_group_id);
        }
    }

    public function getStockCommissionData($group_id = null)
    {
        $query = DB::table('stock_group')
            ->select('stock_group_commission.other_details', 'stock_group_commission.user_name', 'stock_group_commission.group_commission_id', 'stock_group.stock_group_id', 'stock_group.stock_group_name', 'stock_group.under', 'stock_group_commission.setup_date', 'stock_group_commission.commission')
            ->leftJoin('stock_group_commission', 'stock_group_commission.stock_group_id', '=', 'stock_group.stock_group_id');
        if (! empty($group_id)) {
            $query->where('stock_group.stock_group_id', $group_id);
        }

        return $query->orderBy('stock_group.stock_group_id', 'DESC')
            ->get();
    }
}
