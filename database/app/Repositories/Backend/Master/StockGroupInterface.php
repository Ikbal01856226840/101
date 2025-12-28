<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface StockGroupInterface
{
    public function getStockGroupOfIndex();

    public function storeStockGroup(Request $request);

    public function getStockGroupId($id);

    public function updateStockGroup(Request $request, $id);

    public function deleteStockGroup($id);
}
