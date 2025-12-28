<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Report\LedgerCashFlowRepository;
use App\Services\Tree;
use Exception;
use Illuminate\Http\Request;

class LedgerCashFlowController extends Controller
{
    private $ledgerCashFlowRepository;

    private $tree;

    private $groupChartRepository;

    public function __construct(LedgerCashFlowRepository $ledgerCashFlowRepository, Tree $tree, GroupChartRepository $groupChartRepository)
    {
        $this->ledgerCashFlowRepository = $ledgerCashFlowRepository;
        $this->tree = $tree;
        $this->groupChartRepository = $groupChartRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function LedgerCashFlowDetailsShow()
    {
        if (user_privileges_check('report', 'LedgerCashFlow', 'display_role')) {
            $ledger_data = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);

            return view('admin.report.company_statistics.ledger_cash_flow', compact('ledger_data'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function LedgerCashFlowDetailsGetData(Request $request)
    {
        if (user_privileges_check('report', 'LedgerCashFlow', 'display_role')) {
            try {
                $data = $this->ledgerCashFlowRepository->getLedgerCashFlowOfIndex($request);

                return RespondWithSuccess('Cash Flow Ledger Successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Cash Flow Ledger Successfully', $e->getMessage(), 400);
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
    public function LedgerCashFlowDetailsIdWise(Request $request)
    {
        if (user_privileges_check('report', 'LedgerCashFlow', 'display_role')) {
            $ledger_data = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);
            $ledger_id = $request->id;
            $form_date = $request->form_date;
            $to_date = $request->to_date;

            return view('admin.report.company_statistics.ledger_cash_flow', compact('ledger_data', 'ledger_id', 'form_date', 'to_date'));
        } else {
            abort(403);
        }
    }
}
