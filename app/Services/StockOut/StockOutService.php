<?php

namespace App\Services\StockOut;

use App\Models\StockOut;

class StockOutService
{
    public function stockOutSum($tran_id)
    {
        return StockOut::where('tran_id', $tran_id)->sum('qty');
    }
}
