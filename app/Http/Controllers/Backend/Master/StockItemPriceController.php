<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockItemPrice\StockItemPriceStoreRequest;
use App\Http\Requests\StockItemPrice\StockItemPriceUpdateRequest;
use App\Repositories\Backend\Master\SockItemPriceRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use Exception;
use Illuminate\Http\Request;

class StockItemPriceController extends Controller
{
    private $stockGroup;

    private $stock_item_price;

    public function __construct(StockGroupRepository $stockGroupRepository, SockItemPriceRepository $sockItemPriceRepository)
    {
        $this->stockGroup = $stockGroupRepository;
        $this->stock_item_price = $sockItemPriceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->stockGroup->getTreeSelectOption();

        if (user_privileges_check('master', 'Selling Price', 'display_role')) {
            return view('admin.master.stock_item_price.index', compact('select_option_tree'));
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
        if (user_privileges_check('master', 'Selling Price', 'display_role')) {
            try {
                $data = $this->stock_item_price->getTree($request);

                return RespondWithSuccess('All stock Item Price  list show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock item Price  list not show successfully !!', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockItemPriceStoreRequest $request)
    {
        if (user_privileges_check('master', 'Selling Price', 'create_role')) {
            try {
                $data = $this->stock_item_price->StockItemPrice($request);

                return RespondWithSuccess('stock item price create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item price create not successfully !!', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StockItemPriceUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Selling Price', 'alter_role')) {
            try {
                $data = $this->stock_item_price->updateStockItemPrice($request, $id);

                return RespondWithSuccess('stock item price update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item price not  update successfully !!', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Selling Price', 'delete_role')) {
            try {
                $data = $this->stock_item_price->deleteStockItemPrice($id);

                return RespondWithSuccess('stock item price delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item price  delete successfully  !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
