<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class VoucherExchangeRepository implements VoucherExchangeInterface
{

    public function AccessVoucherSetup()
    {

        if(Auth()->user()->user_level==1){
            return DB::table('voucher_setup')
            ->select('voucher_setup.voucher_type_id', 'voucher_setup.voucher_name', 'voucher_setup.voucher_id', 'voucher_type.voucher_type')
            ->leftJoin('voucher_type', 'voucher_type.voucher_type_id', '=', 'voucher_setup.voucher_type_id')
            ->whereIn('voucher_setup.voucher_type_id',[1,8,14])
            ->orderBy('voucher_setup.voucher_type_id', 'ASC')->get();
        }else{
            // Fetch user access titles as an array
            $user_access = DB::table('user_privileges')
            ->where('table_user_id', Auth()->user()->id)
            ->where('status_type', 'Voucher')
            ->where('display_role', 1)
            ->pluck('title_details'); // Pluck the 'title_details' column as a collection
            // Convert collection to an array for `whereIn` usage
            $user_access_array=0;
            if($user_access->isNotEmpty()){
                $user_access_array = implode(',', array_map('intval', $user_access->toArray()));
            }

            // Fetch voucher setup data
            return DB::table('voucher_setup')
            ->select(
                'voucher_setup.voucher_type_id',
                'voucher_setup.voucher_name',
                'voucher_setup.voucher_id',
                'voucher_type.voucher_type',
                DB::raw('(SELECT COUNT(s.voucher_id)
                          FROM voucher_setup as s
                          WHERE s.voucher_type_id = voucher_setup.voucher_type_id) as total_count'), // Total count per type
                DB::raw('(SELECT COUNT(s.voucher_id)
                          FROM voucher_setup as s
                          WHERE s.voucher_type_id = voucher_setup.voucher_type_id
                          AND s.voucher_id IN (' . $user_access_array . ')) as filtered_count') // Count of matching vouchers
            )
            ->leftJoin('voucher_type', 'voucher_type.voucher_type_id', '=', 'voucher_setup.voucher_type_id')
            ->whereIn('voucher_setup.voucher_id', $user_access) // Use the array of title_details
            ->whereIn('voucher_setup.voucher_type_id',[1,8,14])
            ->orderBy('voucher_setup.voucher_type_id', 'ASC')
            ->get();


        }

    }

    public function getVouchers($request = null)
    {
        $voucher_sql = '';
        $params = [];
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if ($request->voucher_id == 0) {
                $voucher_sql =' AND (voucher_setup.voucher_type_id=:voucher_type_id_contra
                                OR voucher_setup.voucher_type_id=:voucher_type_id_payment
                                OR voucher_setup.voucher_type_id=:voucher_type_id_receipt) ';
                $params['voucher_type_id_contra'] = 1;
                $params['voucher_type_id_payment'] = 8;
                $params['voucher_type_id_receipt'] = 14;
            } else {
                if (strpos($request->voucher_id, 'v') !== false) {
                    $voucher_type_id = str_replace('v', '', $request->voucher_id);
                    $voucher_sql = "AND voucher_setup.voucher_type_id=:voucher_type_id";
                    $params['voucher_type_id'] = $voucher_type_id;
                } else {
                    $voucher_sql = "AND transaction_master.voucher_id=:voucher_id";
                    $params['voucher_id'] = $request->voucher_id;
                }
            }
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
            $voucher_sql =' AND (voucher_setup.voucher_type_id=:voucher_type_id_contra
                                OR voucher_setup.voucher_type_id=:voucher_type_id_payment
                                OR voucher_setup.voucher_type_id=:voucher_type_id_receipt) ';
            $params['voucher_type_id_contra'] = 1;
            $params['voucher_type_id_payment'] = 8;
            $params['voucher_type_id_receipt'] = 14;
        }
        if (user_privileges_check('report', 'Daybook', 'display_role')) {
            $user_id = "";
        } else {

            $user_id  = "AND transaction_master.user_id=:user_id";
            $params['user_id'] =Auth()->user()->id;
        }

                $query="SELECT         transaction_master.tran_id,
                                        transaction_master.invoice_no,
                                        transaction_master.transaction_date,
                                        transaction_master.voucher_id,
                                        transaction_master.customer_id,
                                        transaction_master.narration,
                                        transaction_master.user_name,
                                        voucher_setup.voucher_type_id,
                                        debit_credit.ledger_head_id,
                                        ledger_head.ledger_name,
                                        voucher_setup.voucher_name

                        FROM            (transaction_master
                        INNER JOIN      voucher_setup
                        ON              voucher_setup.voucher_id=transaction_master.voucher_id )
                        LEFT OUTER JOIN (debit_credit
                        INNER JOIN      ledger_head
                        ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id )
                        ON              (
                                                        debit_credit.tran_id=transaction_master.tran_id)
                        WHERE           voucher_setup.voucher_type_id NOT IN(20,30) AND transaction_master.transaction_date BETWEEN :from_date AND             :to_date  $user_id $voucher_sql
                        GROUP BY          transaction_master.tran_id
                        ORDER BY    transaction_master.transaction_date ASC,transaction_master.tran_id ASC

                        ";
                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;
        return DB::select($query,$params);



    }

    public function getSearchVouchers($request = null)
    {
        $voucher_id=0;
        $narration='';
        $invoice_no='';
        $ledger_head_id='';
        $voucher_sql = '';
        $params = [];
        if (isset($request)) {
            if(!empty($request->SearchRangeOne)){
                if($request->SearchTypeOne==1){
                    $ledger_head_id=$request->SearchRangeOne;
                }else if($request->SearchTypeOne==2){
                    $voucher_id=$request->SearchRangeOne;
                }else if($request->SearchTypeOne==3){
                    $invoice_no=$request->SearchRangeOne;
                }else if($request->SearchTypeOne==4){
                    $narration=$request->SearchRangeOne;
                }
            }
            if(!empty($request->SearchRangeTwo)){
                if($request->SearchTypeTwo==1){
                    $ledger_head_id=$request->SearchRangeTwo;
                }else if($request->SearchTypeTwo==2){
                    $voucher_id=$request->SearchRangeTwo;
                }else if($request->SearchTypeTwo==3){
                    $invoice_no=$request->SearchRangeTwo;
                }else if($request->SearchTypeTwo==4){
                    $narration=$request->SearchRangeTwo;
                }
            }
            if(!empty($request->SearchRangeThree)){
                if($request->SearchTypeThree==1){
                    $ledger_head_id=$request->SearchRangeThree;
                }else if($request->SearchTypeThree==2){
                    $voucher_id=$request->SearchRangeThree;
                }else if($request->SearchTypeThree==3){
                    $invoice_no=$request->SearchRangeThree;
                }else if($request->SearchTypeThree==4){
                    $narration=$request->SearchRangeThree;
                }
            }
            if(!empty($request->SearchRangeFour)){
                if($request->SearchTypeFour==1){
                    $ledger_head_id=$request->SearchRangeFour;
                }else if($request->SearchTypeFour==2){
                    $voucher_id=$request->SearchRangeFour;
                }else if($request->SearchTypeFour==3){
                    $invoice_no=$request->SearchRangeFour;
                }else if($request->SearchTypeFour==4){
                    $narration=$request->SearchRangeFour;
                }
            }
            if(!empty($voucher_id) || !empty($ledger_head_id) || !empty($invoice_no) || !empty($narration)){
                if (empty($voucher_id)) {
                    $voucher_sql = '';
                }else{
                    if (strpos($voucher_id, 'v') !== false) {
                        $voucher_type_id = str_replace('v', '', $voucher_id);
                        $voucher_sql = "AND voucher_setup.voucher_type_id=:voucher_type_id";
                        $params['voucher_type_id'] = $voucher_type_id;
                    } else {
                        $voucher_sql = "AND transaction_master.voucher_id=:voucher_id";
                        $params['voucher_id'] = $voucher_id;
                    }
                }

                if (user_privileges_check('report', 'Daybook', 'display_role')) {
                    $user_id = "";
                } else {

                    $user_id  = "AND transaction_master.user_id=:user_id";
                    $params['user_id'] =Auth()->user()->id;
                }
                $narration_sql='';
                if(!empty($narration)){
                    $narration_sql="AND transaction_master.narration=:narration";
                    $params['narration']=$narration;
                }
                $invoice_no_sql='';
                if(!empty($invoice_no)){
                    $invoice_no_sql="AND transaction_master.invoice_no=:invoice_no";
                    $params['invoice_no']=$invoice_no;
                }
                $ledger_head_id_sql='';
                if(!empty($ledger_head_id)){
                    $ledger_head_id_sql="AND debit_credit.ledger_head_id=:ledger_head_id";
                    $params['ledger_head_id']=$ledger_head_id;
                }
                $query="SELECT         transaction_master.tran_id,
                            transaction_master.invoice_no,
                            transaction_master.transaction_date,
                            transaction_master.voucher_id,
                            transaction_master.customer_id,
                            transaction_master.narration,
                            transaction_master.user_name,
                            voucher_setup.voucher_type_id,
                            debit_credit.ledger_head_id,
                            ledger_head.ledger_name,
                            voucher_setup.voucher_name

                FROM            (transaction_master
                INNER JOIN      voucher_setup
                ON              voucher_setup.voucher_id=transaction_master.voucher_id )
                LEFT OUTER JOIN (debit_credit
                INNER JOIN      ledger_head
                ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id )
                ON              (
                                            debit_credit.tran_id=transaction_master.tran_id)
                WHERE           voucher_setup.voucher_type_id NOT IN(20,30)
                                $user_id
                                $voucher_sql
                                $narration_sql
                                $invoice_no_sql
                                $ledger_head_id_sql
                GROUP BY          transaction_master.tran_id
                ORDER BY    transaction_master.transaction_date ASC,transaction_master.tran_id ASC
                ";
                return DB::select($query,$params);
            }


        }
        return [];
    }

}

