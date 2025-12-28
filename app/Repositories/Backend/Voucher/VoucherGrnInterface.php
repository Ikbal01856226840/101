<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherGrnInterface
{
   
    public function StoreGrn(Request $request, $voucher_invoice);

    public function getGrnId($id);

    public function updateGrn(Request $request, $id, $voucher_invoice);

    public function deleteGrn($id);
}
