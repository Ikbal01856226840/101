<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Setting\PageWiseSettingRepository;
use Exception;
use Illuminate\Http\Request;

class PageWiseSettingController extends Controller
{
    private $pageWiseSetting;

    public function __construct(PageWiseSettingRepository $pageWiseSettingRepository)
    {
        $this->pageWiseSetting = $pageWiseSettingRepository;
    }

    public function pageWiseSetting(Request $request)
    {

        try {
            $data = $this->pageWiseSetting->pageWiseSetting($request);

            return RespondWithSuccess('Page Wise Setting successfully !!', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Page Wise Setting Not successfully !!', '', 404);
        }
    }
}
