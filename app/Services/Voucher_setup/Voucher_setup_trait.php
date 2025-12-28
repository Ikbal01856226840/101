<?php

namespace App\Services\Voucher_setup;
use Illuminate\Support\Facades\DB;

trait Voucher_setup_trait
{

    public function backwardForwardInvoiceNoAndRefNo ($request){
        $vouchernumbermethod =DB::table('voucher_setup')->where('voucher_id',$request->voucher_id)->first()->vouchernumbermethod;

            if($request->tran_id){
                if($request->type=='invoice_no'){
                    if($vouchernumbermethod==4){
                        if($request->backward_forward=='backward'){
                                return DB::table('transaction_master')
                                ->where('voucher_id',$request->voucher_id)
                                ->where('tran_id','<',$request->tran_id)
                                ->orderBy('tran_id', 'DESC')
                                ->first(['invoice_no','tran_id']);

                        }else{
                            return DB::table('transaction_master')
                                ->where('voucher_id',$request->voucher_id)
                                ->where('tran_id','>',$request->tran_id)
                                ->orderBy('tran_id', 'ASC')
                                ->first(['invoice_no','tran_id']);
                        }
                   }
                }else if($request->type=='ref_no'){
                    if($request->backward_forward=='backward'){
                        return DB::table('transaction_master')
                            ->where('voucher_id',$request->voucher_id)
                            ->where('tran_id','<',$request->tran_id)
                            ->orderBy('tran_id', 'DESC')
                            ->first(['ref_no','tran_id']);
                    }else{
                        return DB::table('transaction_master')
                        ->where('voucher_id',$request->voucher_id)
                        ->where('tran_id','>',$request->tran_id)
                        ->orderBy('tran_id', 'ASC')
                        ->first(['ref_no','tran_id']);
                    }
                }else if($request->type=='narration'){
                    if($request->backward_forward=='backward'){
                        return DB::table('transaction_master')
                            ->where('voucher_id',$request->voucher_id)
                            ->where('tran_id','<',$request->tran_id)
                            ->orderBy('tran_id', 'DESC')
                            ->first(['narration','tran_id']);
                    }else{
                        return DB::table('transaction_master')
                        ->where('voucher_id',$request->voucher_id)
                        ->where('tran_id','>',$request->tran_id)
                        ->orderBy('tran_id', 'ASC')
                        ->first(['narration','tran_id']);
                    }
                }else{
                    return null;
                }
           }else{
                if($request->backward_forward=='backward'){
                    if($request->type=='invoice_no'){
                        if($vouchernumbermethod==4){
                            return DB::table('transaction_master')->where('voucher_id',$request->voucher_id)
                            ->orderBy('tran_id', 'DESC')->first(['invoice_no','tran_id']);
                        }
                    }else if($request->type=='ref_no'){
                        return DB::table('transaction_master')->where('voucher_id',$request->voucher_id)
                        ->orderBy('tran_id', 'DESC')->first(['ref_no','tran_id']);
                    }else if($request->type=='narration'){
                        return DB::table('transaction_master')->where('voucher_id',$request->voucher_id)
                        ->orderBy('tran_id', 'DESC')->first(['narration','tran_id']);
                    }
                }else{
                    return null;
                }

           }
    }
}
