<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ledger\LedgerStoreRequest;
use App\Http\Requests\Ledger\LedgerUpdateRequest;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use Exception;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    private $groupChart;

    private $ledgerHead;

    public function __construct(GroupChartRepository $groupChart, LegerHeadRepository $ledgerHead)
    {
        $this->groupChart = $groupChart;
        $this->ledgerHead = $ledgerHead;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group_chart_id = $this->groupChart->getTreeSelectOption('under_id');

        if (user_privileges_check('master', 'Ledger', 'display_role')) {
            return view('admin.master.ledger.index', compact('group_chart_id'));
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
        if (user_privileges_check('master', 'Ledger', 'display_role')) {
            if ($request->ajax()) {
                $data = $this->ledgerHead->getTree();

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
        if (user_privileges_check('master', 'Ledger', 'display_role')) {
            try {
                $data = $this->ledgerHead->getLegerHeadOfIndex();

                return RespondWithSuccess('All ledger list  show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All ledger list not show successfully !!', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in create.
     */
    public function create()
    {
        $group_chart_id = $this->groupChart->getTreeSelectOption();

        if (user_privileges_check('master', 'Ledger', 'create_role')) {
            return view('admin.master.ledger.create', compact('group_chart_id'));
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
    public function store(LedgerStoreRequest $request)
    {
        if (user_privileges_check('master', 'Ledger', 'create_role')) {
            try {
                $data = $this->ledgerHead->StoreLegerHead($request);

                return RespondWithSuccess('ledger create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('ledger create not successfully', $e->getMessage(), 404);
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
        $group_chart_id = $this->groupChart->getTreeSelectOption();
        $data = $this->ledgerHead->getLegerHeadId($id);

        if (user_privileges_check('master', 'Ledger', 'alter_role')) {
            return view('admin.master.ledger.edit', compact('data', 'group_chart_id'));
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
    public function update(LedgerUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Ledger', 'alter_role')) {
            try {
                $data = $this->ledgerHead->updateLegerHead($request, $id);

                return RespondWithSuccess('ledger update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('ledger not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Ledger', 'delete_role')) {
            try {
                $data = $this->ledgerHead->deleteLegerHead($id);

                return RespondWithSuccess('ledger delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('ledger not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the searching view.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchingData(Request $request)
    {
        if (user_privileges_check('master', 'Ledger', 'display_role')) {
            if ($request->ajax()) {
                $data = $this->ledgerHead->searchingData($request);

                return response()->json($data);
            }
        } else {
            abort(403);
        }

    }
}
