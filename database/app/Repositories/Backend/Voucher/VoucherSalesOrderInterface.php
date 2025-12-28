<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherSalesOrderInterface
{

    public function StoreVoucherSalesOrder(Request $request, $voucher_invoice);

    public function getVoucherSalesOrderId($id);

    public function updateVoucherSalesOrder(Request $request, $id, $voucher_invoice);

    public function deleteVoucherSalesOrder($id);
}
