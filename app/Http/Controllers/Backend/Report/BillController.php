<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\BillRepository;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;

class BillController extends Controller
{
    private $voucherRepository;

    private $billRepository;

    private $voucher_setup;

    public function __construct(VoucherRepository $voucherRepository, BillRepository $billRepository,Voucher_setup $voucher_setup)
    {
        $this->voucherRepository = $voucherRepository;
        $this->billRepository = $billRepository;
        $this->voucher_setup = $voucher_setup;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('report', 'Bill', 'display_role')) {
            $vouchers = $this->voucher_setup->AccessVoucherSetup();
            return view('admin.report.approved.bill', compact('vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBill(Request $request)
    {
        if (user_privileges_check('report', 'Bill', 'display_role')) {
            try {
                $data = $this->billRepository->getBillOfIndex($request);

                return RespondWithSuccess('bill show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('bill not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
