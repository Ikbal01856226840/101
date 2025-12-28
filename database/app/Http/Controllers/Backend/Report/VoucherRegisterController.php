<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\VoucherRegisterRepository;
use Exception;
use Illuminate\Http\Request;

class VoucherRegisterController extends Controller
{
    private $voucherRegisterRepository;

    private $voucherRepository;

    public function __construct(VoucherRegisterRepository $voucherRegisterRepository, VoucherRepository $voucherRepository)
    {
        $this->voucherRegisterRepository = $voucherRegisterRepository;
        $this->voucherRepository = $voucherRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function VoucherRegisterShow()
    {
        if (user_privileges_check('report', 'VoucherListsStatistics', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();

            return view('admin.report.company_statistics.voucher_register', compact('vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVoucherRegister(Request $request)
    {
        if (user_privileges_check('report', 'VoucherListsStatistics', 'display_role')) {
            try {
                $data = $this->voucherRegisterRepository->getVoucherRegisterOfIndex($request);

                return RespondWithSuccess('Voucher Register show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Register show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
