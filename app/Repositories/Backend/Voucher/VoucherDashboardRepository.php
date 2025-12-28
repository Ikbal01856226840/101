<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\Voucher;

class VoucherDashboardRepository implements VoucherDashboardInterface
{
    public function getVoucherOfIndex($id)
    {
        return Voucher::select('voucher_type_id', 'voucher_name', 'voucher_id')->with('voucher_type:voucher_type_id,voucher_type:voucher_type')->where('voucher_type_id', $id)->get();
    }
}
