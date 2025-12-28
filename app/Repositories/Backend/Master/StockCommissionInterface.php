<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface StockCommissionInterface
{
    public function getStockCommissionOfIndex();

    public function StoreStockCommission(Request $request);

    public function getStockCommissionId($id);

    public function updateStockCommission(Request $request, $id);

    public function deleteStockCommission($id);
}
