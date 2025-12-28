<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Godown\GodownStoreRequest;
use App\Http\Requests\Godown\GodownUpdateRequest;
use App\Repositories\Backend\Master\GodownRepository;
use Exception;

class GodownController extends Controller
{
    private $godown;

    public function __construct(GodownRepository $godownRepository)
    {
        $this->godown = $godownRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('master', 'Godown', 'display_role')) {
            return view('admin.master.godown.index');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show godown.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGodown()
    {
        if (user_privileges_check('master', 'Godown', 'display_role')) {
            try {
                $data = $this->godown->getGodownOfIndex();

                return RespondWithSuccess('All Godown list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All Godown list not show successfully !!', $e->getMessage(), 404);
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
    public function store(GodownStoreRequest $request)
    {
        if (user_privileges_check('master', 'Godown', 'create_role')) {
            try {
                $data = $this->godown->StoreGodown($request);

                return RespondWithSuccess('godown create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('godown not create successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Godown', 'alter_role')) {
            try {
                $data = $this->godown->getGodownId($id);

                return RespondWithSuccess('godown show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('godown not show successfully', $e->getMessage(), 400);
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
    public function update(GodownUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Godown', 'alter_role')) {
            try {
                $data = $this->godown->updateGodown($request, $id);

                return RespondWithSuccess('godown update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('godown not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Godown', 'delete_role')) {
            try {
                $data = $this->godown->deleteGodown($id);

                return RespondWithSuccess('godown delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('godown not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
