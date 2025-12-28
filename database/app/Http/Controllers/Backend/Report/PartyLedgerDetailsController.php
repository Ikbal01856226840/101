<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\PartyLedgerDetailsRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\AuthRepository;


use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class PartyLedgerDetailsController extends Controller
{
    private $ledgerHead;
   
    private $tree;

    private $groupChartRepository;

    private $partyLedgerRepository;

    private $authRepository;

    public function __construct(LegerHeadRepository $ledgerHead, Tree $tree, GroupChartRepository $groupChartRepository, PartyLedgerDetailsRepository $partyLedgerRepository,AuthRepository $authRepository)
    {

        $this->ledgerHead = $ledgerHead;
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
    public function PartyLedgerInDetailsShow()
    {
        if (user_privileges_check('report', 'PartyLedgeDetails', 'display_role')) {
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


            return view('admin.report.party_ledger.party_ledger_in_details', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerInDetailsGetData(Request $request)
    {

        if (user_privileges_check('report', 'PartyLedgeDetails', 'display_role')) {
            try {
                $data = $this->partyLedgerRepository->PartyLedgerInDetails($request);

                return RespondWithSuccess('Party Ledger Details successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Party Ledger Details successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerContactDetailsShow()
    {
        if (user_privileges_check('report', 'PartyLedgeDetails', 'display_role')) {
            $group_chart_id = $this->groupChartRepository->getTreeSelectOption('under_id');

            return view('admin.report.party_ledger.party_ledger_contact_details', compact('group_chart_id'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerContactDetailsGetData(Request $request)
    {

        if (user_privileges_check('report', 'PartyLedgeDetails', 'display_role')) {
            try {
                if($request->group_id==0){
                    $data = $this->ledgerHead->getTree();;
                }else{
                    $data = $this->ledgerHead->searchingData($request);
                }


                return RespondWithSuccess('Party Ledger Details successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Party Ledger Details successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
