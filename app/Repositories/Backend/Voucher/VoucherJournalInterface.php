<?php

namespace App\Repositories\Backend\Voucher;

use Illuminate\Http\Request;

interface VoucherJournalInterface
{
    
    public function StoreJournal(Request $request, $voucher_invoice);

    public function getJournalId($id);

    public function updateJournal(Request $request, $id, $voucher_invoice);

    public function deleteJournal($id);
}
