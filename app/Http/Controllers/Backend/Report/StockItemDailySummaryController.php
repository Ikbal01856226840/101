<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Report\StockItemDailySummaryRepository;
use Exception;
use Illuminate\Http\Request;

class StockItemDailySummaryController extends Controller
{
    private $stockItemDailySummaryRepository;

    private $godownRepository;

    public function __construct(GodownRepository $godownRepository, StockItemDailySummaryRepository $stockItemDailySummaryRepository)
    {
        $this->godownRepository = $godownRepository;
        $this->stockItemDailySummaryRepository = $stockItemDailySummaryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockItemDailySummaryShow()
    {
        if (user_privileges_check('report', 'StockItemDaily', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.inventrory.stock_item_daily_summary', compact('godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockItemDailySummary(Request $request)
    {
        if (user_privileges_check('report', 'StockItemDaily', 'display_role')) {
            try {
                $data = $this->stockItemDailySummaryRepository->getStockItemDailySummaryOfIndex($request);

                return RespondWithSuccess('stock item daily summary successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item daily summary successfully', $e->getMessage(), 400);
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
    public function StockItemDailyIdWise(Request $request)
    {
        if (user_privileges_check('report', 'StockItemDaily', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();
            $stock_item_id = $request->stock_item_id;
            $date = $request->date;
            $month_year = date('Y-m', strtotime($date));
            $from_date = "$month_year-01";

            $month = date('Y-m-d', strtotime($month_year));
            $to_day = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date)), date('Y', strtotime($date)));
            if (date('m') == date('m', strtotime($date))) {
                $current_day = date('d');
                $to_date = "$month_year-$current_day";
            } else {
                $to_date = "$month_year-$to_day";
            }
            $godown_id = $request->godown_id;

            return view('admin.report.inventrory.stock_item_daily_summary', compact('godowns', 'stock_item_id', 'from_date', 'to_date', 'godown_id'));
        } else {
            abort(403);
        }

    }
}
