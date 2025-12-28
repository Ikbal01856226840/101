<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Master\StockItemOpeningRepository;
use Exception;
use Illuminate\Http\Request;

class StockItemOpeningController extends Controller
{
    private $stockGroup;

    private $godownRepository;

    private $customerRepository;

    private $stockItemOpeningRepository;

    public function __construct(StockGroupRepository $stockGroupRepository, GodownRepository $godownRepository, CustomerRepository $customerRepository, StockItemOpeningRepository $stockItemOpeningRepository)
    {
        $this->stockGroup = $stockGroupRepository;
        $this->godownRepository = $godownRepository;
        $this->customerRepository = $customerRepository;
        $this->stockItemOpeningRepository = $stockItemOpeningRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->stockGroup->getTreeSelectOption();
        $godowns = $this->godownRepository->getGodownOfIndex();
        $customers = $this->customerRepository->getCustomerOfIndex();

        if (user_privileges_check('master', 'stock_item__opening_balance', 'display_role')) {
            return view('admin.master.stock_item_opening.index', compact('select_option_tree', 'godowns', 'customers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the tree view.
     *
     * @return \Illuminate\Http\Response
     */
    public function treeView(Request $request)
    {
        if (user_privileges_check('master', 'stock_item__opening_balance', 'display_role')) {
            try {
                $data = $this->stockItemOpeningRepository->getTree($request->godown_id, $request->customer_id, $request->stock_group_id);

                return RespondWithSuccess('All stock commission tree list show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock commission tree list not show successfully !!', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (user_privileges_check('master', 'stock_item__opening_balance', 'create_role')) {
            try {
                $data = $this->stockItemOpeningRepository->StoreStockItemOpening($request);

                return RespondWithSuccess('stock item  opening create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item  opening create successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = '';

            return RespondWithSuccess('stock item commission update successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('stock item commission not  update successfully', $e->getMessage(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $data = '';

            return RespondWithSuccess('stock item price delete successfull !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('stock item price  delete successfull', $e->getMessage(), 404);
        }
    }
}
