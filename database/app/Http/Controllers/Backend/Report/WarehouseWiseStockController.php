<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use App\Repositories\Backend\Report\WarehouseWiseStockRepository;
use Exception;
use Illuminate\Http\Request;

class WarehouseWiseStockController extends Controller
{
    private $stockGroupRepository;

    private $godownRepository;

    private $warehouseWiseStockRepository;

    public function __construct(StockGroupRepository $stockGroupRepository, GodownRepository $godownRepository, WarehouseWiseStockRepository $warehouseWiseStockRepository)
    {
        $this->stockGroupRepository = $stockGroupRepository;
        $this->godownRepository = $godownRepository;
        $this->warehouseWiseStockRepository = $warehouseWiseStockRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function warehouseWiseStockShow()
    {
        if (user_privileges_check('report', 'WarehousewiseStock', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->getGodownOfIndex();

            return view('admin.report.inventrory.warehouse_wise_stock', compact('stock_group', 'godowns'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function warehouseWiseStock(Request $request)
    {
        // if (user_privileges_check('report', 'WarehousewiseStock', 'display_role')) {
            try {
                $data = $this->warehouseWiseStockRepository->getWarehouseWiseStockOfIndex($request);

                return RespondWithSuccess('warehouse wise  stock successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('warehouse wise  stock successfully', $e->getMessage(), 400);
            }
        // } else {
        //     abort(403);
        // }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function warehouseWithoutDamageGodownWiseStockShow()
    {
        if (user_privileges_check('report', 'WarehouseWithoutDamage', 'display_role')) {
            $stock_group = $this->stockGroupRepository->getTreeSelectOption('under_id');
            $godowns = $this->godownRepository->withoutDamagegodown();

            return view('admin.report.inventrory.warehouse_without_damage_stock', compact('stock_group', 'godowns'));
        } else {
            abort(403);
        }

    }
}
