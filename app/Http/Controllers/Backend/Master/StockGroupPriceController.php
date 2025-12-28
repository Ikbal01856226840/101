<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\StockGroupPriceRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use Exception;
use Illuminate\Http\Request;

class StockGroupPriceController extends Controller
{
    private $stockGroup;

    private $stockGroupPriceRepository;

    public function __construct(StockGroupRepository $stockGroupRepository, StockGroupPriceRepository $stockGroupPriceRepository)
    {
        $this->stockGroup = $stockGroupRepository;
        $this->stockGroupPriceRepository = $stockGroupPriceRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->stockGroup->getTreeSelectOption();

        if (user_privileges_check('master', 'Group Price', 'alter_role')) {
            return view('admin.master.stock_group_price.index', compact('select_option_tree'));
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
        if (user_privileges_check('master', 'Group Price', 'alter_role')) {
            try {
                $data = $this->stockGroupPriceRepository->getTree($request);

                return RespondWithSuccess('All  Stock group price tree list show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock group price tree list not show successfully !!', $e->getMessage(), 400);
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
        if (user_privileges_check('master', 'Group Price', 'create_role')) {
            try {
                $data = $this->stockGroupPriceRepository->StoreStockGroupPrice($request);

                return RespondWithSuccess('stock group price create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock group price create successfully', $e->getMessage(), 400);
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
        if (user_privileges_check('master', 'Group Price', 'alter_role')) {
            try {
                $data = $this->stockGroupPriceRepository->updateStockGroupPrice($request, $id);

                return RespondWithSuccess('stock group price update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock group price not  update successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
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
        if (user_privileges_check('master', 'Group Price', 'delete_role')) {
            try {
                $data = $this->stockGroupPriceRepository->deleteStockGroupPrice($id);

                return RespondWithSuccess('stock group price delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock group price  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
