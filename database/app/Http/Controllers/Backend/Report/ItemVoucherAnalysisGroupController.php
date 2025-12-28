<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\ItemVoucherAnalysisGroupRepostory;
use Exception;
use Illuminate\Http\Request;

class ItemVoucherAnalysisGroupController extends Controller
{
    private $groupChartRepository;

    private $itemVoucherAnalysisGroupRepostory;

    public function __construct(GroupChartRepository $groupChartRepository, ItemVoucherAnalysisGroupRepostory $itemVoucherAnalysisGroupRepostory)
    {
        $this->groupChartRepository = $groupChartRepository;
        $this->itemVoucherAnalysisGroupRepostory = $itemVoucherAnalysisGroupRepostory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemVoucherAnalysisGroupShow()
    {
        if (user_privileges_check('report', 'ItemVoucherAnalysisGroup', 'display_role')) {
            $group_chart_data = $this->groupChartRepository->getTreeSelectOption();

            return view('admin.report.movement_analysis_1.item_voucher_analysis_group', compact('group_chart_data'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemVoucherAnalysisGroup(Request $request)
    {
        if (user_privileges_check('report', 'ItemVoucherAnalysisGroup', 'display_role')) {
            try {
                $data = $this->itemVoucherAnalysisGroupRepostory->getItemVoucherAnalysisGroupOfIndex($request);

                return RespondWithSuccess('item voucher analysis group successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('item voucher analysis not group  successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
