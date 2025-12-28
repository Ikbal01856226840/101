<?php

namespace App\Repositories\Backend;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyRepository implements CompanyInterface
{
    public function getCompanyOfIndex()
    {
        return Company::first();
    }

    public function updateCompany(Request $request, $id)
    {
        $tran_data = DB::table('transaction_master')->join('voucher_setup','transaction_master.voucher_id','=','voucher_setup.voucher_id')->where('voucher_type_id', '=', 32)->first();
        $company= Company::where('company_id',$id)->first();

        if(($request->financial_year_start)!=($company->financial_year_start)){
            if (!empty($tran_data)) {
                DB::table('transaction_master')->join('voucher_setup','transaction_master.voucher_id','=','voucher_setup.voucher_id')->where('voucher_type_id', '=', 32)->update(['transaction_date' => date('Y-m-d', strtotime('-1 day', strtotime($request->financial_year_start)))]);
                DB::table('stock_in')->where('stock_in.type_in', '=','OP')->update(['stock_in.tran_date' => date('Y-m-d', strtotime('-1 day', strtotime($request->financial_year_start)))]);
            }
        }
        

        return Company::where('company_id', $id)->update([
            'company_name' => $request->company_name,
            'branch_id' => $request->branch_id,
            'customer_id' => $request->customer_id,
            'quantity_decimals' => $request->quantity_decimals,
            'amount_decimals' => $request->amount_decimals,
            'amount_in_word' => $request->amount_in_word,
            'mailing_address' => $request->mailing_address,
            'category' => $request->category,
            'financial_year_start' => $request->financial_year_start,
            'financial_year_end' => $request->financial_year_end,
            'opening_stock_item_customer_is' => $request->opening_stock_item_customer_is,
        ]);
    }
}
