<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountLedgerDailySummaryRepository;
use Exception;
use Illuminate\Http\Request;

class AccountLedgerDailySummaryController extends Controller
{
    private $accountLedgerDailySummaryRepository;

    public function __construct(AccountLedgerDailySummaryRepository $accountLedgerDailySummaryRepository)
    {

        $this->accountLedgerDailySummaryRepository = $accountLedgerDailySummaryRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerDailySummaryShow()
    {

        if (user_privileges_check('report', 'LedgerDaily', 'display_role')) {
            return view('admin.report.account_summary.account_ledger_daily_summary');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountLedgerDailySummary(Request $request)
    {
        if (user_privileges_check('report', 'LedgerDaily', 'display_role')) {
            try {
                $data = $this->accountLedgerDailySummaryRepository->getAccountLedgerDailySummaryOfIndex($request);

                return RespondWithSuccess('account ledger daily summary  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account ledger daily not  successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }

    }
}
