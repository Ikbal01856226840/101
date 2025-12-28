<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherPurchaseInterface
{
    
    public function StorePurchase(Request $request, $voucher_invoice);

    public function getPurchaseId($id);

    public function updatePurchase(Request $request, $id, $voucher_invoice);

    public function deletePurchase($id);
}
