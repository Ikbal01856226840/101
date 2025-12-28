<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ledger\LedgerStoreRequest;
use App\Http\Requests\Ledger\LedgerUpdateRequest;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use Illuminate\Http\Request;
use App\Services\Tree;
use Exception;


class LedgerController extends Controller
{
    private $groupChart;

    private $ledgerHead;

    private $tree;

    public function __construct(GroupChartRepository $groupChart, LegerHeadRepository $ledgerHead,Tree $tree)
    {
        $this->groupChart = $groupChart;
        $this->ledgerHead = $ledgerHead;
        $this->tree = $tree;
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
        $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChart->getGroupChartOfIndex(), true), true), 0);
        if (user_privileges_check('master', 'Ledger', 'create_role')) {
            return view('admin.master.ledger.create', compact('group_chart_id','ledgers'));
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
        $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChart->getGroupChartOfIndex(), true), true), 0);
        if (user_privileges_check('master', 'Ledger', 'alter_role')) {
            return view('admin.master.ledger.edit', compact('data', 'group_chart_id','ledgers'));
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
    public function editdata($id)
    {
    
        if (user_privileges_check('master', 'Ledger', 'alter_role')) {
            try {
                $data = $this->ledgerHead->getLegerHeadId($id);
                return RespondWithSuccess('ledger show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('ledger show not successfully', $e->getMessage(), 404);
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


    public function getAutoAlias(){
        if (user_privileges_check('master', 'Ledger', 'create_role')) {
            try {
                $data = $this->ledgerHead->getAutoAlias();
                return RespondWithSuccess('ledger alias successfully !! ', $data, 200);
            } catch (Exception $e) {
                return RespondWithError('ledger alias not successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function duplicateAliasCheck(Request $request){
        if (user_privileges_check('master', 'Ledger', 'create_role')) {        
            try {
                $data = $this->ledgerHead->duplicateAliasCheck($request);
                return RespondWithSuccess('ledger alias successfully !! ', $data, 200);
            } catch (Exception $e) {
                return RespondWithError('ledger alias not successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }

    }


    public function getLastManualAlias(){
        if (user_privileges_check('master', 'Ledger', 'create_role')) {
            try {
                $data = $this->ledgerHead->getLastManualAlias();
                return RespondWithSuccess('ledger code successfully !! ', $data, 200);
            } catch (Exception $e) {
                return RespondWithError('ledger code not successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function setAlias()
    {
        $group_chart_id = $this->groupChart->getTreeSelectOption('under_id');
        $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChart->getGroupChartOfIndex(), true), true), 0);
        $nextAlias = $this->ledgerHead->getAutoAlias();
        if (user_privileges_check('master', 'Ledger', 'alter_role')) {
            return view('admin.master.ledger.set_alias', compact('group_chart_id','ledgers','nextAlias'));
        } else {
            abort(403);
        }
    }    
    public function getSetAliasData(Request $request)
    {
        $group_chart_id = $this->groupChart->getTreeSelectOption('under_id');
        $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChart->getGroupChartOfIndex(), true), true), 0);
        $nextAlias = $this->ledgerHead->getAutoAlias();
        $id=$request->id;
        $alias_type=$request->alias_type;
        if (user_privileges_check('master', 'Ledger', 'alter_role')) {
            return view('admin.master.ledger.set_alias', compact('group_chart_id','ledgers','nextAlias','id','alias_type'));
        } else {
            abort(403);
        }
    }

    public function aliasUpdate(Request $request){
        if (user_privileges_check('master', 'Ledger', 'alter_role')) {
            try {
                $data = $this->ledgerHead->aliasUpdate($request);
                return RespondWithSuccess('Alias set successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Alias not  set successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
