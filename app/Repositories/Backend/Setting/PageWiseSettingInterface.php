<?php

namespace App\Repositories\Backend\Setting;

use Illuminate\Http\Request;

interface PageWiseSettingInterface
{
    /**
     * page wise setting
     */
    public function pageWiseSetting(Request $request);
}
