<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\PartyLedgerRepository;
use App\Repositories\Backend\AuthRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class PartyLedgerController extends Controller
{
    private $tree;

    private $groupChartRepository;

    private $partyLedgerRepository;

    private $authRepository;

    public function __construct(Tree $tree, GroupChartRepository $groupChartRepository, PartyLedgerRepository $partyLedgerRepository,AuthRepository $authRepository)
    {
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->partyLedgerRepository = $partyLedgerRepository;
        $this->authRepository = $authRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerShow()
    {
        if (user_privileges_check('report', 'PartyLedger', 'display_role')) {
            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if (array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0) {
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

            return view('admin.report.party_ledger.party_ledger', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerGetData(Request $request)
    {

        if (user_privileges_check('report', 'PartyLedger', 'display_role')) {
            try {
                $data = $this->partyLedgerRepository->PartyLedgerOfIndex($request);

                return RespondWithSuccess('party ledger successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('party ledger  successfully', $e->getMessage(), 404);
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
    public function PartyLedgerIdWise(Request $request)
    {
        if (user_privileges_check('report', 'PartyLedger', 'display_role')) {

            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if (array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0) {
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
            $ledger_id = $request->ledger_id;
            $from_date = $request->form_date;
            $to_date = $request->to_date;

            return view('admin.report.party_ledger.party_ledger_in_details', compact('ledgers', 'ledger_id', 'from_date', 'to_date','all'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerIdWiseDetails(Request $request)
    {
        if (user_privileges_check('report', 'PartyLedgeDetails', 'display_role')) {

            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
           if (array_sum(array_map('intval',explode(',',$get_user->agar))) != 0&&Auth()->user()->user_level!= 1) {
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
            $ledger_id = $request->ledger_id;
            $from_date = $request->form_date;
            $to_date = $request->to_date;
            $sort_by = $request->sort_by;
            $stort_type=$request->sort_type;
            $description=$request->description;
            $narratiaon=$request->narratiaon;
            $remarks=$request->remarks;
            $user_info=$request->user_info;
            $inline_closing_blance=$request->inline_closing_blance??false;
            
            return view('admin.report.party_ledger.party_ledger_in_details_new',compact('ledgers', 'ledger_id', 'from_date', 'to_date','all','sort_by','stort_type','description','narratiaon','remarks','user_info','inline_closing_blance'));
        } else {
            abort(403);
        }
    }
}
