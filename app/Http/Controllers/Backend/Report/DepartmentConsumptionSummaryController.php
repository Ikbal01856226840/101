<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\DepartmentConsumptionSummaryRepository;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;

class DepartmentConsumptionSummaryController extends Controller
{
    private $departmentConsumptionSummaryRepository;
    private $voucher_setup;

    public function __construct(DepartmentConsumptionSummaryRepository $departmentConsumptionSummaryRepository, Voucher_setup $voucher_setup)
    {
        $this->departmentConsumptionSummaryRepository = $departmentConsumptionSummaryRepository;
        $this->voucher_setup = $voucher_setup;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('report', 'DepartmentConsumptionSummary', 'display_role')) {
            $departments = $this->voucher_setup->departments();
            return view('admin.report.inventrory.department_consumption_summary', compact('departments'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDepartmentConsumptionSummary(Request $request)
    {
        if (user_privileges_check('report', 'DepartmentConsumptionSummary', 'display_role')) {
            try {
                $data = $this->departmentConsumptionSummaryRepository->getDepartmentConsumptionSummary($request);
                return RespondWithSuccess('Department Consumption Summary show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Department Consumption Summary show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    public function departmentConsumption(Request $request){
        if (user_privileges_check('report', 'DepartmentConsumptionSummary', 'display_role')) {
            $departments = $this->voucher_setup->departments();
            $from_date = $request->from_date??date('Y-m-d');
            $to_date = $request->to_date??date('Y-m-d');
            $department = $request->department??'';
            $stock_item_id = $request->stock_item_id??'';
            return view('admin.report.inventrory.department_consumption', compact('departments','from_date','to_date','department','stock_item_id'));
        } else {
            abort(403);
        }
    }

    public function getDepartmentConsumption(Request $request)
    {
        if (user_privileges_check('report', 'DepartmentConsumptionSummary', 'display_role')) {
            try {
                $data = $this->departmentConsumptionSummaryRepository->getDepartmentConsumption($request);
                return RespondWithSuccess('Department Consumption show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Department Consumption show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

}
