<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Repositories\Backend\Report\BankReconciliationRepository;
use App\Services\Tree;

class BankReconciliationController extends Controller
{
    private $bankReconciliationRepository;

    private $tree;
    public function __construct(Tree $tree,BankReconciliationRepository $bankReconciliationRepository)
    {
        $this->tree = $tree;
        $this->bankReconciliationRepository= $bankReconciliationRepository;
    }


    public function bankReconciliationShow(){
        if (user_privileges_check('report', 'BankReconciliation', 'display_role')) {

            $data = json_decode(json_encode($this->tree->group_chart_tree_row_query(9), true), true);
            $keys = array_keys(array_column($this->tree->group_chart_tree_row_query(9), 'lvl'), 1);
            $new_array = array_map(function ($k) use ($data) {
                return $data[$k];
            }, $keys);

            $ledgers = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($data, true), true), $new_array[0]['under']);
            return view('admin.report.company_statistics.bank_reconciliation',compact('ledgers'));
        } else {
            abort(403);
        }
    }

    public function bankReconciliation(Request $request){
        if (user_privileges_check('report', 'BankReconciliation', 'display_role')) {
            try {
                $data = $this->bankReconciliationRepository->getBankReconciliationOfIndex($request);

                return RespondWithSuccess('bank reconciliation show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('bank reconciliation not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
    public function bankReconciliationStore(Request $request){
      
        if (user_privileges_check('report', 'BankReconciliation', 'display_role')) {
            try {
                $data = $this->bankReconciliationRepository->bankReconciliationStore($request);
                return RespondWithSuccess('bank reconciliation create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('bank reconciliation not create successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
