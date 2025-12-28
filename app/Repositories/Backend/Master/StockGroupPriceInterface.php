<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface StockGroupPriceInterface
{
    // public function getStockGroupPriceOfIndex();

    public function StoreStockGroupPrice(Request $request);

    public function getStockGroupPriceId($id);

    public function updateStockGroupPrice(Request $request, $id);

    public function deleteStockGroupPrice($id);
}
