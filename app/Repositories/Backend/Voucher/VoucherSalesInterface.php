<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherSalesInterface
{

    public function StoreSales(Request $request, $voucher_invoice);

    public function getSalesId($id);

    public function updateSales(Request $request, $id, $voucher_invoice);

    public function deleteSales($id);
}
