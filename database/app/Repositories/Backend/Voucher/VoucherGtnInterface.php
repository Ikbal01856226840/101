<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherGtnInterface
{

    public function StoreGtn(Request $request, $voucher_invoice);

    public function getGtnId($id);

    public function updateGtn(Request $request, $id, $voucher_invoice);

    public function deleteGtn($id);
}
