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
        if (user_privileges_check('report', 'companyVoucherRegister', 'display_role')) {
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
        if (user_privileges_check('report', 'companyVoucherRegister', 'display_role')) {
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

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function VoucherMonthWise(Request $request)
    {
        if (user_privileges_check('report', 'companyVoucherRegister', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $voucher_id = $request->voucher_id;
            $date =date('Y-m-d', strtotime($request->date)) ;
            $month_year = date('Y-m', strtotime($date));
           
            if(date('Y-m',strtotime($request->from_date))==$month_year){
                $from_date =$request->from_date;
            }else{
                $from_date = "$month_year-01";
            }

            
            $month = date('Y-m-d', strtotime($month_year));
            $to_day = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date)), date('Y', strtotime($date)));

            if(date('Y-m', strtotime($request->to_date))==$month_year){
                $to_date =$request->to_date;
            }else{
                if (date('m') == date('m', strtotime($date))) {
                    $current_day = date('d');
                    $to_date = "$month_year-$current_day";
                } else {
                    $to_date = "$month_year-$to_day";
                }
            }
            return view('admin.report.company_statistics.voucher_register', compact('vouchers','voucher_id','from_date','to_date'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function VoucherMonthWiseDetails(Request $request)
    {
        if (user_privileges_check('report', 'companyVoucherRegister', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $voucher_id = $request->voucher_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $debit=$request->debit;
            $credit=$request->credit;
            $narration=$request->narratiaon;
            $sort_by=$request->sort_by;
            return view('admin.report.company_statistics.voucher_register', compact('vouchers','voucher_id','from_date','to_date','debit','credit','narration','sort_by'));
        } else {
            abort(403);
        }
    }
}
