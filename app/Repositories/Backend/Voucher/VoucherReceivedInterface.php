<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherReceivedInterface
{
   
    public function StoreVoucherReceived(Request $request, $voucher_invoice);

    public function getVoucherReceivedId($id);

    public function updateVoucherReceived(Request $request, $id, $voucher_invoice);

    public function deleteVoucherReceived($id);
}
