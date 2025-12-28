<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Components\ComponentsStoreRequest;
use App\Http\Requests\Components\ComponentsUpdateRequest;
use App\Repositories\Backend\Master\ComponentsRepository;
use Exception;

class ComponentsController extends Controller
{
    private $components;

    public function __construct(ComponentsRepository $componentsRepository)
    {
        $this->components = $componentsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('master', 'Components', 'display_role')) {
            return view('admin.master.components.index');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getComponents()
    {
        if (user_privileges_check('master', 'Components', 'display_role')) {
            try {
                $data = $this->components->getComponentsOfIndex();

                return RespondWithSuccess('All components list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All components list not show successfully !!', $e->getMessage(), 404);
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
    public function store(ComponentsStoreRequest $request)
    {
        if (user_privileges_check('master', 'Components', 'create_role')) {
            try {
                $data = $this->components->storeComponents($request);

                return RespondWithSuccess('components create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('components not create successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Components', 'alter_role')) {
            try {
                $data = $this->components->getComponentsId($id);

                return RespondWithSuccess('components show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('components not show successfully', $e->getMessage(), 400);
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
    public function update(ComponentsUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Components', 'alter_role')) {
            try {
                $data = $this->components->updateComponents($request, $id);

                return RespondWithSuccess('components update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('components not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Components', 'delete_role')) {
            try {
                $data = $this->components->deleteComponents($id);

                return RespondWithSuccess('components delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('components not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
