<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Master\StockItemCommissionRepository;
use Exception;
use Illuminate\Http\Request;

class StockItemCommissionController extends Controller
{
    private $stockGroup;

    private $stockItemCommissionRepository;

    public function __construct(StockGroupRepository $stockGroupRepository, StockItemCommissionRepository $stockItemCommissionRepository)
    {
        $this->stockGroup = $stockGroupRepository;
        $this->stockItemCommissionRepository = $stockItemCommissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->stockGroup->getTreeSelectOption();

        if (user_privileges_check('master', 'stock_item__commission', 'display_role')) {
            return view('admin.master.stock_item_commission.index', compact('select_option_tree'));
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
        if (user_privileges_check('master', 'stock_item__commission', 'display_role')) {
            try {
                $data = $this->stockItemCommissionRepository->getTree($request);

                return RespondWithSuccess('All stock item commission tree list show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock item commission tree list not show successfully !!', $e->getMessage(), 400);
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
        if (user_privileges_check('master', 'stock_item__commission', 'create_role')) {
            try {
                $data = $this->stockItemCommissionRepository->StoreStockItemCommission($request);

                return RespondWithSuccess('stock item commission create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item commission create successfully', $e->getMessage(), 400);
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
        if (user_privileges_check('master', 'stock_item__commission', 'alter_role')) {
            try {
                $data = $this->stockItemCommissionRepository->updateStockItemCommission($request, $id);

                return RespondWithSuccess('stock item commission update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item commission not  update successfully !!', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'stock_item__commission', 'delete_role')) {
            try {
                $data = $this->stockItemCommissionRepository->deleteStockItemCommission($id);

                return RespondWithSuccess('stock item commission delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item commission  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
