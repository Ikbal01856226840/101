<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\AccountGroupVoucherWiseAnalysisRepository;
use Exception;
use Illuminate\Http\Request;

class AccountGroupVoucherWiseAnalysisController extends Controller
{
    private $groupChartRepository;

    private $accountGroupVoucherWiseAnalysisRepository;

    public function __construct(GroupChartRepository $groupChartRepository, AccountGroupVoucherWiseAnalysisRepository $accountGroupVoucherWiseAnalysisRepository)
    {
        $this->groupChartRepository = $groupChartRepository;
        $this->accountGroupVoucherWiseAnalysisRepository = $accountGroupVoucherWiseAnalysisRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupVoucherWiseAnalysisShow()
    {
        if (user_privileges_check('report', 'AccountsGroupVoucherWiseAnalysis', 'display_role')) {
            $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');

            return view('admin.report.movement_analysis_1.account_group_voucher_wise_analysis', compact('group_chart_data'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountGroupVoucherWiseAnalysis(Request $request)
    {
        if (user_privileges_check('report', 'AccountsGroupVoucherWiseAnalysis', 'display_role')) {
            try {
                $data = $this->accountGroupVoucherWiseAnalysisRepository->getAccountGroupVoucherWiseAnalysisOfIndex($request);

                return RespondWithSuccess('account group voucher wise analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('account group voucher wise analysis successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
