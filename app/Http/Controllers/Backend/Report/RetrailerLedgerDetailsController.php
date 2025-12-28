<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Report\RetrailerLedgerDetailsRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class RetrailerLedgerDetailsController extends Controller
{

    private $tree;

    private $groupChartRepository;

    private $retrailerLedgerDetailsRepository;


    public function __construct(Tree $tree, GroupChartRepository $groupChartRepository, RetrailerLedgerDetailsRepository $retrailerLedgerDetailsRepository,AuthRepository $authRepository)
    {

        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->retrailerLedgerDetailsRepository = $retrailerLedgerDetailsRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function retrailerLedgerDetailsShow()
    {
        if (user_privileges_check('report', 'RetrailerLedgerDetails', 'display_role')) {
            $all=1;
            $ledgers = $this->tree->getTreeViewSelectOptionRetrailerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);

            return view('admin.report.party_ledger.retrailer_ledger_details', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function retrailerLedgerDetailsGetData(Request $request)
    {

        if (user_privileges_check('report', 'RetrailerLedgerDetails', 'display_role')) {
            try {
                $data = $this->retrailerLedgerDetailsRepository->RetrailerLedgerDetailsOfIndex($request);

                return RespondWithSuccess('Party Ledger Details successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Party Ledger Details successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }


}
