<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\CurrentStockRepository;
use Exception;
use Illuminate\Http\Request;

class CurrentStockController extends Controller
{
    private $currentStockRepository;

    public function __construct(CurrentStockRepository $currentStockRepository)
    {
        $this->currentStockRepository = $currentStockRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentStockShow()
    {

        return view('admin.report.inventrory.current_stock');

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentStockStock(Request $request)
    {

        try {
            $data = $this->currentStockRepository->getCurrentStockOfIndex($request);

            return RespondWithSuccess('current stock successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('current stock successfully', $e->getMessage(), 400);
        }
    }
}
