<?php

namespace App\Repositories\Backend\Report;

interface VoucherExchangeInterface
{
    public function AccessVoucherSetup();
    public function getVouchers($request);
    public function getSearchVouchers($request);
}
