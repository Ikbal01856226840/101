<?php

namespace App\Repositories\Backend\Setting;

use App\Models\PageWiseSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageWiseSettingRepository implements PageWiseSettingInterface
{
    public function pageWiseSetting(Request $request)
    {

        if (! empty($request->page_wise_id)) {
            $page_wise_setting = PageWiseSetting::findOrFail($request->page_wise_id);
            $page_wise_setting->created_by = $request->user_name ?? '';
            $page_wise_setting->history = $request->last_update ?? '';
            $page_wise_setting->alias = $request->alias ?? '';
            $page_wise_setting->sort_by = $request->sort_by ?? '';
            $page_wise_setting->sort_by_group = $request->sort_by_group;
            $page_wise_setting->bangla_name = $request->bangla_name;
            $page_wise_setting->last_insert_data_set = $request->last_insert_data_set ?? 0;
            $page_wise_setting->redirect_page = $request->redirect_page ?? 0;
            $page_wise_setting->save();

            return $page_wise_setting;
        } else {
            $page_wise_setting = new PageWiseSetting();
            $page_wise_setting->user_id = Auth::id();
            $page_wise_setting->page_title = $request->page_title ?? '';
            $page_wise_setting->page_unique_id = $request->page_unique_id ?? '';
            $page_wise_setting->created_by = $request->user_name ?? '';
            $page_wise_setting->history = $request->last_update ?? '';
            $page_wise_setting->alias = $request->alias ?? '';
            $page_wise_setting->sort_by = $request->sort_by;
            $page_wise_setting->sort_by_group = $request->sort_by_group;
            $page_wise_setting->bangla_name = $request->bangla_name;
            $page_wise_setting->last_insert_data_set = $request->last_insert_data_set ?? 0;
            $page_wise_setting->redirect_page = $request->redirect_page ?? 0;
            $page_wise_setting->save();

            return $page_wise_setting;
        }
    }
}
