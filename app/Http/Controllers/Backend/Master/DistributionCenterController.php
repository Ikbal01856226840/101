<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Distribution\DistributionStoreRequest;
use App\Http\Requests\Distribution\DistributionUpdateRequest;
use App\Repositories\Backend\Master\DistributionCenterRepository;
use Exception;

class DistributionCenterController extends Controller
{
    private $distributionCenter;

    public function __construct(DistributionCenterRepository $distributionCenterRepository)
    {
        $this->distributionCenter = $distributionCenterRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->distributionCenter->getTreeSelectOption();

        if (user_privileges_check('master', 'Distribution Center', 'display_role')) {
            return view('admin.master.distribution_center.index', compact('select_option_tree'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show distribution center.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDistributionCenter()
    {
        if (user_privileges_check('master', 'Distribution Center', 'display_role')) {
            try {
                $data = $this->distributionCenter->getDistributionCenterOfIndex();

                return RespondWithSuccess('All distribution list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All distribution list not show successfully !!', $e->getMessage(), 404);
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
    public function store(DistributionStoreRequest $request)
    {
        if (user_privileges_check('master', 'Distribution Center', 'create_role')) {
            try {
                $data = $this->distributionCenter->StoreDistributionCenter($request);

                return RespondWithSuccess('distribution create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('distribution not create successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Distribution Center', 'alter_role')) {
            try {
                $data = $this->distributionCenter->getDistributionCenterId($id);

                return RespondWithSuccess('distribution show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('distribution not show successfully', $e->getMessage(), 400);
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
    public function update(DistributionUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Distribution Center', 'alter_role')) {
            try {
                $data = $this->distributionCenter->updateDistributionCenter($request, $id);

                return RespondWithSuccess('distribution update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('distribution not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Distribution Center', 'delete_role')) {
            try {
                $data = $this->distributionCenter->deleteDistributionCenter($id);

                return RespondWithSuccess('distribution delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('distribution not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }

    }
}
