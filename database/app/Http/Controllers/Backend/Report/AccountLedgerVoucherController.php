<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountLedgerVoucherRepository;
use Exception;
use Illuminate\Http\Request;

class AccountLedgerVoucherController extends Controller
{
    private $accountLedgerVoucherRepository;

    public function __construct(AccountLedgerVoucherRepository $accountLedgerVoucherRepository)
    {

        $this->accountLedgerVoucherRepository = $accountLedgerVoucherRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerVoucherShow()
    {
        if (user_privileges_check('report', 'LedgerVoucherList', 'display_role')) {
            return view('admin.report.account_summary.account_voucher_list');
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

                return RespondWithSuccess('account ledger voucher  successfully !! ', $data, 201);
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

            return view('admin.report.account_summary.account_voucher_list', compact('ledger_id', 'form_date', 'to_date'));
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
            $date = $request->date;

            $month_year = date('Y-m', strtotime($date));
            $form_date = "$month_year-01";

            $month = date('Y-m-d', strtotime($month_year));
            $to_day = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date)), date('Y', strtotime($date)));
            if (date('m') == date('m', strtotime($date))) {
                $current_day = date('d');
                $to_date = "$month_year-$current_day";
            } else {
                $to_date = "$month_year-$to_day";
            }

            return view('admin.report.account_summary.account_voucher_list', compact('ledger_id', 'form_date', 'to_date'));
        } else {
            abort(403);
        }
    }
}
