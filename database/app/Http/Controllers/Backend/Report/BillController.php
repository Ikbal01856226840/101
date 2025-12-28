<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\BillRepository;
use Exception;
use Illuminate\Http\Request;

class BillController extends Controller
{
    private $voucherRepository;

    private $billRepository;

    public function __construct(VoucherRepository $voucherRepository, BillRepository $billRepository)
    {
        $this->voucherRepository = $voucherRepository;
        $this->billRepository = $billRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('report', 'Bill', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();

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
