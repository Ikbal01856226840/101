<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherOrderRequisitionInterface
{

    public function storeOrderRequisition(Request $request, $voucher_invoice);

    public function getOrderRequisitionId($id);

    public function updateOrderRequisition(Request $request, $id, $voucher_invoice);

    public function deleteOrderRequisition($id);
}
