<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface StockItemPriceInterface
{
    public function getStockItemPriceOfIndex();

    public function StockItemPrice(Request $request);

    public function getStockItemPriceId($id);

    public function updateStockItemPrice(Request $request, $id);

    public function deleteStockItemPrice($id);
}
