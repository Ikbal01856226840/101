<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\StockCommissionRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use Exception;
use Illuminate\Http\Request;

class StockCommissionController extends Controller
{
    private $stockGroup;

    private $stock_commission;

    public function __construct(StockGroupRepository $stockGroupRepository, StockCommissionRepository $stock_commission)
    {
        $this->stockGroup = $stockGroupRepository;
        $this->stock_commission = $stock_commission;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->stockGroup->getTreeSelectOption();

        if (user_privileges_check('master', 'stock_group__commission', 'alter_role')) {
            return view('admin.master.stock_commission.index', compact('select_option_tree'));
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
        if (user_privileges_check('master', 'stock_group__commission', 'alter_role')) {
            try {
                $data = $this->stock_commission->getTree($request);

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
        if (user_privileges_check('master', 'stock_group__commission', 'create_role')) {
            try {
                $data = $this->stock_commission->StoreStockCommission($request);

                return RespondWithSuccess('stock commission create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock commission create successfully', $e->getMessage(), 400);
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
        if (user_privileges_check('master', 'stock_group__commission', 'alter_role')) {
            try {
                $data = $this->stock_commission->updateStockCommission($request, $id);

                return RespondWithSuccess('stock commission update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock  commission not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'stock_group__commission', 'delete_role')) {
            try {
                $data = $this->stock_commission->deleteStockCommission($id);

                return RespondWithSuccess('stock commission delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock commission  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
