<?php

namespace App\Repositories\Backend\Master;

use App\Models\StockGroupPrice;
use App\Services\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockGroupPriceRepository implements StockGroupPriceInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function StoreStockGroupPrice($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stockGroupPrice = new StockGroupPrice();
        $stockGroupPrice->price_type = (int) $request->price_type;
        $stockGroupPrice->stock_group_id = $request->stock_group_id;
        $stockGroupPrice->setup_date = $request->setup_date;
        $stockGroupPrice->rate = (float) $request->rate;
        $stockGroupPrice->user_id = Auth::id();
        $stockGroupPrice->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $stockGroupPrice->user_name = Auth::user()->user_name;
        $stockGroupPrice->save();

        return $stockGroupPrice;
    }

    public function getStockGroupPriceId($id)
    {
        return StockGroupPrice::findOrFail($id);
    }

    public function updateStockGroupPrice($request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stockGroupPrice = StockGroupPrice::findOrFail($id);
        $stockGroupPrice->price_type = (int) $request->price_type;
        $stockGroupPrice->stock_group_id = $request->stock_group_id;
        $stockGroupPrice->setup_date = $request->setup_date;
        $stockGroupPrice->rate = (float) $request->rate;
        $update_history = json_decode($stockGroupPrice->other_details);
        $stockGroupPrice->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $stockGroupPrice->save();

        return $stockGroupPrice;
    }

    public function deleteStockGroupPrice($id)
    {
        return StockGroupPrice::findOrFail($id)->delete();
    }

    public function getTree($tree_id)
    {

        $group_chart = $this->getStockGroupPriceData($tree_id->price_type_id);
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, $tree_id->stock_group_id, 0, 'stock_group_id', 'under');

    }

    public function getStockGroupPriceData($price_type_id)
    {

        return DB::table('stock_group')
            ->select('stock_group_price.other_details', 'stock_group_price.user_name', 'stock_group_price.group_price_id', 'stock_group.stock_group_id', 'stock_group.stock_group_name', 'stock_group.under', 'stock_group_price.setup_date', 'stock_group_price.rate')
            ->leftJoin('stock_group_price', 'stock_group.stock_group_id', '=', DB::raw('stock_group_price.stock_group_id AND stock_group_price.price_type='.$price_type_id))
            ->orderBy('stock_group.stock_group_id', 'DESC')
            ->get();
    }
}
