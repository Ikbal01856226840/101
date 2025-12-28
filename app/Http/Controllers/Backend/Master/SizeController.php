<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Size\SizeStoreRequest;
use App\Http\Requests\Size\SizeUpdateRequest;
use App\Repositories\Backend\Master\SizeRepository;
use Exception;

class SizeController extends Controller
{
    private $size;

    public function __construct(SizeRepository $sizeRepository)
    {
        $this->size = $sizeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('master', 'Unit of Size', 'display_role')) {
            return view('admin.master.unit_of_size.index');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show godown.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSize()
    {
        if (user_privileges_check('master', 'Unit of Size', 'display_role')) {
            try {
                $data = $this->size->getSizeOfIndex();

                return RespondWithSuccess('All Size list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All Size list not show successfully !!', $e->getMessage(), 404);
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
    public function store(SizeStoreRequest $request)
    {
        if (user_privileges_check('master', 'Unit of Size', 'create_role')) {
            try {
                $data = $this->size->StoreSize($request);

                return RespondWithSuccess('Size create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Size not create successfully !!', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Unit of Size', 'alter_role')) {
            try {
                $data = $this->size->getSizeId($id);

                return RespondWithSuccess('Size show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Size not show successfully !!', $e->getMessage(), 404);
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
    public function update(SizeUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Unit of Size', 'alter_role')) {
            try {
                $data = $this->size->updateSize($request, $id);

                return RespondWithSuccess('Size update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Size not  update successfully !!', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Unit of Size', 'delete_role')) {
            try {
                $data = $this->size->deleteSize($id);

                return RespondWithSuccess('Size delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Size not  delete successfully !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
?>

