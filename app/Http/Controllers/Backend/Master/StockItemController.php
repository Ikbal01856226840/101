<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockItem\StockItemStoreRequest;
use App\Http\Requests\StockItem\StockItemUpdateRequest;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\MeasureRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Master\StockItemRepository;
use Exception;

class StockItemController extends Controller
{
    private $stockItem;

    private $stockGroup;

    private $godown;

    private $measureRepository;

    public function __construct(StockItemRepository $stockItemRepository, StockGroupRepository $stockGroupRepository, GodownRepository $godownRepository, MeasureRepository $measureRepository)
    {
        $this->stockItem = $stockItemRepository;
        $this->stockGroup = $stockGroupRepository;
        $this->godown = $godownRepository;
        $this->measureRepository = $measureRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->stockGroup->getTreeSelectOption();

        if (user_privileges_check('master', 'Stock Item', 'display_role')) {
            return view('admin.master.stock_item.index', compact('select_option_tree'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the tree view.
     *
     * @return \Illuminate\Http\Response
     */
    public function treeViewItem()
    {

        if (user_privileges_check('master', 'Stock Item', 'display_role')) {
            try {
                $data = $this->stockItem->getTreeItem();

                return RespondWithSuccess('All stock item tree list show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock item tree list not show successfully !!', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the plain view.
     *
     * @return \Illuminate\Http\Response
     */
    public function planViewItem()
    {
        if (user_privileges_check('master', 'Stock Item', 'display_role')) {
            try {
                $data = $this->stockItem->getStockItemOfIndex();

                return RespondWithSuccess('All stock  item list  show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock  item list not show successfully !!', $e->getMessage(), 400);
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
    public function store(StockItemStoreRequest $request)
    {
        if (user_privileges_check('master', 'Stock Item', 'create_role')) {
            try {
                $data = $this->stockItem->storeStockItem($request);

                return RespondWithSuccess('stock item create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item not create successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in create.
     */
    public function create()
    {
        $sizes = $this->measureRepository->getMeasureOfIndex();
        $godowns = $this->godown->getGodownOfIndex();
        $select_option_tree = $this->stockGroup->getTreeSelectOption();

        if (user_privileges_check('master', 'Stock Item', 'create_role')) {
            return view('admin.master.stock_item.create', compact('select_option_tree', 'godowns', 'sizes'));
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $data = $this->stockItem->getStockItemId($id);
        $godowns = $this->godown->getGodownOfIndex();
        $stock_group_tree = $this->stockGroup->getTreeStockGroup();
        $sizes = $this->measureRepository->getMeasureOfIndex();

        if (user_privileges_check('master', 'Stock Item', 'alter_role')) {
            return view('admin.master.stock_item.edit', compact('data', 'stock_group_tree', 'godowns', 'sizes'));
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
    public function update(StockItemUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Stock Item', 'alter_role')) {
            try {
                $data = $this->stockItem->updateStockItem($request, $id);

                return RespondWithSuccess('stock item update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock item not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Stock Item', 'delete_role')) {
            try {
                $data = $this->stockItem->deleteStockItem($id);

                return RespondWithSuccess('distribution delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('distribution not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
