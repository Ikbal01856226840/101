<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;
class StockCkeck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stock-ckeck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'current stock check day wise';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stock_in=DB::table('transaction_master')->select('transaction_master.tran_id')->Join('stock_in', 'transaction_master.tran_id', '=', 'stock_in.tran_id')->where('transaction_master.entry_date',date('Y-m-d'))->count();
            $stock_out=DB::table('transaction_master')->select('transaction_master.tran_id')->Join('stock_out', 'transaction_master.tran_id', '=', 'stock_out.tran_id')->where('transaction_master.entry_date',date('Y-m-d'))->count();
            $stock=DB::table('transaction_master')->select('transaction_master.tran_id')->Join('stock', 'transaction_master.tran_id', '=', 'stock.tran_id')->where('transaction_master.entry_date',date('Y-m-d'))->count();
            $total_stock_in_out_count=intval($stock_in+$stock_out);
        if($stock==$total_stock_in_out_count){
            return 0;
        }else{
        
            $stock_in_new=DB::table('transaction_master')->select('transaction_master.tran_id')->Join('stock_in', 'transaction_master.tran_id', '=', 'stock_in.tran_id')->where('transaction_master.entry_date',date('Y-m-d'))->count();
            $stock_out_new=DB::table('transaction_master')->select('transaction_master.tran_id')->Join('stock_out', 'transaction_master.tran_id', '=', 'stock_out.tran_id')->where('transaction_master.entry_date',date('Y-m-d'))->count();
            $stock_new=DB::table('transaction_master')->select('transaction_master.tran_id')->Join('stock', 'transaction_master.tran_id', '=', 'stock.tran_id')->where('transaction_master.entry_date',date('Y-m-d'))->count();
            $total_stock_in_out_count_new=($stock_in_new+$stock_out_new);
                if($stock_new==$total_stock_in_out_count_new){
                    return 0;
                }else{
                    $tran=DB::table('transaction_master')->select('tran_id')->where('entry_date',date('Y-m-d'))->get();
                    foreach($tran as $trans){
                        DB::table('stock')->where('tran_id',$trans->tran_id)->delete();
                        $stock_in=DB::table('stock_in')->where('tran_id',$trans->tran_id)->get();
                        $stock_out=DB::table('stock_out')->where('tran_id',$trans->tran_id)->get();
                        if(!empty(count($stock_in))){
                            foreach($stock_in as $stock){
                                $stock_id=DB::table('stock')->where('stock_item_id',$stock->stock_item_id)->orderBy('id', 'DESC')->first();
                                if(!empty($stock_id)){
                                if(($stock_id->current_qty==0)&&($stock->qty==0)){
                                    $rate=0;
                                }else{
                                    $rate_1=($stock_id->current_qty+$stock->qty);
                                    if($rate_1>0){
                                        $rate=((($stock_id->current_qty *$stock_id->current_rate) + $stock->total) /($stock_id->current_qty+$stock->qty));
                                    }else{
                                        $rate=0;
                                    }

                                }

                                $current_qty=($stock_id->current_qty+$stock->qty);
                                $stock_data = new Stock();
                                $stock_data->stock_item_id=$stock->stock_item_id;
                                $stock_data->godown_id =$stock->godown_id;
                                $stock_data->inwards_id=$stock->stock_in_id;
                                $stock_data->inwards_qty =$stock->qty ?? 0;
                                $stock_data->inwards_value =$stock->total ?? 0;
                                $stock_data->current_qty=$current_qty ?? 0;
                                $stock_data->current_rate=$rate ?? 0;
                                $stock_data->tran_id  =$stock->tran_id;
                                $stock_data->tran_date  =$stock->tran_date;
                                $stock_data->save();

                                }else{
                                    $stock_data = new Stock();
                                    $stock_data->stock_item_id=$stock->stock_item_id;
                                    $stock_data->godown_id =$stock->godown_id;
                                    $stock_data->inwards_id=$stock->stock_in_id;
                                    $stock_data->inwards_qty =$stock->qty ?? 0;
                                    $stock_data->inwards_value =$stock->total ?? 0;
                                    $stock_data->current_qty= $stock->qty ?? 0;
                                    $stock_data->current_rate=$stock->total==0?0:($stock->qty==0?0:($stock->total/$stock->qty) ?? 0);
                                    $stock_data->tran_id  =$stock->tran_id;
                                    $stock_data->tran_date  =$stock->tran_date;
                                    $stock_data->save();
                                }
                            }

                        }if(!empty(count($stock_out))){

                            foreach($stock_out as $stock_out_id){
                                $stock_out_show=DB::table('stock')->where('stock_item_id',$stock_out_id->stock_item_id)->orderBy('id', 'DESC')->first();
                                // dd($stock_out_show);
                                if(!empty($stock_out_show)){
                                $rate= $stock_out_show->current_rate;

                                $current_qty=($stock_out_show->current_qty-$stock_out_id->qty);
                                $stock_data_out = new Stock();
                                $stock_data_out->stock_item_id=$stock_out_id->stock_item_id;
                                $stock_data_out->godown_id =$stock_out_id->godown_id;
                                $stock_data_out->outwards_id=$stock_out_id->stock_out_id;
                                $stock_data_out->outwards_qty =$stock_out_id->qty ?? 0;
                                $stock_data_out->outwards_value =$stock_out_id->total ?? 0;
                                $stock_data_out->current_qty=$current_qty ?? 0;
                                $stock_data_out->current_rate=$rate ?? 0;
                                $stock_data_out->tran_id  =$stock_out_id->tran_id;
                                $stock_data_out->tran_date =$stock_out_id->tran_date;
                                $stock_data_out->save();

                                }else{
                                    $stock_data_out = new Stock();
                                $stock_data_out->stock_item_id=$stock_out_id->stock_item_id;
                                $stock_data_out->godown_id =$stock_out_id->godown_id;
                                $stock_data_out->outwards_id=$stock_out_id->stock_out_id;
                                $stock_data_out->outwards_qty = $stock_out_id->qty ?? 0;
                                $stock_data_out->outwards_value =$stock_out_id->total ?? 0;
                                $stock_data_out->current_qty=-$stock_out_id->qty ?? 0;
                                $stock_data_out->current_rate= 0;
                                $stock_data_out->tran_id  =$stock_out_id->tran_id;
                                $stock_data_out->tran_date  =$stock_out_id->tran_date;
                                $stock_data_out->save();
                                }
                            }
                        }
                    }
            }

        }
    }
}
