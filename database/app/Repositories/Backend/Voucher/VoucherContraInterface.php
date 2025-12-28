<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherContraInterface
{
   
    public function StoreVoucherContra(Request $request, $voucher_invoice);

    public function getVoucherContraId($id);

    public function updateVoucherContra(Request $request, $id, $voucher_invoice);

    public function deleteVoucherContra($id);
}
