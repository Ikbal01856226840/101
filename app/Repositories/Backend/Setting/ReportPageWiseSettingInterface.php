<?php

namespace App\Repositories\Backend\Setting;

use Illuminate\Http\Request;

interface ReportPageWiseSettingInterface
{
    /**
     * page wise setting
     */
    public function reportPageWiseSetting(Request $request);
}
