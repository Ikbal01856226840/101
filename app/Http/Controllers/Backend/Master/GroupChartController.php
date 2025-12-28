<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupChart\GroupChartStoreRequest;
use App\Http\Requests\GroupChart\GroupChartUpdate;
use App\Repositories\Backend\Master\GroupChartRepository;
use Exception;
use Illuminate\Http\Request;

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

        $group_chart_id = $this->groupChart->getTreeSelectOption();

        if (user_privileges_check('master', 'Group', 'display_role')) {
            return view('admin.master.group_chart.index', compact('group_chart_id'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the tree view.
     *
     * @return \Illuminate\Http\Response
     */
    public function treeView(Request $request)
    {
        if (user_privileges_check('master', 'Group', 'display_role')) {
            if ($request->ajax()) {
                $data = $this->groupChart->getTree();

                return response()->json($data);
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
        if (user_privileges_check('master', 'Group', 'display_role')) {
            try {
                $data = $this->groupChart->getGroupChartOfIndex();

                return RespondWithSuccess('All group chart list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All group chart list not show successfully !!', $e->getMessage(), 400);
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
    public function store(GroupChartStoreRequest $request)
    {
        if (user_privileges_check('master', 'Group', 'create_role')) {
            try {
                $data = $this->groupChart->StoreGroupChart($request);

                return RespondWithSuccess('group chart create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('group chart not create successfully', $e->getMessage(), 400);
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
        if (user_privileges_check('master', 'Group', 'alter_role')) {
            try {
                $data = $this->groupChart->getGroupChartId($id);

                return RespondWithSuccess('group chart show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('group chart not show successfully', $e->getMessage(), 400);
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
    public function update(GroupChartUpdate $request, $id)
    {
        if (user_privileges_check('master', 'Group', 'alter_role')) {
            try {
                $data = $this->groupChart->updateGroupChart($request, $id);

                return RespondWithSuccess('group chart update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('group chart not  update successfully', $e->getMessage(), 400);
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
        if (user_privileges_check('master', 'Group', 'delete_role')) {
            try {
                $data = $this->groupChart->deleteGroupChart($id);

                return RespondWithSuccess('group chart delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('group chart not  delete successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNatureGroup(Request $request)
    {
        $get_nature_group_id = $this->groupChart->get_nature_group($request);

        return response()->json($get_nature_group_id);
    }
}
