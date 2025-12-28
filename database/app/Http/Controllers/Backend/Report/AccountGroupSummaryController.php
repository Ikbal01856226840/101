<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\AccountGroupSummaryRepository;
use Exception;
use Illuminate\Http\Request;

class AccountGroupSummaryController extends Controller
{
    private $groupChartRepository;

    private $accountGroupSummaryRepository;

    public function __construct(GroupChartRepository $groupChartRepository, AccountGroupSummaryRepository $accountGroupSummaryRepository)
    {

        $this->groupChartRepository = $groupChartRepository;
        $this->accountGroupSummaryRepository = $accountGroupSummaryRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupSummaryShow()
    {
        if (user_privileges_check('report', 'AccountGroupSummary', 'display_role')) {
            $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');

            return view('admin.report.account_summary.account_group_summary', compact('group_chart_data'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupSummary(Request $request)
    {
        if (user_privileges_check('report', 'AccountGroupSummary', 'display_role')) {
            try {
                $data = $this->accountGroupSummaryRepository->getAccountGroupSummaryOfIndex($request);

                return RespondWithSuccess('account group summary  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account group summary not  successfully', $e->getMessage(), 400);
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
    public function accountGroupSummaryIdWise(Request $request)
    {
        if (user_privileges_check('report', 'AccountGroupSummary', 'display_role')) {
            $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');
            $group_id = $request->group_chart_id;
            $form_date = $request->form_date;
            $to_date = $request->to_date;

            return view('admin.report.account_summary.account_group_summary', compact('group_chart_data', 'group_id', 'form_date', 'to_date'));
        } else {
            abort(403);
        }
    }
}
