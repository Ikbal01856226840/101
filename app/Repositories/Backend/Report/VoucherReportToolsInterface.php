<?php

namespace App\Repositories\Backend\Report;

interface VoucherReportToolsInterface
{
    public function getVouchers($request);
    public function getSearchVouchers($request);
    public function voucherModeChange($request);
}
