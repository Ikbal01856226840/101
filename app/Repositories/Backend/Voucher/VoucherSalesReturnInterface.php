<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherSalesReturnInterface
{
    
    public function StoreSalesReturn(Request $request, $voucher_invoice);

    public function getSalesReturnId($id);

    public function updateSalesReturn(Request $request, $id, $voucher_invoice);

    public function deleteSalesReturn($id);
}
