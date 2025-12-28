<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface StockItemInterface
{
    public function getStockItemOfIndex();

    public function StoreStockItem(Request $request);

    public function getStockItemId($id);

    public function updateStockItem(Request $request, $id);

    public function deleteStockItem($id);
}
