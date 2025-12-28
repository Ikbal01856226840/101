<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\AccountLedgerDealerRetrailerAnalysisRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class RetrailerAnalysisController extends Controller
{

    private $tree;

    private $groupChartRepository;

    private $accountLedgerDealerRetrailerAnalysisRepository;


    public function __construct(Tree $tree, GroupChartRepository $groupChartRepository,AccountLedgerDealerRetrailerAnalysisRepository $accountLedgerDealerRetrailerAnalysisRepository)
    {

        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
        $this->accountLedgerDealerRetrailerAnalysisRepository = $accountLedgerDealerRetrailerAnalysisRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function retrailerAnalysisShow()
    {
        if (user_privileges_check('report', 'RetrailerAnalysis', 'display_role')) {
            $all=1;
            $ledgers = $this->tree->getTreeViewSelectOptionRetrailerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);

            return view('admin.report.movement_analysis_1.retrailer_analysis', compact('ledgers','all'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function retraileAnalysisGetData(Request $request)
    {

        if (user_privileges_check('report', 'RetrailerAnalysis', 'display_role')) {
            try {
                $data = $this->accountLedgerDealerRetrailerAnalysisRepository->DealerRetrailerAnalysisOfIndex($request);

                return RespondWithSuccess('Retrailer Analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Retrailer Analysis successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }


}
