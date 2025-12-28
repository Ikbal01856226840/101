<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Voucher\VoucherDashboardRepository;

class DashboardController extends Controller
{
    private $voucher;

    public function __construct(VoucherDashboardRepository $voucherDashboardRepository)
    {
        $this->voucher = $voucherDashboardRepository;
    }

    public function index()
    {
        $purchases = $this->voucher->getVoucherOfIndex(10);
        $receives = $this->voucher->getVoucherOfIndex(14);
        $grns = $this->voucher->getVoucherOfIndex(24);
        $sales_return = $this->voucher->getVoucherOfIndex(25);
        $gtns = $this->voucher->getVoucherOfIndex(23);
        $payments = $this->voucher->getVoucherOfIndex(8);
        $sales = $this->voucher->getVoucherOfIndex(19);
        $purchase_returns = $this->voucher->getVoucherOfIndex(29);
        $adjustments = $this->voucher->getVoucherOfIndex(22);
        $contra = $this->voucher->getVoucherOfIndex(1);
        $jv = $this->voucher->getVoucherOfIndex(21);
        $journals = $this->voucher->getVoucherOfIndex(6);
        $commissions = $this->voucher->getVoucherOfIndex(28);
        $sales_orders = $this->voucher->getVoucherOfIndex(20);
        $sales_pos=$this->voucher->getVoucherOfIndex(30);
        $sales_exchanges=$this->voucher->getVoucherOfIndex(27);
      
        return view('admin.voucher.voucher_dashboard', compact('purchases', 'receives', 'grns', 'sales_return', 'payments', 'sales', 'purchase_returns', 'adjustments', 'contra', 'jv', 'gtns', 'journals', 'commissions','sales_orders','sales_pos','sales_exchanges'));
    }
}
