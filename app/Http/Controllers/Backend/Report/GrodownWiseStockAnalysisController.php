<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Report\GrodownWiseStockAnalysisRepository;
use Exception;
use Illuminate\Http\Request;

class GrodownWiseStockAnalysisController extends Controller
{
    private $stockGroupRepository;


    private $grodownWiseStockAnalysisRepository;

    public function __construct(StockGroupRepository $stockGroupRepository,GrodownWiseStockAnalysisRepository $grodownWiseStockAnalysisRepository)
    {
        $this->stockGroupRepository = $stockGroupRepository;
        $this->grodownWiseStockAnalysisRepository = $grodownWiseStockAnalysisRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function grodownWiseStockAnalysisShow()
    {
        if (user_privileges_check('report', 'GodownwiseStockAnalysis', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');


            return view('admin.report.inventrory.grodown_wise_stock_analysis', compact('stock_group'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function grodownWiseStockAnalysis(Request $request)
    {
        if (user_privileges_check('report', 'GodownwiseStockAnalysis', 'display_role')) {
            try {
                $data = $this->grodownWiseStockAnalysisRepository->getGrodownWiseStockAnalysisOfIndex($request);

                return RespondWithSuccess('grodown wise stock analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('grodown wise stock analysis successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }



}
