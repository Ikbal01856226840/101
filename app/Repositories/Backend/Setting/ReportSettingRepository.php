<?php

namespace App\Repositories\Backend;

use App\Models\ReportPageWiseSetting;
use Illuminate\Support\Facades\Auth;

class ReportSetting implements ReportSettingInterface
{
    public function getReportOfIndex()
    {
        return ReportPageWiseSetting::all(['id', 'parent_group', 'report_name', 'report_title','page_name','status','company_id']);
    }

    public function StoreReport($request)
    {
        $data = new ReportPageWiseSetting();
        $data->parent_group= $request->parent_group;
        $data->report_name = $request->report_name;
        $data->report_title = $request->report_title;
        $data->page_name = $request->page_name;
        $data->company_id= company()->company_id;
        $data->save();

        return $data;
    }

    public function getReportId($id)
    {
        return ReportPageWiseSetting::findOrFail($id);
    }

    public function updateReport($request, $id)
    {
        $data = ReportPageWiseSetting::findOrFail($id);
        $data->parent_group= $request->parent_group;
        $data->report_name = $request->report_name;
        $data->report_title = $request->report_title;
        $data->page_name = $request->page_name;
        $data->company_id= company()->company_id;
        $data->save();

        return $data;
    }

    public function deleteReport($id)
    {
        return ReportPageWiseSetting::findOrFail($id)->delete();
    }
}
