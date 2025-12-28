<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherPurchaseReturnInterface
{
    
    public function StorePurchaseReturn(Request $request, $voucher_invoice);

    public function getPurchaseReturnId($id);

    public function updatePurchaseReturn(Request $request, $id, $voucher_invoice);

    public function deletePurchaseReturn($id);
}
