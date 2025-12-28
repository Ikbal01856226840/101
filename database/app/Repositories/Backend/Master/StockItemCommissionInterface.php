<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface StockItemCommissionInterface
{
    public function getStockItemCommissionOfIndex();

    public function StoreStockItemCommission(Request $request);

    public function getStockItemCommissionId($id);

    public function updateStockItemCommission(Request $request, $id);

    public function deleteStockItemCommission($id);
}
