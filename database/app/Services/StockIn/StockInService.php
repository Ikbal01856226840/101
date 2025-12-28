<?php

namespace App\Services\StockIn;

use App\Models\StockIn;

class StockInService
{
    public function StockInStore($tran_id, $transaction_date, $product_id, $godown_id, $qty, $rate, $amount, $remark = null)
    {
        $stock_in_data = new StockIn();
        $stock_in_data->tran_id = $tran_id ?? exit;
        $stock_in_data->tran_date = $transaction_date;
        $stock_in_data->stock_item_id = $product_id;
        $stock_in_data->godown_id = $godown_id ?? 0;
        $stock_in_data->qty = (int) $qty ?? 0;
        $stock_in_data->rate = (float) $rate ?? 0;
        $stock_in_data->total = (float) $amount ?? 0;
        $stock_in_data->remark = $remark ?? '';
        $stock_in_data->save();

        return $stock_in_data;
    }

    public function StockInUpdate($stock_in_id, $tran_id, $transaction_date, $product_id, $godown_id, $qty, $rate, $amount, $remark = null)
    {
        return StockIn::where('stock_in_id', $stock_in_id)->update([
            'tran_date' => $transaction_date,
            'godown_id' => $godown_id ?? 0,
            'stock_item_id' => $product_id ?? 0,
            'qty' => (int) $qty ?? 0,
            'rate' => (float) $rate ?? 0,
            'total' => (float) $amount ?? 0,
            'remark' => $remark ?? '',
        ]);

    }

    public function stockInSum($tran_id)
    {
        return StockIn::where('tran_id', $tran_id)->sum('qty');
    }
}
