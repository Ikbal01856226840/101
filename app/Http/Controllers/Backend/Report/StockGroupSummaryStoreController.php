<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Report\StockGroupSummaryRepository;
use Exception;
use Illuminate\Http\Request;

class StockGroupSummaryStoreController extends Controller
{
    private $stockGroupRepository;

    private $godownRepository;

    private $stockGroupSummaryRepository;

    public function __construct(StockGroupRepository $stockGroupRepository, GodownRepository $godownRepository, StockGroupSummaryRepository $stockGroupSummaryRepository)
    {
        $this->stockGroupRepository = $stockGroupRepository;
        $this->godownRepository = $godownRepository;
        $this->stockGroupSummaryRepository = $stockGroupSummaryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockGroupSummaryStoreShow()
    {
        if (user_privileges_check('report', 'StockGroupSummaryStore', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.inventrory.stock_group_summary_store', compact('stock_group', 'godowns'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockGroupSummaryStore(Request $request)
    {
        if (user_privileges_check('report', 'StockGroupSummaryStore', 'display_role')) {
            try {
                $data = $this->stockGroupSummaryRepository->getStockGroupSummaryOfIndex($request);

                return RespondWithSuccess('stock  group summary successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock  group summary successfully', $e->getMessage(), 400);
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
    public function  stockGroupSummaryStoreIdWise(Request $request)
    {
        if (user_privileges_check('report', 'StockGroupSummaryStore', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();
            $form_date = $request->form_date;
            $to_date = $request->to_date;
            $stock_group_id=$request->id;
            $grodown_id=$request->godown_id;

            return view('admin.report.inventrory.stock_group_summary_store', compact('stock_group_id','grodown_id','stock_group', 'godowns', 'form_date', 'to_date'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockGroupSummaryShortStore(Request $request)
    {
        if (user_privileges_check('report', 'StockGroupSummaryNewStore', 'display_role')) {
            try {
                $data = $this->stockGroupSummaryRepository->getStockGroupSummaryStoreOfIndex($request);

                return RespondWithSuccess('stock  group summary successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock  group summary successfully', $e->getMessage(), 400);
            }
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stockGroupSummaryShortStoreShow()
    {
        if (user_privileges_check('report', 'StockGroupSummaryNewStore', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->godownAccess();

            return view('admin.report.inventrory.stock_group_summary_new_store', compact('stock_group', 'godowns'));
        } else {
            abort(403);
        }
    }

}
