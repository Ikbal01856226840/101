<?php

namespace App\Http\Controllers\BackendApi\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupChart\GroupChartStoreRequest;
use App\Repositories\Backend\Master\GroupChartRepository;
use Exception;

class GroupChartController extends Controller
{
    private $groupChart;

    public function __construct(GroupChartRepository $groupChart)
    {
        $this->groupChart = $groupChart;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->groupChart->getGroupChartOfIndex();

            return View('admin.group_chart_index', compact('data'));
        } catch (Exception $e) {
            return $this->RespondWithError('All Group Chart list not show successfull !!', $e->getMessage(), 404);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupChartStoreRequest $request)
    {

        try {
            $data = $this->groupChart->StoreGroupChart($request);

            return RespondWithSuccess('group chart create successfull !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('group chart not create successfull', $e->getMessage(), 404);
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
        try {
            $data = $this->groupChart->getGroupChartId($id);

            return RespondWithSuccess('group chart show successfull !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('group chart not show successfull', $e->getMessage(), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupChartStoreRequest $request, $id)
    {
        dd($request->all());

        try {
            $data = $this->groupChart->updateGroupChart($request, $id);

            return RespondWithSuccess('group chart update successfull !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('group chart not  update successfull', $e->getMessage(), 404);
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

        try {
            $data = $this->groupChart->deleteGroupChart($id);

            return RespondWithSuccess('group chart delete successfull !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('group chart not  delete successfull', $e->getMessage(), 404);
        }
    }
}
