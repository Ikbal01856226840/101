<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\GroupWisePartyLedgerRepository;
use App\Repositories\Backend\AuthRepository;
use Exception;
use Illuminate\Http\Request;

class GroupWisePartyLedgerController extends Controller
{
    private $groupChartRepository;

    private $groupWisePartyLedgerRepository;

    private $authRepository;

    public function __construct(GroupChartRepository $groupChartRepository, GroupWisePartyLedgerRepository $groupWisePartyLedgerRepository,AuthRepository $authRepository)
    {

        $this->groupChartRepository = $groupChartRepository;
        $this->groupWisePartyLedgerRepository = $groupWisePartyLedgerRepository;
        $this->authRepository = $authRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupWisePartyLedgerShow()
    {
        if (user_privileges_check('report', 'GroupWisePartyLedger', 'display_role')) {
            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if (array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0) {
                $all=0;
              $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');
            }else{
                $all=1;
                $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');
            }
            return view('admin.report.party_ledger.group_wise_party_ledger', compact('group_chart_data','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupWisePartyLedgerGetData(Request $request)
    {
        if (user_privileges_check('report', 'GroupWisePartyLedger', 'display_role')) {
            try {
                $data = $this->groupWisePartyLedgerRepository->getGroupWisePartyLedgerOfIndex($request);

                return RespondWithSuccess('group wise party ledger  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('group wise party ledger  successfully', $e->getMessage(), 400);
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
    public function groupWisePartyLedgerIdWise(Request $request)
    {
        if (user_privileges_check('report', 'GroupWisePartyLedger', 'display_role')) {
            
            $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
            if (array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0) {
                $all=0;
              $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');
            }else{
                $all=1;
                $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');
            }
            $group_id = $request->group_chart_id;
            $form_date = $request->form_date;
            $to_date = $request->to_date;

            return view('admin.report.party_ledger.group_wise_party_ledger', compact('group_chart_data', 'group_id', 'form_date', 'to_date','all'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupWisePartyLedgerCreditLimitShow(){
        if (user_privileges_check('report', 'GroupWisePartyLedgerCreditLimit', 'display_role')) {
            $group_chart_data = $this->groupChartRepository->getTreeSelectOption('under_id');

            return view('admin.report.party_ledger.group_wise_party_ledger_limit_credit', compact('group_chart_data'));
        } else {
            abort(403);
        }
    }

     /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function groupWisePartyLedgerCreditLimitGetData(Request $request){
        if (user_privileges_check('report', 'GroupWisePartyLedgerCreditLimit', 'display_role')) {
            try {
                $data = $this->groupWisePartyLedgerRepository->getGroupWisePartyLedgerCreditLimit($request);

                return RespondWithSuccess('group wise party ledger  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('group wise party ledger  successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
