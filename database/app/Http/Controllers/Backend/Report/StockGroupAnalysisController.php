<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Report\StockGroupAnalysisRepository;
use Exception;
use Illuminate\Http\Request;

class StockGroupAnalysisController extends Controller
{
    private $stockGroupRepository;

    private $godownRepository;

    private $stockGroupAnalysisRepository;

    public function __construct(StockGroupRepository $stockGroupRepository, GodownRepository $godownRepository, StockGroupAnalysisRepository $stockGroupAnalysisRepository)
    {
        $this->stockGroupRepository = $stockGroupRepository;
        $this->godownRepository = $godownRepository;
        $this->stockGroupAnalysisRepository = $stockGroupAnalysisRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockGroupAnalysisShow()
    {
        if (user_privileges_check('report', 'StockGroupAnalysis', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.movement_analysis_2.stock_group_analysis', compact('stock_group', 'godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockGroupAnalysis(Request $request)
    {
        if (user_privileges_check('report', 'StockGroupAnalysis', 'display_role')) {
            try {
                $data = $this->stockGroupAnalysisRepository->stockGroupAnalysisOfIndex($request);

                return RespondWithSuccess('stock group analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock group analysis successfully', $e->getMessage(), 400);
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
    public function stockGroupAnalysisIdWise(Request $request)
    {
        if (user_privileges_check('report', 'StockGroupAnalysis', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();
            $godown_id = $request->godown_id;
            $stock_group_id = $request->stock_group_id;
            $form_date = $request->form_date;
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

            return view('admin.report.movement_analysis_2.stock_group_analysis', compact('stock_group', 'godowns', 'stock_group_id', 'form_date', 'to_date', 'godown_id', 'purchase_in', 'grn_in', 'purchase_return_in', 'journal_in', 'stock_journal_in', 'sales_return_out', 'gtn_out', 'sales_out', 'journal_out', 'stock_journal_out'));
        } else {
            abort(403);
        }
    }
}
