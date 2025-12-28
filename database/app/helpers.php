<?php

if (! function_exists('RespondWithSuccess')) {
    /**
     * return response json success  .
     */
    function RespondWithSuccess($massage, $data, $code)
    {
        return response()->json([
            'success' => true,
            'message' => $massage,
            'data' => $data,
        ], $code);
    }
}
if (! function_exists('RespondWithError')) {
    /**
     * return response json error .
     */
    function RespondWithError($massage, $data, $code)
    {
        return response()->json([
            'error' => true,
            'message' => $massage,
            'data' => $data,
        ], $code);
    }
}
if (! function_exists('company')) {
    /**
     * return object .
     */
    function company()
    {
        return App\Models\Company::first();
    }
}
if (! function_exists('user_privileges_role')) {
    /**
     * return object .
     */
    function user_privileges_role($user_id, $status_type, $title_details)
    {

        return App\Models\UserPrivilege::where('table_user_id', $user_id)->where('status_type', $status_type)->where('title_details', $title_details)->first();
    }
}
if (! function_exists('user_privileges_role_voucher')) {
    /**
     * return object .
     */
    function user_privileges_role_voucher($user_id, $status_type)
    {
        $data = App\Models\UserPrivilege::where('table_user_id', $user_id)->where('status_type', $status_type)->get();

        return json_decode(json_encode($data, true), true);
    }
}
if (! function_exists('user_privileges_insert_update')) {
    /**
     * return object .
     */
    function user_privileges_insert_update($user_id, $status_type, $create_or_update)
    {
        return App\Models\UserPrivilegeInsertUpdate::where('user_id', $user_id)->where('status_type', $status_type)->where('create_or_update', $create_or_update)->first();
    }
}
if (! function_exists('unit_branch')) {
    /**
     * return object .
     */
    function unit_branch()
    {
        return App\Models\Branch::all(['id', 'branch_name']);
    }
}

if (! function_exists('user_privileges_check')) {
    /**
     * return object .
     */
    function user_privileges_check($status_type, $title_details, $privileges_type)
    {
        // $cacheKey = "user_query_results_foo_{$status_type}_bar_{$title_details}_bar_{$privileges_type}";
        // if (Illuminate\Support\Facades\Cache::has($cacheKey)) {

        //     $data = Illuminate\Support\Facades\Cache::get($cacheKey);
            
        //     if (($data ? $data[$privileges_type] == 1 : '') || (Auth()->user()->user_level == 1)) {
        //         return true;
        //     } else {
        //         return false;
        //     }
        // } else {

            $data = App\Models\UserPrivilege::where('table_user_id', Auth()->user()->id)->where('status_type', $status_type)->where('title_details', $title_details)->first(['privileges_id', $privileges_type]);

            // Illuminate\Support\Facades\Cache::forever($cacheKey, $data);

            if (($data ? $data[$privileges_type] == 1 : '') || (Auth()->user()->user_level == 1)) {
                return true;
            } else {
                return false;
            }
        //}

    }
}
if (! function_exists('page_wise_setting')) {
    /**
     * return object .
     */
    function page_wise_setting($user_id, $page_unique_id)
    {
        return App\Models\PageWiseSetting::where('user_id', $user_id)->where('page_unique_id', $page_unique_id)->first();

    }
}

if (! function_exists('financial_start_date')) {
    /**
     * return object .
     */
    function financial_start_date($date)
    {
        if((company()->financial_year_start<=$date)&&(company()->financial_year_end>=$date)){
            return $date;
        }else{
            return company()->financial_year_start;
        }

    }
}
if (! function_exists('financial_end_date')) {
    /**
     * return object .
     */
    function financial_end_date($date)
    {
        if((company()->financial_year_start<=$date)&&(company()->financial_year_end>=$date)){
            return $date;
        }else{
            return company()->financial_year_end;
        }

    }
}
