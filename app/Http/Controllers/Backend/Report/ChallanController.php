<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Report\ChallanRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Services\Voucher_setup\Voucher_setup;
use App\Services\User\UserCheck;

use Exception;
use Illuminate\Http\Request;

class ChallanController extends Controller
{
    private $godownRepository;

    private $challanRepository;

    private $voucherRepository;

    private $userCheck;

    private $voucher_setup;

    

    public function __construct(GodownRepository $godownRepository, ChallanRepository $challanRepository,VoucherRepository $voucherRepository,UserCheck $userCheck,Voucher_setup $voucher_setup)
    {
        $this->godownRepository = $godownRepository;
        $this->challanRepository = $challanRepository;
        $this->voucherRepository = $voucherRepository;
        $this->voucher_setup = $voucher_setup;
        $this->userCheck=$userCheck;
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('report', 'Challan', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $vouchers = $this->voucher_setup->AccessVoucherSetup();
            $distributions= $this->userCheck->AccessDistributionCenter();

            return view('admin.report.approved.challan', compact('distributions','vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getChallan(Request $request)
    {
        if (user_privileges_check('report', 'Challan', 'display_role')) {
            try {
                $data = $this->challanRepository->getChallanfIndex($request);

                return RespondWithSuccess('challan show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('challan not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
