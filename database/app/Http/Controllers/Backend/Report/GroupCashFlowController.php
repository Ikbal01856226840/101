<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\GroupCashFlowRepository;
use Exception;
use Illuminate\Http\Request;

class GroupCashFlowController extends Controller
{
    private $groupCashFlowRepository;

    private $group_chart;

    public function __construct(GroupCashFlowRepository $groupCashFlowRepository, GroupChartRepository $groupChartRepository)
    {
        $this->groupCashFlowRepository = $groupCashFlowRepository;
        $this->group_chart = $groupChartRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function GroupCashFlowDetailsShow()
    {
        if (user_privileges_check('report', 'GroupCashFlow', 'display_role')) {
            $group_chart_data = $this->group_chart->getTreeSelectOption();

            return view('admin.report.company_statistics.group_cash_flow', compact('group_chart_data'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function GroupCashFlowDetailsGetData(Request $request)
    {
        if (user_privileges_check('report', 'GroupCashFlow', 'display_role')) {
            try {
                $data = $this->groupCashFlowRepository->getGroupCashFlowOfIndex($request);

                return RespondWithSuccess('Cash Flow group successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Cash Flow group successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    public function GroupCashFlowDetailsIdWise(Request $request)
    {
        if (user_privileges_check('report', 'GroupCashFlow', 'display_role')) {
            $group_chart_data = $this->group_chart->getTreeSelectOption();
            $group_id = $request->id;
            $form_date = $request->form_date;
            $to_date = $request->to_date;

            return view('admin.report.company_statistics.group_cash_flow', compact('group_chart_data', 'group_id', 'form_date', 'to_date'));
        } else {
            abort(403);
        }
    }
}
