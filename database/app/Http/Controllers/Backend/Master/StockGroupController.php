<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockGroup\StockGroupStoreRequest;
use App\Http\Requests\StockGroup\StockGroupUpdateRequest;
use App\Repositories\Backend\Master\StockGroupRepository;
use Exception;

class StockGroupController extends Controller
{
    private $stockGroup;

    public function __construct(StockGroupRepository $stockGroupRepository)
    {
        $this->stockGroup = $stockGroupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->stockGroup->getTreeSelectOption();

        if (user_privileges_check('master', 'Stock Group', 'display_role')) {
            return view('admin.master.stock_group.index', compact('select_option_tree'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the tree view.
     *
     * @return \Illuminate\Http\Response
     */
    public function treeView()
    {
        if (user_privileges_check('master', 'Stock Group', 'display_role')) {
            try {
                $data = $this->stockGroup->getTreeStockGroup();

                return RespondWithSuccess('All stock group tree list show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock group tree list not show successfully !!', $e->getMessage(), 404);
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
    public function planView()
    {
        if (user_privileges_check('master', 'Stock Group', 'display_role')) {
            try {
                $data = $this->stockGroup->getStockGroupOfIndex();

                return RespondWithSuccess('All stock group  list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All stock group  list not show successfully !!', $e->getMessage(), 404);
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
    public function store(StockGroupStoreRequest $request)
    {
        if (user_privileges_check('master', 'Stock Group', 'create_role')) {
            try {
                $data = $this->stockGroup->storeStockGroup($request);

                return RespondWithSuccess('stock group create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock group not create successfully', $e->getMessage(), 404);
            }
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
        if (user_privileges_check('master', 'Stock Group', 'alter_role')) {
            try {
                $data = $this->stockGroup->getStockGroupId($id);

                return RespondWithSuccess('stock group show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock group  not show successfully', $e->getMessage(), 404);
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
    public function update(StockGroupUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Stock Group', 'alter_role')) {
            try {
                $data = $this->stockGroup->updateStockGroup($request, $id);

                return RespondWithSuccess('stock group update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('stock group not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Stock Group', 'delete_role')) {
            try {
                $data = $this->stockGroup->deleteStockGroup($id);

                return RespondWithSuccess('distribution delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('distribution not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
