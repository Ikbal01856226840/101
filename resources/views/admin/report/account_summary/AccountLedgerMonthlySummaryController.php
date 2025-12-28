<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountLedgerMonthlySummaryRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use Exception;
use Illuminate\Http\Request;

class AccountLedgerMonthlySummaryController extends Controller
{
    private $accountLedgerMonthlySummaryRepository;
    private $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository,AccountLedgerMonthlySummaryRepository $accountLedgerMonthlySummaryRepository)
    {
        $this->voucherRepository = $voucherRepository;
        $this->accountLedgerMonthlySummaryRepository = $accountLedgerMonthlySummaryRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerMonthlySummaryShow()
    {
        if (user_privileges_check('report', 'LedgerMonthly', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();
            return view('admin.report.account_summary.account_ledger_monthly_summary',compact('vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerMonthlySummary(Request $request)
    {
        if (user_privileges_check('report', 'LedgerMonthly', 'display_role')) {
            try {
                $data = $this->accountLedgerMonthlySummaryRepository->getAccountLedgerMonthlySummaryOfIndex($request);

                return RespondWithSuccess('account ledger monthly summary  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account ledger monthly not  successfully', $e->getMessage(), 400);
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
    public function accountLedgerMonthlySummaryIdWise(Request $request)
    {
        if (user_privileges_check('report', 'LedgerMonthly', 'display_role')) {
            $ledger_id = $request->ledger_id;
            $form_date = $request->form_date;
            $to_date = $request->to_date;
            $voucher_id= $request->voucher_id??0;
            $vouchers = $this->voucherRepository->voucher_specific_data();
            return view('admin.report.account_summary.account_ledger_monthly_summary', compact('form_date', 'to_date', 'ledger_id','vouchers','voucher_id'));
        } else {
            abort(403);
        }
    }
}
