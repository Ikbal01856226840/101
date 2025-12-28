<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherTransferInterface
{
   
    public function StoreTransfer(Request $request, $voucher_invoice);

    public function getTransferId($id);

    public function updateTransfer(Request $request, $id, $voucher_invoice);

    public function deleteTransfer($id);
}
