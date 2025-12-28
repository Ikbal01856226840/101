<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\AccountGroupAnalysisRepository;
use Exception;
use Illuminate\Http\Request;

class AccountGroupAnalysisController extends Controller
{
    private $groupChartRepository;

    private $accountGroupAnalysisRepository;

    public function __construct(GroupChartRepository $groupChartRepository, AccountGroupAnalysisRepository $accountGroupAnalysisRepository)
    {
        $this->groupChartRepository = $groupChartRepository;
        $this->accountGroupAnalysisRepository = $accountGroupAnalysisRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupAnalysisShow()
    {
        if (user_privileges_check('report', 'AccountsGroupAnalysis', 'display_role')) {
            $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');

            return view('admin.report.movement_analysis_1.account_group_analysis', compact('group_chart_data'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupAnalysis(Request $request)
    {
        if (user_privileges_check('report', 'AccountsGroupAnalysis', 'display_role')) {
            try {
                $data = $this->accountGroupAnalysisRepository->getAccountGroupAnalysisOfIndex($request);

                return RespondWithSuccess('account group analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account group analysis successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
