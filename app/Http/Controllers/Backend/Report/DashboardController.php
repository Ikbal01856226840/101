<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\DashboardRepository;
use App\Services\Voucher_setup\Voucher_setup;
use App\Repositories\Backend\AuthRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    private $dashboardRepository;

    private $voucher_setup;

    private $authRepository;

    public function __construct(DashboardRepository $dashboardRepository,Voucher_setup $voucher_setup,AuthRepository $authRepository)
    {
        $this->dashboardRepository=$dashboardRepository;
         $this->voucher_setup=$voucher_setup;
        $this->authRepository = $authRepository;
    }

    public function index()
    {
        $inventory_summary=$this->dashboardRepository->idWiseReportAccess(1,1);

        $general_reports=$this->dashboardRepository->idWiseReportAccess(2,2);
        $movement_analysis_2=$this->dashboardRepository->idWiseReportAccess(3,3);
        $party_ledger=$this->dashboardRepository->idWiseReportAccess(4,4);
        $account_summary=$this->dashboardRepository->idWiseReportAccess(5,5);
        $inventory_books=$this->dashboardRepository->idWiseReportAccess(6,6);
        $manufacturing_reports=$this->dashboardRepository->idWiseReportAccess(7,7);
        $company_statistics=$this->dashboardRepository->idWiseReportAccess(8,8);
        $movement_analysis_1=$this->dashboardRepository->idWiseReportAccess(9,9);
        $pos=$this->dashboardRepository->idWiseReportAccess(10,10);

        return view('admin.report.report_dashboard',compact('inventory_summary','general_reports','movement_analysis_2','party_ledger','account_summary','inventory_books','manufacturing_reports','company_statistics','movement_analysis_1','pos'));
    }

    public function ledgerSearch(){
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        $search = $_GET['name'];
        if (array_sum(array_map('intval',explode(' ', $get_user->agar))) != 0 && Auth()->user()->user_level!= 1) {
             $data = $this->voucher_setup->group_chart($get_user->agar, $search);
        }else{
              $data = $this->voucher_setup->ledger_head_searching($search);
        }
        
        echo json_encode($data);
        exit();
    }

    public function searchingPartyLedgerName(Request $request){
        $data=DB::table('ledger_head')->where('ledger_head_id',$request->id)->first();
        if (!empty($data)) {
            echo json_encode($data);
            exit();
        } 
    }
}
