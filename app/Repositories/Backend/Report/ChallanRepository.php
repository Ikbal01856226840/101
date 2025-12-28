<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\AuthRepository;

class ChallanRepository implements ChallanInterface
{    
    private $authRepository;
     public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
        
    }
    public function getChallanfIndex($request)
    {
        $params = [];
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $voucher_sql = '';
        if ($request->voucher_id == 0) {
            if(Auth()->user()->user_level==1){
                $voucher_sql = '';
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
                    $voucher_sql="AND transaction_master.voucher_id IN($user_access_array)";
                }else{
                    $voucher_sql="AND transaction_master.voucher_id IN($user_access_array)"; 
                }
            }
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
        if( $request->delivery_status){
            $delivery_status = $request->delivery_status ? "delivery_status=:delivery_status AND" : 'delivery_status IN(1,2) AND';
            $params['delivery_status'] = $request->delivery_status;
        }else{
            $delivery_status = $request->delivery_status ? "delivery_status=:delivery_status AND" : 'delivery_status IN(1,2) AND';
        }
      
      
        
        if($request->dis_cen_id==0){
             if(Auth()->user()->user_level==1){
              $dis_cen_id = "";
             }else{
                $dis_cen_id = $this->authRepository->findUserGet(Auth()->user()->id);
                if (array_sum(array_map('intval', explode(' ', $dis_cen_id->dis_cen_id))) != 0){
                    $ids = explode(',', $dis_cen_id->dis_cen_id);
                      $dis_cen_id = "AND transaction_master.dis_cen_id IN($ids)";
                }else{
                    $dis_cen_id = ""; 
                }
             }
        }else{
          $dis_cen_id = "AND transaction_master.dis_cen_id=:dis_cen_id";
          $params['dis_cen_id'] =$request->dis_cen_id;
        }
       

             $query="SELECT         transaction_master.tran_id,
                                    transaction_master.invoice_no,
                                    transaction_master.transaction_date,
                                    transaction_master.voucher_id,
                                    transaction_master.other_details,
                                    transaction_master.delivery_status,
                                    voucher_setup.voucher_type_id,
                                    ledger_head.ledger_name,
                                    voucher_setup.voucher_name,
                                    order_approver.other_details AS order_approver
                    FROM            (transaction_master
                    INNER JOIN      voucher_setup
                    ON              voucher_setup.voucher_id=transaction_master.voucher_id )
                    LEFT OUTER JOIN (debit_credit
                    INNER JOIN      ledger_head
                    ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id )
                    ON              (
                                                    debit_credit.tran_id=transaction_master.tran_id)
                    LEFT JOIN      stock_out
                    ON              transaction_master.tran_id=stock_out.tran_id
                    LEFT JOIN      godowns
                    ON              stock_out.godown_id=godowns.godown_id
                    LEFT JOIN      order_approver
                    ON              order_approver.tran_id=transaction_master.tran_id
                    WHERE           $delivery_status  
                                    transaction_master.transaction_date BETWEEN :from_date AND             :to_date $dis_cen_id $voucher_sql
                    AND             order_approver.order_approve_status=transaction_master.delivery_status
                    GROUP BY        transaction_master.tran_id 
                    ORDER BY    transaction_master.transaction_date ASC,transaction_master.tran_id ASC";
                  
                  $params['from_date'] = $from_date;
                  $params['to_date'] = $to_date;

        return DB::select($query,$params);
                   
                  
        
    }
}
