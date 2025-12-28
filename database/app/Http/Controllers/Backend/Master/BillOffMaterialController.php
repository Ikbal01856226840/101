<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillOfMaterial\BillOfMaterialStoreRequest;
use App\Http\Requests\BillOfMaterial\BillOfMaterialUpdateRequest;
use App\Repositories\Backend\Master\BillOfMaterialRepository;
use Exception;

class BillOffMaterialController extends Controller
{
    private $billOfMaterialRepository;

    public function __construct(BillOfMaterialRepository $billOfMaterialRepository)
    {
        $this->billOfMaterialRepository = $billOfMaterialRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('master', 'bill_of_material', 'display_role')) {
            return view('admin.master.bill_of_material.index');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show godown.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBillOffMaterialData()
    {
        if (user_privileges_check('master', 'bill_of_material', 'display_role')) {
            try {
                $data = $this->billOfMaterialRepository->getBillOfMaterialOfIndex();

                return RespondWithSuccess('All Bill Of Material list not show successful !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All Bill Of Material list not show successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function create()
    {

        if (user_privileges_check('master', 'bill_of_material', 'create_role')) {
            return view('admin.master.bill_of_material.create');
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
    public function store(BillOfMaterialStoreRequest $request)
    {
        if (user_privileges_check('master', 'bill_of_material', 'create_role')) {
            try {
                $data = $this->billOfMaterialRepository->storeBillOfMaterial($request);

                return RespondWithSuccess('Bill Of Material create successful !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Bill Of Material not create successful !!', $e->getMessage(), 404);
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
        $data = $this->billOfMaterialRepository->getBillOfMaterialId($id);
        $bill_of_material_details = $this->billOfMaterialRepository->getBillOfMaterialDetailsId($id);

        if (user_privileges_check('master', 'bill_of_material', 'alter_role')) {
            return view('admin.master.bill_of_material.edit', compact('data', 'bill_of_material_details'));
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
    public function update(BillOfMaterialUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'bill_of_material', 'alter_role')) {
            try {
                $data = $this->billOfMaterialRepository->updateBillOfMaterial($request, $id);

                return RespondWithSuccess('Bill Of Material update successful !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Bill Of Material not  update successful', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'bill_of_material', 'delete_role')) {
            try {
                $data = $this->billOfMaterialRepository->deleteBillOfMaterial($id);

                return RespondWithSuccess('Bill Of Material delete successful !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Bill Of Material  not  delete successful', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
