<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class VoucherRegisterRepository implements VoucherRegisterInterface
{
    public function getVoucherRegisterOfIndex($request = null)
    {
        $voucher_sql = '';
        $params = [];
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
             $user_access_authorized_sql='';
            if ($request->voucher_id == 0) {
                    if(Auth()->user()->user_level==1){
                        $voucher_sql = '';
                        $user_access_authorized_sql='';
                    }else{
                        // Fetch user access titles as an array
                        $user_access = DB::table('user_privileges')
                            ->where('table_user_id', Auth()->user()->id)
                            ->where('status_type', 'Voucher')
                            ->where('display_role', 1)
                            ->pluck('title_details')
                            ->toArray(); // Convert to array

                        $user_authorized_access = DB::table('user_privileges')
                            ->where('table_user_id', Auth()->user()->id)
                            ->where('status_type', 'Voucher')
                            ->where('authorized_dsply', 1)
                            ->pluck('title_details')
                            ->toArray(); // Convert to array

                        $newarray = array_diff($user_access, $user_authorized_access);
            
                        $user_access_array = 0;
                        $user_authorized_access_array = 0;
                        if (!empty($newarray)) {
                        
                            $user_access_array = implode(',', array_map('intval', $newarray));
                            $voucher_sql = "AND transaction_master.voucher_id IN($user_access_array)";
                        }
                        if(!empty($user_authorized_access)){
                            $user_authorized_access_array = implode(',', array_map('intval',$user_authorized_access));
                        if(empty($newarray)){
                            $voucher_sql = "AND transaction_master.voucher_id IN($user_authorized_access_array) AND transaction_master.user_id=".Auth()->user()->id."";
                        }else{
                            $user_access_authorized_sql = "OR(transaction_master.transaction_date BETWEEN :from_date_authorized AND   :to_date_authorized AND transaction_master.user_id=".Auth()->user()->id." AND transaction_master.voucher_id IN($user_authorized_access_array))";
                            $params['from_date_authorized'] = $from_date;
                            $params['to_date_authorized'] = $to_date;
                        }
                    
                   
                    }
                }
            } else {
                if (strpos($request->voucher_id, 'v') !== false) {
                    $voucher_type_id = str_replace('v', '', $request->voucher_id);
                    $get_voucher_ids = DB::table('voucher_setup')
                                        ->where('voucher_type_id', $voucher_type_id)
                                        ->pluck('voucher_id')
                                        ->toArray();
                    
                       $user_access = DB::table('user_privileges')
                                    ->where('table_user_id', Auth()->user()->id)
                                    ->where('status_type', 'Voucher')
                                    ->where('display_role', 1)
                                    ->whereIn('title_details', $get_voucher_ids)
                                    ->pluck('title_details')
                                    ->toArray(); 

                        $user_authorized_access = DB::table('user_privileges')
                            ->where('table_user_id', Auth()->user()->id)
                            ->where('status_type', 'Voucher')
                            ->where('authorized_dsply', 1)
                            ->whereIn('title_details', $get_voucher_ids)
                            ->pluck('title_details')
                            ->toArray(); 

                        $newarray = array_diff($user_access, $user_authorized_access);
            
                        $user_access_array = 0;
                        $user_authorized_access_array = 0;
                        if (!empty($newarray)) {
                        
                            $user_access_array = implode(',', array_map('intval', $newarray));
                            $voucher_sql = "AND transaction_master.voucher_id IN($user_access_array)";
                        }
                        if(!empty($user_authorized_access)){
                            $user_authorized_access_array = implode(',', array_map('intval',$user_authorized_access));
                        if(empty($newarray)){
                            $voucher_sql = "AND transaction_master.voucher_id IN($user_authorized_access_array) AND transaction_master.user_id=".Auth()->user()->id."";
                        }else{
                            $user_access_authorized_sql = "OR(transaction_master.transaction_date BETWEEN :from_date_authorized AND   :to_date_authorized AND transaction_master.user_id=".Auth()->user()->id." AND transaction_master.voucher_id IN($user_authorized_access_array))";
                            $params['from_date_authorized'] = $from_date;
                            $params['to_date_authorized'] = $to_date;
                        }
                    }
                } else {
                    
                    $user_authorized_access = DB::table('user_privileges')
                            ->where('table_user_id', Auth()->user()->id)
                            ->where('status_type', 'Voucher')
                            ->where('authorized_dsply', 1)
                            ->where('authorized_dsply', 1)
                            ->where('title_details', $request->voucher_id)
                            ->first(['title_details']);
                     if($user_authorized_access){
                         $voucher_sql = "AND transaction_master.voucher_id=:voucher_id AND transaction_master.user_id=".Auth()->user()->id." ";
                         $params['voucher_id'] = $request->voucher_id;
                     }else{
                        $voucher_sql = "AND transaction_master.voucher_id=:voucher_id";
                        $params['voucher_id'] = $request->voucher_id;
                     }       
                  
                }
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $voucher_sql = "AND transaction_master.voucher_id=0";
        }
        if ($request->sort_by == 1) {
            $sort_by = " transaction_master.transaction_date ASC,transaction_master.tran_id ASC";

        } else {
            $sort_by = ($request->sort_by == 2 ? 'debit ASC' : ($request->sort_by == 3 ? 'credit ASC' : ($request->sort_by == 4 ? 'invoice_no ASC' : ($request->sort_by == 5 ? 'ledger_name ASC' : ''))));
        }
        $query="SELECT
                            transaction_master.tran_id,
                            transaction_master.invoice_no,
                            transaction_master.transaction_date,
                            transaction_master.voucher_id,
                            transaction_master.narration,
                            voucher_setup.voucher_type_id,
                            debit_credit.ledger_head_id,
                            ledger_head.ledger_name,
                            voucher_setup.voucher_name,
                            transaction_master.narration,
                            debit_credit.dr_cr,
                            debit_credit.debit,
                            debit_credit.credit,
                            CASE 
                                WHEN voucher_type_id = 22 OR voucher_type_id = 21 THEN 
                                    (SELECT SUM(st_in.qty)
                                    FROM stock_in AS st_in
                                    WHERE st_in.tran_id = transaction_master.tran_id)
                                ELSE NULL
                            END AS stock_in_sum,

                            CASE 
                                WHEN voucher_type_id = 22 OR voucher_type_id = 21 THEN 
                                    (SELECT SUM(st_out.qty)
                                    FROM stock_out AS st_out
                                    WHERE st_out.tran_id = transaction_master.tran_id)
                                ELSE NULL
                            END AS stock_out_sum
                            
                        FROM (transaction_master
                                INNER JOIN voucher_setup
                                ON voucher_setup.voucher_id=transaction_master.voucher_id
                            )
                        LEFT OUTER JOIN
                        (debit_credit INNER JOIN ledger_head
                            ON ledger_head.ledger_head_id=debit_credit.ledger_head_id
                        )
                        ON (debit_credit.tran_id=transaction_master.tran_id)
                        WHERE   voucher_setup.voucher_type_id NOT IN(20,30) AND transaction_master.transaction_date BETWEEN :from_date AND :to_date  $voucher_sql   $user_access_authorized_sql
                        Group by transaction_master.tran_id
                        ORDER BY $sort_by
                    ";
                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;
        return DB::select($query,$params);
    }
}
