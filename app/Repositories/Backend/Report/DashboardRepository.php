<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function idWiseReportAccess($parent_id,$child_id)
    {

        return  DB::select(
            "SELECT report_id,
                    report_name,
                    report_title,
                    page_name
                FROM   reports
                WHERE  reports.report_name!='Reserved' AND (report_id=$parent_id OR parent_group=$child_id)  AND (reports.report_status = 1
                        OR EXISTS (SELECT report_unit_setup.reports_id
                                FROM   unit_branch_setup
                                        INNER JOIN report_unit_setup
                                                ON report_unit_setup.id_report =
                                                    unit_branch_setup.id_report
                                WHERE  report_unit_setup.reports_id = reports.report_id) )");



    }
}
