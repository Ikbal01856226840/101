<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Report\StockItemMonthlySummaryRepository;
use Exception;
use Illuminate\Http\Request;

class StockItemMonthlySummaryController extends Controller
{
    private $stockItemMonthlySummaryRepository;

    private $godownRepository;

    public function __construct(GodownRepository $godownRepository, StockItemMonthlySummaryRepository $stockItemMonthlySummaryRepository)
    {
        $this->godownRepository = $godownRepository;
        $this->stockItemMonthlySummaryRepository = $stockItemMonthlySummaryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockItemMontlySummaryShow()
    {
        if (user_privileges_check('report', 'StockItemMonthly', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.inventrory.stock_item_monthly_summary', compact('godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockItemMontlySummary(Request $request)
    {
        if (user_privileges_check('report', 'StockItemMonthly', 'display_role')) {
            try {
                $data = $this->stockItemMonthlySummaryRepository->getStockItemMonthlySummaryOfIndex($request);

                return RespondWithSuccess('stock item monthly summary successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item monthly summary successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display .
     *
     * @return \Illuminate\Http\Response
     */
    public function stockItemMontlySummaryWise(Request $request)
    {
        if (user_privileges_check('report', 'StockItemMonthly', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $stock_item_id = $request->id;
            $form_date = $request->form_date;
            $to_date = $request->to_date;
            $godown_id = $request->godown_id;

            return view('admin.report.inventrory.stock_item_monthly_summary', compact('godowns', 'stock_item_id', 'form_date', 'to_date', 'godown_id'));
        } else {
            abort(403);
        }

    }
}
