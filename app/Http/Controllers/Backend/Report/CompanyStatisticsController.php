<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\CompanyStatisticsRepository;
use Exception;
use Illuminate\Http\Request;

class CompanyStatisticsController extends Controller
{

    private $companyStatisticsRepository;


    public function __construct(CompanyStatisticsRepository $companyStatisticsRepository)
    {
        $this->companyStatisticsRepository = $companyStatisticsRepository;


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyStatisticsShow()
    {
        if (user_privileges_check('report', 'CompanyStatistics', 'display_role')) {

            return view('admin.report.company_statistics.company_statistics');
        } else {
            abort(403,'Access Denied — You don’t have permission to view this page.');
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyStatistics(Request $request)
    {
        if (user_privileges_check('report', 'CompanyStatistics', 'display_role')) {
            try {
                $data = $this->companyStatisticsRepository->getCompanyStatisticsOfIndex($request);

                return RespondWithSuccess('Company Statistics show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Company Statistics not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403,'Access Denied — You don’t have permission to view this page.');
        }
    }

    
}
