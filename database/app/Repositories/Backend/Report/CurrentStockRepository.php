<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class CurrentStockRepository implements CurrentStockInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getCurrentStockOfIndex($request = null)
    {
        return DB::table('stock')
            ->select('stock.inwards_qty', 'stock.inwards_rate', 'stock_item.product_name', 'stock.stock_item_id')
            ->leftJoin('stock_item', 'stock.stock_item_id', '=', 'stock_item.stock_item_id')
            ->get();
    }
}
