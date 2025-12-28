<?php

namespace App\Repositories\Backend\Setting;

use App\Models\ReportPageWiseSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportPageWiseSettingRepository implements ReportPageWiseSettingInterface
{
    public function reportPageWiseSetting(Request $request)
    {
      
      $setting_data=ReportPageWiseSetting::where('page_unique_id',$request->page_unique_id)->where('user_id',Auth::id())->first();
     
        if (!empty($setting_data)) {
           
            $page_wise_setting = ReportPageWiseSetting::findOrFail($setting_data->id);
            $page_wise_setting->page_title = $request->page_title ?? 0;
            $page_wise_setting->page_unique_id = $request->page_unique_id ?? 0;
            $page_wise_setting->qty_dcecimal = $request->show_quantity_decimal==1?$request->quantity_decimals:0 ?? 0;
            $page_wise_setting->qty_comma = $request->show_quantity_comma ?? 0;
            $page_wise_setting->amount_decimal =$request->show_amount_decimal==1?$request->amount_decimals:0 ?? 0;
            $page_wise_setting->rate_decimal = $request->show_rate_decimal==1?$request->rate_decimals:0 ?? 0;;
            $page_wise_setting->company_name = $request->show_company_name==1?$request->company_name:0 ??0;
            $page_wise_setting->company_mailingaddress=$request->show_company_mailing_address==1?$request->company_mailing_address:0??0;
            $page_wise_setting->print_date = $request->print_date ?? 0;
            $page_wise_setting->report_name= $request->show_report_name==1?$request->report_name:0 ?? 0;
            $page_wise_setting->report_details = $request->show_report_details==1?$request->show_report_details:0 ?? 0;
            $page_wise_setting->show_date = $request->show_report_details==1?$request->show_date:0 ?? 0;
            $page_wise_setting->show_godown = $request->show_report_details==1?$request->show_godown:0 ?? 0;
            $page_wise_setting->show_ledger= $request->show_report_details==1?$request->show_ledger:0 ?? 0;
            $page_wise_setting->show_item = $request->show_report_details==1?$request->show_item:0 ?? 0;
            $page_wise_setting->show_group_chart = $request->show_report_details==1?$request->show_group_chart:0 ?? 0;
            $page_wise_setting->show_stock_group = $request->show_report_details==1?$request->show_stock_group:0 ?? 0;
            $page_wise_setting->fontsizetop = $request->font_size_top ?? 0;
            $page_wise_setting->fontsizebody= $request->font_size_body ?? 0;
            $page_wise_setting->units_of_measure= $request->show_units_of_measure ?? 0;
            $page_wise_setting->show_footer = $request->show_footer ?? 0;
            $page_wise_setting->left_footer=$request->show_footer==1? $request->left_footer:'' ?? 0;
            $page_wise_setting->middle_footer=$request->show_footer==1? $request->middle_footer:'' ?? 0;
            $page_wise_setting->right_footer= $request->show_footer==1?$request->right_footer:'' ?? 0;
            $page_wise_setting->accounts_decimals = $request->show_account_decimal==1?$request->accounts_decimals:0 ?? 0;
            $page_wise_setting->show_debit_is =$request->show_debit_is?? 0;
            $page_wise_setting->show_credit_is =$request->show_credit_is?? 0;
            $page_wise_setting->show_closing_is=$request->show_closing_is?? 0;
            $page_wise_setting->save();

            return $page_wise_setting;
        } else {
            $page_wise_setting = new ReportPageWiseSetting();
            $page_wise_setting->user_id = Auth::id();
            $page_wise_setting->page_title = $request->page_title ?? 0;
            $page_wise_setting->page_unique_id = $request->page_unique_id ?? 0;
            $page_wise_setting->qty_dcecimal = $request->show_quantity_decimal==1?$request->quantity_decimals:0 ?? 0;
            $page_wise_setting->qty_comma = $request->show_quantity_comma ?? 0;
            $page_wise_setting->amount_decimal =$request->show_amount_decimal==1?$request->amount_decimals:0 ?? 0;
            $page_wise_setting->rate_decimal = $request->show_rate_decimal==1?$request->rate_decimals:0 ?? 0;;
            $page_wise_setting->company_name = $request->show_company_name==1?$request->company_name:0 ??0;
            $page_wise_setting->company_mailingaddress=$request->show_company_mailing_address==1?$request->company_mailing_address:0??0;
            $page_wise_setting->print_date = $request->print_date ?? 0;
            $page_wise_setting->report_name= $request->show_report_name==1?$request->report_name:0 ?? 0;
            $page_wise_setting->report_details = $request->show_report_details==1?$request->show_report_details:0 ?? 0;
            $page_wise_setting->show_date = $request->show_report_details==1?$request->show_date:0 ?? 0;
            $page_wise_setting->show_godown = $request->show_report_details==1?$request->show_godown:0 ?? 0;
            $page_wise_setting->show_ledger= $request->show_report_details==1?$request->show_ledger:0 ?? 0;
            $page_wise_setting->show_item = $request->show_report_details==1?$request->show_item:0 ?? 0;
            $page_wise_setting->show_group_chart = $request->show_report_details==1?$request->show_group_chart:0 ?? 0;
            $page_wise_setting->show_stock_group = $request->show_report_details==1?$request->show_stock_group:0 ?? 0;
            $page_wise_setting->fontsizetop = $request->font_size_top ?? 0;
            $page_wise_setting->fontsizebody= $request->font_size_body ?? 0;
            $page_wise_setting->units_of_measure= $request->show_units_of_measure ?? 0;
            $page_wise_setting->show_footer = $request->show_footer ?? 0;
            $page_wise_setting->left_footer=$request->show_footer==1? $request->left_footer:'' ?? 0;
            $page_wise_setting->middle_footer=$request->show_footer==1? $request->middle_footer:'' ?? 0;
            $page_wise_setting->right_footer= $request->show_footer==1?$request->right_footer:'' ?? 0;
            $page_wise_setting->accounts_decimals = $request->show_account_decimal==1?$request->accounts_decimals:0 ?? 0;
            $page_wise_setting->show_debit_is =$request->show_debit_is?? 0;
            $page_wise_setting->show_credit_is =$request->show_credit_is?? 0;
            $page_wise_setting->show_closing_is=$request->show_closing_is?? 0;
            $page_wise_setting->save();

            return $page_wise_setting;
        }
    }
}
