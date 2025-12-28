<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Services\Voucher_setup\Voucher_setup;
use App\Repositories\Backend\Report\VoucherReportToolsRepository;
use App\Repositories\Backend\Master\VoucherRepository;
class VoucherSearchController extends Controller
{
    private $voucher_setup;
    private $voucherReportToolsRepository;
    private $voucherRepository;
    public function __construct(Voucher_setup $voucher_setup,VoucherReportToolsRepository $voucherReportToolsRepository,VoucherRepository $voucherRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->voucherReportToolsRepository = $voucherReportToolsRepository;
        $this->voucherRepository = $voucherRepository;
    }

    public function index()
    {
        if (user_privileges_check('report', 'VoucherSearch', 'display_role')) {
            $vouchers = $this->voucher_setup->AccessVoucherSetup();
            return view('admin.report.general.voucher_search', compact('vouchers'));
        } else {
            abort(403);
        }
    }
    public function voucherSearch(Request $request)
    {
        if (user_privileges_check('report', 'VoucherSearch', 'display_role')) {
            try {
                // dd($request->all());
                // $voucher=$this->voucherRepository->getVoucherId($request->voucher_id);

                $transaction = $this->voucherReportToolsRepository->getSearchVouchers($request);
                $data = [
                    'transaction' => $transaction,
                    // 'voucher'=>$voucher
                ];
                return RespondWithSuccess('Voucher Search show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Error', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
