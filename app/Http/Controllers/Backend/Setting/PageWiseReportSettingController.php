<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Setting\ReportPageWiseSettingRepository;
use Exception;
use Illuminate\Http\Request;

class PageWiseReportSettingController extends Controller
{
    private $reportPageWiseSetting;

    public function __construct(ReportPageWiseSettingRepository $reportPageWiseSettingRepository)
    {
        $this->reportPageWiseSetting= $reportPageWiseSettingRepository;
    }

    public function reportPageWiseSetting(Request $request)
    {

        try {
            $data = $this->reportPageWiseSetting->ReportPageWiseSetting($request);

            return RespondWithSuccess('Report Page Wise Setting successfully !!', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Report Page Wise Setting Not successfully !!', '', 404);
        }
    }
}
