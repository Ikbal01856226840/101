<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\SupplierStoreRequest;
use App\Http\Requests\Supplier\SupplierUpdateRequest;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\SupplierRepository;
use App\Services\Tree;
use Exception;

class SupplierController extends Controller
{
    private $supplierRepository;

    private $groupChartRepository;

    private $tree;

    public function __construct(SupplierRepository $supplierRepository, GroupChartRepository $groupChartRepository, Tree $tree)
    {
        $this->supplierRepository = $supplierRepository;
        $this->groupChartRepository = $groupChartRepository;
        $this->tree = $tree;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ledger_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);

        if (user_privileges_check('master', 'Supplier', 'display_role')) {
            return view('admin.master.supplier.index', compact('ledger_tree'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSupplier()
    {
        if (user_privileges_check('master', 'Supplier', 'display_role')) {
            try {
                $data = $this->supplierRepository->getSupplierOfIndex();

                return RespondWithSuccess('All supplier list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All supplier list not show successfully !!', $e->getMessage(), 404);
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
    public function store(SupplierStoreRequest $request)
    {
        if (user_privileges_check('master', 'Supplier', 'create_role')) {
            try {
                $data = $this->supplierRepository->storeSupplier($request);

                return RespondWithSuccess('supplier create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('supplier not create successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Supplier', 'alter_role')) {
            try {
                $data = $this->supplierRepository->getSupplierId($id);

                return RespondWithSuccess('supplier show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('supplier not show successfully', $e->getMessage(), 400);
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
    public function update(SupplierUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Supplier', 'alter_role')) {
            try {
                $data = $this->supplierRepository->updateSupplier($request, $id);

                return RespondWithSuccess('supplier update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('supplier not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Supplier', 'delete_role')) {
            try {
                $data = $this->supplierRepository->deleteSupplier($id);

                return RespondWithSuccess('supplier delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('supplier not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
