<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherPOSExchangeInterface
{
   
    public function StorePOSExchange(Request $request, $voucher_invoice);

    public function getPOSExchangeId($id);

    public function updatePOSExchange(Request $request, $id, $voucher_invoice);

    public function deletePOSExchange($id);

    public function POSExchangeInvoiceGetData($id);
}
