<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\ItemVoucherAnalysisRepository;
use App\Repositories\Backend\Master\GodownRepository;
use Exception;
use Illuminate\Http\Request;

class ItemVoucherAnalysisController extends Controller
{
    private $itemVoucherAnalysisRepository;

    private $godownRepository;

    public function __construct(ItemVoucherAnalysisRepository $itemVoucherAnalysisRepository, GodownRepository $godownRepository)
    {
        $this->itemVoucherAnalysisRepository = $itemVoucherAnalysisRepository;
        $this->godownRepository=$godownRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemVoucherAnalysisShow()
    {
        if (user_privileges_check('report', 'ItemVoucherAnalysisLedger', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            return view('admin.report.movement_analysis_1.item_voucher_analysis',compact('godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemVoucherAnalysis(Request $request)
    {
        if (user_privileges_check('report', 'ItemVoucherAnalysisLedger', 'display_role')) {
            try {
                $data = $this->itemVoucherAnalysisRepository->getItemVoucherAnalyisOfIndex($request);

                return RespondWithSuccess('item voucher analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('item voucher analysis not successfully', $e->getMessage(), 400);
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
    public function itemVoucherAnalysisDetails(Request $request)
    {
   
    if (user_privileges_check('report', 'ItemVoucherAnalysisLedger', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $from_date=$request->form_date;
            $to_date=$request->to_date;
            $stock_item_id=$request->stock_item_id;
            $purchase_in='';
            $grn_in='';
            $purchase_return_in='';
            $journal_in='';
            $stock_journal_in='';
            $sales_return_out=25;
            $gtn_out='';
            $sales_out=19;
            $journal_out='';
            $stock_journal_out='';
            $godown_id =$request->godown_id;
           return view('admin.report.movement_analysis_1.item_voucher_analysis', compact('godown_id','godowns','purchase_in','grn_in','purchase_return_in','journal_in','stock_journal_in','sales_return_out','gtn_out','from_date','sales_out', 'to_date' ,'stock_item_id','journal_out','stock_journal_out'));
         
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemVoucherAnalysisLedgerDetails(Request $request)
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
            $ledger_id=$request->ledger_id;
            $godowns = $this->godownRepository->getGodownOfIndex();
           return view('admin.report.movement_analysis_1.item_voucher_analysis', compact('godowns','ledger_id','purchase_in','grn_in','purchase_return_in','journal_in','stock_journal_in','sales_return_out','gtn_out','from_date','sales_out', 'to_date' ,'stock_item_id','journal_out','stock_journal_out'));
         
        } else {
            abort(403);
        }
    }
}
