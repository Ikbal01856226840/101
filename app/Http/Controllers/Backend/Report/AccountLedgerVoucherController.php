<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountLedgerVoucherRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use Exception;
use Illuminate\Http\Request;

class AccountLedgerVoucherController extends Controller
{
    private $accountLedgerVoucherRepository;
    private $voucherRepository;

    public function __construct(AccountLedgerVoucherRepository $accountLedgerVoucherRepository, VoucherRepository $voucherRepository)
    {

        $this->accountLedgerVoucherRepository = $accountLedgerVoucherRepository;
        $this->voucherRepository = $voucherRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerVoucherShow()
    {
        if (user_privileges_check('report', 'LedgerVoucherList', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $narration=false;
            return view('admin.report.account_summary.account_voucher_list',compact('vouchers','narration'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerVoucher(Request $request)
    {
        if (user_privileges_check('report', 'LedgerVoucherList', 'display_role')) {
            try {
                $data = $this->accountLedgerVoucherRepository->getAccountLedgerVoucherOfIndex($request);
                $ledgerOpening = $this->accountLedgerVoucherRepository->getAccountLedgerOpeningBalance($request);
                return RespondWithSuccess('account ledger voucher  successfully !! ',
                [
                    'data' => $data,
                    'group_chart_nature'=>$ledgerOpening['group_chart_nature'],
                    'op_party_ledger'=>$ledgerOpening['op_party_ledger']
                ], 201);
            } catch (Exception $e) {
                return RespondWithError('account ledger  voucher not  successfully', $e->getMessage(), 400);
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
    public function accountLedgerVoucherIdWise(Request $request)
    {
        if (user_privileges_check('report', 'LedgerVoucherList', 'display_role')) {
            $ledger_id = $request->ledger_id;
            $form_date = $request->form_date;
            $to_date = $request->to_date;
            $voucher_id=$request->voucher_id??0;
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $narration=$request->narration??false;
            return view('admin.report.account_summary.account_voucher_list', compact('ledger_id', 'form_date', 'to_date','vouchers','voucher_id','narration'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerVoucherMonthWise(Request $request)
    {
        if (user_privileges_check('report', 'LedgerVoucherList', 'display_role')) {
            $ledger_id = $request->ledger_id;
            $narration = $request->narration;
            $date = $request->date;

            $month_year = date('Y-m', strtotime($date));
            if(date('Y-m', strtotime($request->from_date))==$month_year){
                $form_date =$request->from_date;
            }else{
                $form_date = "$month_year-01";
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
            $voucher_id=$request->voucher_id;
            $vouchers = $this->voucherRepository->voucher_specific_data();
            return view('admin.report.account_summary.account_voucher_list', compact('ledger_id', 'form_date', 'to_date','vouchers','voucher_id','narration'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerVoucherDayhWise(Request $request)
    {
        $vouchers = $this->voucherRepository->voucher_specific_data();
        if (user_privileges_check('report', 'LedgerVoucherList', 'display_role')) {
            $ledger_id = $request->ledger_id;
            $form_date = $request->date;
            $to_date =$request->date;
            $narration= $request->narration??false;
            return view('admin.report.account_summary.account_voucher_list', compact('ledger_id', 'form_date', 'to_date','vouchers','narration'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerVoucherDetails(Request $request)
    {
        
        if (user_privileges_check('report', 'LedgerVoucherList', 'display_role')) {
            $ledger_id = $request->ledger_id;
            $form_date = $request->from_date;
            $to_date = $request->to_date;
            $voucher_id=$request->voucher_id;
            $narration=$request->narration;
            $vouchers = $this->voucherRepository->voucher_specific_data();
            return view('admin.report.account_summary.account_voucher_list', compact('ledger_id', 'form_date', 'to_date','vouchers','voucher_id','narration'));
        } else {
            abort(403);
        }
    }
}
