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
        return App\Models\UserPrivilegeInsertUpdate::where('user_id', $user_id)->where('status_type', $status_type)->first();
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
if (! function_exists('unit_branch_first')) {
    /**
     * return object .
     */
    function unit_branch_first()
    {
        return App\Models\Branch::first()->id_report;
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
if (! function_exists('page_wise_report_setting')) {
    /**
     * return object .
     */
    function page_wise_report_setting($user_id, $page_unique_id)
    {
        return App\Models\ReportPageWiseSetting::where('user_id', $user_id)->where('page_unique_id', $page_unique_id)->first();

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

    if (! function_exists('numberTowords')) {
        function numberToWords($number) {
            $hyphen      = '-';
            $conjunction = ' and ';
            $separator   = ', ';
            $negative    = 'negative ';
            $decimal     = ' point ';
            $dictionary  = [
                0                   => 'Zero',
                1                   => 'one',
                2                   => 'two',
                3                   => 'three',
                4                   => 'four',
                5                   => 'five',
                6                   => 'six',
                7                   => 'seven',
                8                   => 'eight',
                9                   => 'nine',
                10                  => 'ten',
                11                  => 'eleven',
                12                  => 'twelve',
                13                  => 'thirteen',
                14                  => 'fourteen',
                15                  => 'fifteen',
                16                  => 'sixteen',
                17                  => 'seventeen',
                18                  => 'eighteen',
                19                  => 'nineteen',
                20                  => 'twenty',
                30                  => 'thirty',
                40                  => 'forty',
                50                  => 'fifty',
                60                  => 'sixty',
                70                  => 'seventy',
                80                  => 'eighty',
                90                  => 'ninety',
                100                 => 'hundred',
                1000                => 'thousand',
                100000              => 'lakh',
                10000000            => 'crore'
            ];

            if (!is_numeric($number)) {
                return false;
            }

            if ($number < 0) {
                return $negative . numberToWords(abs($number));
            }

            $string = '';

            if ($number < 21) {
                $string = $dictionary[$number];
            } elseif ($number < 100) {
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
            } elseif ($number < 1000) {
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[(int) $hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . numberToWords($remainder);
                }
            } else {
                foreach ([10000000 => 'crore', 100000 => 'lakh', 1000 => 'thousand'] as $value => $word) {
                    if ($number >= $value) {
                        $baseUnits = $number / $value;
                        $remainder = $number % $value;
                        $string = numberToWords((int) $baseUnits) . ' ' . $word;
                        if ($remainder) {
                            $string .= $remainder < 100 ? $conjunction : $separator;
                            $string .= numberToWords($remainder);
                        }
                        break;
                    }
                }
            }

        return ucwords($string);
    }

    if (!function_exists('filterNullAndZero')) {
        function filterNullAndZero($value)
        {
            return $value !== null && (double)$value !== 0.00;
        }
    }

   function makeCurrency($num, $qty_comma = true) {
        if (!$qty_comma) {
            return $num;
        } else {
            $parts = explode('.', $num);
            // Add commas to the integer part of the number
            $parts[0] = preg_replace('/(\d)(?=(\d{2})+\d$)/', "$1,", $parts[0]);
            return implode('.', $parts);
        }
   }

   function validateTransaction($request) {
        // Convert the credit and debit values to numeric
        $credit = array_map('floatval', $request->credit);
        $debit = array_map('floatval', $request->debit);

        // Calculate the absolute difference and validate
        if (abs(array_sum($credit) - array_sum($debit)) <= 1) {
            return true; // Validation passed
        }

        return false; // Validation failed
    }

    if (! function_exists('company_name')) {
        /**
         * Get the label of the status enum by its value.
         *
         * @param  int  $value
         * @return string
         */
        function company_name(int $value): string
        {
            return \App\Enums\CompanyName::from($value)->label();
        }
    }

    if (! function_exists('voucher_modify_authorization')) {
        /**
         * Get the label of the status enum by its value.
         *
         * @param  int  $value
         * @return string
         */
        function voucher_modify_authorization(int $tran_id)
        {
        
            
                $user_privileges_insert_update=App\Models\UserPrivilegeInsertUpdate::where('user_id', Auth()->user()->id)->where('status_type', 'update')->first();
                $tran_master= App\Models\TransactionMaster::where('tran_id', $tran_id)->first();
            if(Auth()->user()->user_level==1){
                return true;
            }else if($user_privileges_insert_update->status_type=='update' && $user_privileges_insert_update->create_or_update==0 &&Auth()->user()->user_level!= 1)
            {
               if($tran_master->transaction_date< date('Y-m-d'))
                {
                    return false;
                }else{
                    return true;
                }
            }else if($user_privileges_insert_update->status_type=='update' && $user_privileges_insert_update->create_or_update==2 &&Auth()->user()->user_level!= 1) {
                // dd($tran_master->transaction_date,$user_privileges_insert_update->specific_date);
                if($tran_master->transaction_date>= $user_privileges_insert_update->specific_date){
                    return true;
                    
                }else{
                    return false; 
                }
               
            }else if($user_privileges_insert_update->status_type=='update' && $user_privileges_insert_update->create_or_update==3 &&Auth()->user()->user_level!= 1) {

                $voucherDate = Carbon\Carbon::parse($tran_master->transaction_date);
              
                $today = Carbon\Carbon::today();
                $cutoff = $today->copy()->subDays($user_privileges_insert_update->number);
        
                if($voucherDate->lt($cutoff))
                {
                    return false;
                }else{
                    return true;
                }
            }else{
                return true;
            }
            
        }
    }
}
