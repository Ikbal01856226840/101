<?php

namespace App\Repositories\Backend\Report;

interface AccountLedgerVoucherInterface
{
    public function getAccountLedgerVoucherOfIndex($request);
    public function getAccountLedgerOpeningBalance($request);
}
