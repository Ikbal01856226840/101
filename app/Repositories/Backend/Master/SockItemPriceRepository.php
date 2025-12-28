<?php

namespace App\Repositories\Backend\Master;

use App\Models\SockItemPrice;
use App\Services\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SockItemPriceRepository implements StockItemPriceInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getStockItemPriceOfIndex()
    {
        return $this->getStockItemPriceData();
    }

    public function StockItemPrice($request)
    {
        //         $data=DB::select("SELECT stock_item_old.*,selling_price.*,
        //                             ( SELECT s_p.rate FROM selling_price AS s_p 
        //                                 WHERE s_p.stock_item_id=selling_price.stock_item_id   
        //                                 ORDER BY selling_price_id DESC LIMIT 1  ) AS  new_selling_price_rate
        //                         FROM stock_item_old INNER JOIN selling_price ON selling_price.stock_item_id=stock_item_old.stock_item_id  GROUP BY selling_price.stock_item_id");;
        //     // dd($data);
        //        foreach($data as $stock_out_id){
        //              $data1=DB::table('stock_item')->where('product_name',trim($stock_out_id->product_name))->first();
        //              if(!empty($data1)){
        //                 $stockItemPrice = new SockItemPrice();
        //                 $stockItemPrice->price_type = 1;
        //                 $stockItemPrice->stock_item_id =$data1->stock_item_id;
        //                 $stockItemPrice->setup_date =date("Y-m-d");
        //                 $stockItemPrice->rate = $stock_out_id->new_selling_price_rate;
        //                 $stockItemPrice->user_id = Auth::id();
        //                 $stockItemPrice->user_name = Auth::user()->user_name;
        //                 $stockItemPrice->updated_history = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.'Selling Price: '.$request->rate);
        //                 $stockItemPrice->save();
        //              }

        //         }

        //     $data=DB::select("SELECT stock_item_old.*,standard_price.*,
        //                         ( SELECT s_p.rate FROM standard_price AS s_p 
        //                             WHERE s_p.stock_item_id=standard_price.stock_item_id   
        //                             ORDER BY standard_price_id DESC LIMIT 1  ) AS  new_selling_price_rate
        //                     FROM stock_item_old INNER JOIN standard_price ON standard_price.stock_item_id=stock_item_old.stock_item_id  GROUP BY standard_price.stock_item_id");;
        // // dd($data);
        //    foreach($data as $stock_out_id){
        //          $data1=DB::table('stock_item')->where('product_name',trim($stock_out_id->product_name))->first();
        //          if(!empty($data1)){
        //             $stockItemPrice = new SockItemPrice();
        //             $stockItemPrice->price_type = 2;
        //             $stockItemPrice->stock_item_id =$data1->stock_item_id;
        //             $stockItemPrice->setup_date =date("Y-m-d");
        //             $stockItemPrice->rate = $stock_out_id->new_selling_price_rate;
        //             $stockItemPrice->user_id = Auth::id();
        //             $stockItemPrice->user_name = Auth::user()->user_name;
        //             $stockItemPrice->updated_history = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.'Selling Price: '.$request->rate);
        //             $stockItemPrice->save();
        //          }

        //     }

        $stockItemPrice = new SockItemPrice();
        $stockItemPrice->price_type = $request->price_type;
        $stockItemPrice->stock_item_id = $request->stock_item_id;
        $stockItemPrice->setup_date = $request->setup_date;
        $stockItemPrice->rate = $request->rate;
        $stockItemPrice->user_id = Auth::id();
        $stockItemPrice->user_name = Auth::user()->user_name;
        $stockItemPrice->updated_history = json_encode('Created On: ' . \Carbon\Carbon::now()->format('D, d M Y g:i:s A') . ' By:' . Auth::user()->user_name . 'Selling Price: ' . $request->rate);
        $stockItemPrice->save();

        return $stockItemPrice;
    }

    public function getStockItemPriceId($id)
    {
        return SockItemPrice::where('price_id', $id)->first();
    }

    public function updateStockItemPrice($request, $id)
    {
        $stockItemPrice = SockItemPrice::findOrFail($id);
        $stockItemPrice->stock_item_id = $request->stock_item_id;
        $stockItemPrice->setup_date = $request->setup_date;
        $data = json_decode($stockItemPrice->updated_history);
        $stockItemPrice->rate = $request->rate;
        $stockItemPrice->updated_history = json_encode($data . '<br> Updated On: ' . \Carbon\Carbon::now()->format('D, d M Y g:i:s A') . ' By:' . Auth::user()->user_name . ' Selling Price: ' . $request->rate);
        $stockItemPrice->save();

        return $stockItemPrice;
    }

    public function deleteStockItemPrice($id)
    {
        return SockItemPrice::where('price_id', $id)->delete();
    }

    public function getTree($tree_id)
    {

        $group_chart = $this->getStockItemPriceData($tree_id->price_type_id);
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, $tree_id->stock_group_id, 0, 'stock_group_id', 'under', 'stock_item_id');
    }

    public function getTreeSelectOption()
    {
        $stock_group = $this->getStockItemPriceData();
        $stock_group_object_to_array = json_decode(json_encode($stock_group, true), true);
        $build_group_tree = $this->tree->buildTree($stock_group_object_to_array, 0, 0, 'stock_group_id', 'under');

        return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'stock_group_id', 'under', 'group_chart_name');
    }

    public function getStockItemPriceData($price_type_id = 0)
    {

        $query = DB::table('stock_item')
            ->select('stock_group.stock_group_id', 'stock_group_name', 'under', 'stock_item.stock_item_id', 'product_name', 'price_setup.price_id', 'rate', 'setup_date')
            ->rightJoin('stock_group', 'stock_item.stock_group_id', '=', 'stock_group.stock_group_id')
            ->leftJoin('price_setup', 'stock_item.stock_item_id', '=', DB::raw('price_setup.stock_item_id AND price_setup.price_type=' . $price_type_id));
        $query->select('stock_group.stock_group_id', 'stock_group_name', 'under', 'stock_item.stock_item_id', 'product_name', 'price_setup.price_id', 'rate', 'setup_date', 'updated_history', 'price_setup.user_name');
        $data = $query->orderBy('stock_group.stock_group_name', 'DESC')
            ->orderBy('stock_item.product_name', 'DESC')
            ->orderBy('price_setup.price_id', 'DESC')
            ->get();

        return $data;
    }
}
