<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Report\UnusedStockRepository;
use Exception;
use Illuminate\Http\Request;

class UnusedStockController extends Controller
{
    private $stockGroupRepository;

    private $godownRepository;

    private $unusedStockRepository;

    public function __construct(StockGroupRepository $stockGroupRepository, GodownRepository $godownRepository, UnusedStockRepository $unusedStockRepository)
    {
        $this->stockGroupRepository = $stockGroupRepository;
        $this->godownRepository = $godownRepository;
        $this->unusedStockRepository = $unusedStockRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unusedStockShow()
    {
        if (user_privileges_check('report', 'StockItemUnusedStock', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.inventrory.unused_stock', compact('stock_group', 'godowns'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function unusedStock(Request $request)
    {
        if (user_privileges_check('report', 'StockItemUnusedStock', 'display_role')) {
            try {
                $data = $this->unusedStockRepository->getUnusedStockOfIndex($request);
                return RespondWithSuccess('unused stock successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('unused stock not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

}
