<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\VoucherFilterAnalysisRepository;
use Exception;
use Illuminate\Http\Request;

class VoucherFilterAnalysisController extends Controller
{
    private $voucherFilterAnalysisRepository;
    private $voucherRepository;

    public function __construct(VoucherFilterAnalysisRepository $voucherFilterAnalysisRepository,VoucherRepository $voucherRepository)
    {

        $this->voucherFilterAnalysisRepository = $voucherFilterAnalysisRepository;
        $this->voucherRepository=$voucherRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function voucherFilterAnalysisShow()
    {
        
            $vouchers = $this->voucherRepository->voucher_specific_data();
            return view('admin.report.general.voucher_filter_analysis',compact('vouchers'));
       
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function voucherFilterAnalysis(Request $request)
    {
       
            try {
                $data = $this->voucherFilterAnalysisRepository->getVoucherFilterAnalysisOfIndex($request);

                return RespondWithSuccess('trial balance  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('trial balance successfully', $e->getMessage(), 400);
            }
        
    }
}
