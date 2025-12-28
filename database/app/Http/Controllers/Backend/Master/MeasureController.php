<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Measure\MeasureStoreRequest;
use App\Http\Requests\Measure\MeasureUpdateRequest;
use App\Repositories\Backend\Master\MeasureRepository;
use Exception;

class MeasureController extends Controller
{
    private $measure;

    public function __construct(MeasureRepository $measureRepository)
    {
        $this->measure = $measureRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('master', 'Units of Measure', 'display_role')) {
            return view('admin.master.unit_of_measure.index');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show godown.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMeasure()
    {
        if (user_privileges_check('master', 'Units of Measure', 'display_role')) {
            try {
                $data = $this->measure->getMeasureOfIndex();

                return RespondWithSuccess('All measure list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All measure list not show successfully !!', $e->getMessage(), 404);
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
    public function store(MeasureStoreRequest $request)
    {
        if (user_privileges_check('master', 'Units of Measure', 'create_role')) {
            try {
                $data = $this->measure->StoreMeasure($request);

                return RespondWithSuccess('Measure create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Measure not create successfully !!', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Units of Measure', 'alter_role')) {
            try {
                $data = $this->measure->getMeasureId($id);

                return RespondWithSuccess('Measure show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Measure not show successfully', $e->getMessage(), 404);
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
    public function update(MeasureUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Units of Measure', 'alter_role')) {
            try {
                $data = $this->measure->updateMeasure($request, $id);

                return RespondWithSuccess('Measure update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Measure not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Units of Measure', 'delete_role')) {
            try {
                $data = $this->measure->deleteMeasure($id);

                return RespondWithSuccess('Measure delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Measure  not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
?>

