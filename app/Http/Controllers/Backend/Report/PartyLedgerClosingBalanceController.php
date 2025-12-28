<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Report\PartyLedgerClosingbalanceRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class PartyLedgerClosingBalanceController extends Controller
{
    private $tree;

    private $groupChartRepository;

    private $partyLedgerClosingbalanceRepository;

    private $authRepository;

    public function __construct(Tree $tree, GroupChartRepository $groupChartRepository,PartyLedgerClosingbalanceRepository $partyLedgerClosingbalanceRepository,AuthRepository $authRepository)
    {
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->partyLedgerClosingbalanceRepository = $partyLedgerClosingbalanceRepository;
        $this->authRepository = $authRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerClosingBalanceShow()
    {

        if (user_privileges_check('report', 'PartyLedgerClosingBalance', 'display_role')) {
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

            return view('admin.report.party_ledger.party_ledger_closing balance', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function PartyLedgerClosingBalanceGetData(Request $request)
    {
        if (user_privileges_check('report', 'PartyLedgerClosingBalance', 'display_role')) {
            try {
                $data = $this->partyLedgerClosingbalanceRepository->PartyLedgerClosingbalanceGetData($request);

                return RespondWithSuccess(' party ledger closing balance  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError(' party ledger closing balance not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }


}
