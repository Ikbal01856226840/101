<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\LedgerAnalysisRepository;
use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class LedgerAnalysisController extends Controller
{
    private $ledgerAnalysisRepository;

    private $authRepository;

    private $groupChartRepository;

    private $tree;

    public function __construct(Tree $tree, AuthRepository $authRepository,GroupChartRepository $groupChartRepository, LedgerAnalysisRepository $ledgerAnalysisRepository)
    {
        $this->ledgerAnalysisRepository = $ledgerAnalysisRepository;
        $this->authRepository = $authRepository;
        $this->groupChartRepository=$groupChartRepository;
        $this->tree=$tree;


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ledgerAnalysisShow()
    {
        if (user_privileges_check('report', 'LedgerAnalysis', 'display_role')) {

            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if ((array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0)&&Auth()->user()->user_level!= 1) {
                $all=0;
                $data = json_decode(json_encode($this->tree->group_chart_tree_row_query($get_user->agar), true), true);
                $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
                $new_array = array_map(function ($k) use ($data) {
                    return $data[$k];
                }, $keys);
                
                $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($data, true), true), $new_array[0]['under']);
            }else{
                $all=1;
                $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            }
            return view('admin.report.movement_analysis_1.ledger_analysis',compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function ledgerAnalysis(Request $request)
    {
        if (user_privileges_check('report', 'LedgerAnalysis', 'display_role')) {
            try {
                $data = $this->ledgerAnalysisRepository->getLedgerAnalyisOfIndex($request);

                return RespondWithSuccess('ledger analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('ledger analysis successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    public function ledgerAnalysisGroupWise(Request $request)
    {
        if (user_privileges_check('report', 'LedgerAnalysis', 'display_role')) {
            
            $ledger_id = $request->ledger_id;
            
            $stock_group_id = $request->stock_group_id;
            $from_date = $request->form_date;
          
            $to_date = $request->to_date;
            $purchase_in = $request->purchase_in ?? 0;

            $grn_in = $request->grn_in ?? 0;
            $purchase_return_in = $request->purchase_return_in ?? 0;
            $journal_in = $request->journal_in ?? 0;
            $stock_journal_in = $request->stock_journal_in ?? 0;
            $sales_return_out = $request->sales_return_out ?? 0;
            $gtn_out = $request->gtn_out ?? 0;
            $sales_out = $request->sales_out ?? 0;
            $journal_out = $request->journal_out ?? 0;
            $stock_journal_out = $request->stock_journal_out ?? 0;
            $in_ward_column_rate=$request->in_ward_column_rate ?? 0;
            $out_ward_column_rate=$request->out_ward_column_rate ?? 0;
             $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if ((array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0)&&Auth()->user()->user_level!= 1) {
                $all=0;
                $data = json_decode(json_encode($this->tree->group_chart_tree_row_query($get_user->agar), true), true);
                $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
                $new_array = array_map(function ($k) use ($data) {
                    return $data[$k];
                }, $keys);
                
                $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($data, true), true), $new_array[0]['under']);
            }else{
                $all=1;
                $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            }

            return view('admin.report.movement_analysis_1.ledger_analysis', compact( 'ledger_id', 'stock_group_id', 'from_date', 'to_date',  'purchase_in', 'grn_in', 'purchase_return_in', 'journal_in', 'stock_journal_in', 'sales_return_out', 'gtn_out', 'sales_out', 'journal_out', 'stock_journal_out','ledgers','all','in_ward_column_rate','out_ward_column_rate'));
        } else {
            abort(403);
        }
    }
}
