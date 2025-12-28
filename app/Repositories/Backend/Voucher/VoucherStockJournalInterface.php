<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherStockJournalInterface
{

    public function StoreStockJournal(Request $request, $voucher_invoice);

    public function getStockJournalId($id);

    public function updateStockJournal(Request $request, $id, $voucher_invoice);

    public function deleteStockJournal($id);
}
