<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherPOSInterface
{
    public function StorePOS(Request $request, $voucher_invoice);

    public function getPOSId($id);

    public function updatePOS(Request $request, $id, $voucher_invoice);

    public function deletePOS($id);
}
