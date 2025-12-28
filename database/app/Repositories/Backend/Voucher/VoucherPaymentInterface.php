<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherPaymentInterface
{
   
    public function StoreVoucherPayment(Request $request, $voucher_invoice);

    public function getVoucherPaymentId($id);

    public function updateVoucherPayment(Request $request, $id, $voucher_invoice);

    public function deleteVoucherPayment($id);
}
