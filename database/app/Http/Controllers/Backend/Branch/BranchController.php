<?php

namespace App\Http\Controllers\Backend\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\BranchStoreRequest;
use App\Http\Requests\Branch\BranchUpdateRequest;
use App\Repositories\Backend\BranchRepository;
use Exception;

class BranchController extends Controller
{
    private $branch;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branch = $branchRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function branchShow()
    {
        if (user_privileges_check('Global Setup', 'unit_or_branch', 'display_role')) {
            return view('admin.company.index_branch');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('Global Setup', 'unit_or_branch', 'display_role')) {
            try {
                $data = $this->branch->getBranchOfIndex();

                return RespondWithSuccess('branch create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('branch not create successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (user_privileges_check('Global Setup', 'unit_or_branch', 'create_role')) {
            return view('admin.company.create_branch');
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
    public function store(BranchStoreRequest $request)
    {
        if (user_privileges_check('Global Setup', 'unit_or_branch', 'create_role')) {
            try {
                $data = $this->branch->StoreBranch($request);

                return RespondWithSuccess('branch create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('branch not create successfully', $e->getMessage(), 404);
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
        $data = $this->branch->getBranchId($id);
        if (user_privileges_check('Global Setup', 'unit_or_branch', 'alter_role')) {
            return view('admin.company.edit_branch', compact('data'));
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
    public function update(BranchUpdateRequest $request, $id)
    {
        if (user_privileges_check('Global Setup', 'unit_or_branch', 'alter_role')) {
            try {
                $data = $this->branch->updateBranch($request, $id);

                return RespondWithSuccess('branch update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('branch  update successfully', $e->getMessage(), 400);
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
        if (user_privileges_check('Global Setup', 'unit_or_branch', 'delete_role')) {
            try {
                $data = $this->branch->deleteBranch($id);

                return RespondWithSuccess('branch  delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('branch not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function showCompany()
    {
        return view('admin.company.company_show');
    }
}
