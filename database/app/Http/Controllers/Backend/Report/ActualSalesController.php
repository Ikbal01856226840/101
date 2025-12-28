<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Report\ActualSalesRepository;
use Exception;
use Illuminate\Http\Request;

class ActualSalesController extends Controller
{
    private $godownRepository;

    private $actualSalesRepository;

    public function __construct(GodownRepository $godownRepository, ActualSalesRepository $actualSalesRepository)
    {
        $this->godownRepository = $godownRepository;
        $this->actualSalesRepository = $actualSalesRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function actualSalesShow()
    {
        if (user_privileges_check('report', 'ActualSales', 'display_role')) {
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.movement_analysis_1.actual_sales', compact('godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function actualSales(Request $request)
    {
        if (user_privileges_check('report', 'ActualSales', 'display_role')) {
            try {
                $data = $this->actualSalesRepository->getActualSalesOfIndex($request);

                return RespondWithSuccess('actual sales successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('actual sales successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
