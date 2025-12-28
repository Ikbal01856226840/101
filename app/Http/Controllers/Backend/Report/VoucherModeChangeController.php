<?php
namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Services\Voucher_setup\Voucher_setup;
use App\Repositories\Backend\Report\VoucherReportToolsRepository;
use App\Repositories\Backend\Master\VoucherRepository;

class VoucherModeChangeController extends Controller
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
        if (user_privileges_check('report', 'VoucherModeChange', 'display_role')) {
            $vouchers = $this->voucher_setup->AccessVoucherSetup();
            return view('admin.report.general.voucher_mode_change', compact('vouchers'));
        } else {
            abort(403);
        }
    }

    public function voucherModeChange(Request $request)
    {
        if (user_privileges_check('report', 'VoucherModeChange', 'display_role')) {
            try {
                $voucher=$this->voucherRepository->getVoucherId($request->voucher_id);

                $transaction = $this->voucherReportToolsRepository->getVouchers($request);
                $data = ['transaction' => $transaction,'voucher'=>$voucher];
                return RespondWithSuccess('Voucher Mode Change show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Error', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    public function voucherModeChangeStore(Request $request)
    {
        if (user_privileges_check('report', 'VoucherModeChange', 'display_role')) {
            try {
                $data=$this->voucherReportToolsRepository->voucherModeChange($request);
                if($data){
                    return RespondWithSuccess('Voucher Mode Change store successfully !! ', null, 201);
                }
                return RespondWithError('Voucher Mode Change store not successfully !! ', null, 400);
            } catch (Exception $e) {
                return RespondWithError('Error', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

}
