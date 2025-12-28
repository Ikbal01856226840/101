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
   

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function  itemVoucherAnalysisGroupDetails(Request $request)
    {
   
    if (user_privileges_check('report', 'ItemVoucherAnalysisLedger', 'display_role')) {
            $from_date=$request->form_date;
            $to_date=$request->to_date;
            $stock_item_id=$request->stock_item_id;
            $purchase_in=$request->purchase_in;
            $grn_in=$request->grn_in;
            $purchase_return_in=$request->purchase_return_in;
            $journal_in=$request->journal_in;
            $stock_journal_in=$request->stock_journal_in;
            $sales_return_out=$request->sales_return_out;
            $gtn_out=$request->gtn_out;
            $sales_out=$request->sales_out;
            $journal_out=$request->journal_out;
            $stock_journal_out=$request->stock_journal_out;
            $group_chart_data = $this->groupChartRepository->getTreeSelectOption();
            $group_chart = explode('-', $request->group_id, 2);
            $group_id=$group_chart[0];
           return view('admin.report.movement_analysis_1.item_voucher_analysis_group', compact('group_id','group_chart_data','purchase_in','grn_in','purchase_return_in','journal_in','stock_journal_in','sales_return_out','gtn_out','from_date','sales_out', 'to_date' ,'stock_item_id','journal_out','stock_journal_out'));
         
        } else {
            abort(403);
        }
    }
}
