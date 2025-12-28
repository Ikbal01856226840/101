<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\CashFlowSummaryRepository;
use Exception;
use Illuminate\Http\Request;

class CashFlowSummaryController extends Controller
{
    private $cashFlowSummaryRepository;

    public function __construct(CashFlowSummaryRepository $cashFlowSummaryRepository)
    {
        $this->cashFlowSummaryRepository = $cashFlowSummaryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function CashFlowSummaryDetailsShow()
    {
        if (user_privileges_check('report', 'CashFlow', 'display_role')) {
            return view('admin.report.company_statistics.cash_flow_summary');
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

        if (user_privileges_check('report', 'CashFlow', 'display_role')) {
            try {
                $data = $this->cashFlowSummaryRepository->getCashFlowSummaryOfIndex($request);

                return RespondWithSuccess('Cash Flow Summary successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Cash Flow Summary successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
