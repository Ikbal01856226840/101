<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherCommissionInterface
{

    public function StoreVoucherCommission(Request $request, $voucher_invoice);

    public function getVoucherCommissionId($id);

    public function updateVoucherCommission(Request $request, $id, $voucher_invoice);

    public function deleteVoucherCommission($id);
}
