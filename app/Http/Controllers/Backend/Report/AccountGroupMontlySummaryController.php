<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountGroupMonthlySummaryRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use Exception;
use Illuminate\Http\Request;

class AccountGroupMontlySummaryController extends Controller
{
    private $accountGroupMonthlySummaryRepository;
    private $groupChartRepository;

    public function __construct(GroupChartRepository $groupChartRepository,AccountGroupMonthlySummaryRepository $accountGroupMonthlySummaryRepository)
    {
        $this->groupChartRepository = $groupChartRepository;
        $this->accountGroupMonthlySummaryRepository = $accountGroupMonthlySummaryRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupMonthlySummaryShow()
    {

        if (user_privileges_check('report', 'AccountMontlySummary', 'display_role')) {
              $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');
            return view('admin.report.account_summary.account_group_monthly_summmary',compact('group_chart_data'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupMonthlySummary(Request $request)
    {
        if (user_privileges_check('report', 'AccountMontlySummary', 'display_role')) {
            try {
                $data = $this->accountGroupMonthlySummaryRepository->getAccountMonthlySummaryOfIndex($request);

                return RespondWithSuccess('account group monthly summary  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account group monthly summary not  successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }

    }
}
