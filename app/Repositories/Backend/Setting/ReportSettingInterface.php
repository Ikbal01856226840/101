<?php

namespace App\Repositories\Backend;

use Illuminate\Http\Request;

interface ReportSettingInterface
{
    public function getReportOfIndex();

    public function StoreReport(Request $request);

    public function getReportId($id);

    public function updateReport(Request $request, $id);

    public function deleteReport($id);
}
