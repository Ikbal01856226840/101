<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Report\VoucherMonthlySummaryRepository;
use Exception;
use Illuminate\Http\Request;

class VoucherMonthlySummaryController extends Controller
{
    private $voucherMonthlySummaryRepository;

    private $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository,VoucherMonthlySummaryRepository $voucherMonthlySummaryRepository)
    {
        $this->voucherRepository = $voucherRepository;
        $this->voucherMonthlySummaryRepository =$voucherMonthlySummaryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function voucherMonthlySummaryShow()
    {
        if (user_privileges_check('report', 'VoucherMonthlySummary', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();

            return view('admin.report.company_statistics.voucher_monthly_summary', compact('vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function voucherMonthlySummary(Request $request)
    {
        if (user_privileges_check('report', 'VoucherMonthlySummary', 'display_role')) {
            try {
                $data = $this->voucherMonthlySummaryRepository->getVoucherMonthlySummaryOfIndex($request);
                return RespondWithSuccess('voucher  monthly summary  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('voucher monthly summary  successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
    
    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyStatisticsMonthly(Request $request)
    {
        if (user_privileges_check('report', 'VoucherMonthlySummary', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $voucher_id=$request->voucher_id;
            $from_date=$request->form_date;
            $to_date=$request->to_date;
            return view('admin.report.company_statistics.voucher_monthly_summary', compact('vouchers','voucher_id','from_date','to_date'));
        } else {
            abort(403);
        }
    }

    

}
