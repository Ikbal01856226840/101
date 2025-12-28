<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface VoucherInterface
{
    public function getVoucherOfIndex();

    public function StoreVoucher(Request $request);

    public function getVoucherId($id);

    public function updateVoucher(Request $request, $id);

    public function deleteVoucher($id);
}
