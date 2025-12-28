<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Report\POSRepository;
use Exception;
use Illuminate\Http\Request;

class POSController extends Controller
{

    private $POSRepository;

    private $stockGroupRepository;

    public function __construct(POSRepository $POSRepository,StockGroupRepository $stockGroupRepository)
    {

        $this->POSRepository = $POSRepository;
        $this->stockGroupRepository = $stockGroupRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function salesListShow()
    {
        if (user_privileges_check('report', 'POSSalesList', 'display_role')) {
            return view('admin.report.pos.sales_list');
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function salesList(Request $request)
    {
        if (user_privileges_check('report', 'POSSalesList', 'display_role')) {
            try {
                $data = $this->POSRepository->salesList($request);

                return RespondWithSuccess('Sales List successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Sales List not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    public function posCurrentStockShow(){
        if (user_privileges_check('report', 'POSCurrentStock', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            return view('admin.report.pos.pos_current_stock',compact('stock_group'));
        } else {
            abort(403);
        }
       
    }
}
