<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\LedgerAnalysisRepository;
use Exception;
use Illuminate\Http\Request;

class LedgerAnalysisController extends Controller
{
    private $ledgerAnalysisRepository;

    public function __construct(LedgerAnalysisRepository $ledgerAnalysisRepository)
    {
        $this->ledgerAnalysisRepository = $ledgerAnalysisRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ledgerAnalysisShow()
    {
        if (user_privileges_check('report', 'LedgerAnalysis', 'display_role')) {
            return view('admin.report.movement_analysis_1.ledger_analysis');
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
}
