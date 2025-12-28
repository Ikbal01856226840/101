<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class RateCalculation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $updatekey;
    public $update_data;
    public $type;
    public function __construct($request, $update_data=null, $type=null)
    {
        $this->updatekey = $request;
        $this->update_data = $update_data;  // Fixed the syntax here
        $this->type = $type;  // Fixed the syntax here
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $updatekey = $this->updatekey;
        $update_data = $this->update_data;
        $type = $this->type;
        // Log the initial state
        // Log::info('Job Handle Started', [
        //     'type' => $type,
        //     'updatekey' => $updatekey,
        //     'update_data' => $update_data
        // ]);
        // Dispatch the job with the array
        if($type=='UpdateOut'){
            $this->stockOutCalculation($update_data,$updatekey);
        }else if($type=='UpdateIn'){
            $this->stockInCalculation($update_data,$updatekey);
        }else if($type=='Delete'){
            foreach($updatekey as $value){
                $this->stockReCalculation($value->stock_item_id,$value->godown_id);
            }
        }
        Log::info('Job type', ['type' => $type]);
    }

    // public function stockOutCalculation($update_data,$updatekey){
    //     foreach ($update_data as  $value) {
    //         $key = $value->stock_out_id;
    //         if(array_key_exists($value->stock_out_id,$updatekey)){
    //             $old = $updatekey[$key];
    //             $sameItem = $value->stock_item_id == $old['stock_item_id'];
    //             $sameGodown = $value->godown_id == $old['godown_id'];
    //             $sameQty = $value->qty == $old['qty'];
    //             $sameTotal = abs($value->total - $old['total']) < 0.5;

    //             if($value->godown_id==$updatekey[$value->stock_out_id]['godown_id'] &&
    //                 $value->stock_item_id==$updatekey[$value->stock_out_id]['stock_item_id'] &&
    //                 $value->qty==$updatekey[$value->stock_out_id]['qty'] &&
    //                 abs($value->total-$updatekey[$value->stock_out_id]['total'])<.5
    //             ){
    //                 // No Change
    //                 continue;
    //             }else if($value->godown_id==$updatekey[$value->stock_out_id]['godown_id'] &&
    //                     $value->stock_item_id==$updatekey[$value->stock_out_id]['stock_item_id']){
    //                 // godown and item No change
    //                 // But Value or Qty change
    //                 $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //             }else if($value->godown_id==$updatekey[$value->stock_out_id]['godown_id'] &&
    //                 $value->stock_item_id!=$updatekey[$value->stock_out_id]['stock_item_id']){
    //                     // godown No change
    //                     // But item change
    //                     $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //                     $this->stockReCalculation($updatekey[$value->stock_out_id]['stock_item_id'],$value->godown_id);
    //             }else if($value->stock_item_id==$updatekey[$value->stock_out_id]['stock_item_id'] &&
    //                 $value->godown_id!=$updatekey[$value->stock_out_id]['godown_id']){
    //                     // item No change
    //                     // But godown  change
    //                     $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //                     $this->stockReCalculation($value->stock_item_id,$updatekey[$value->stock_out_id]['godown_id']);
    //             }else if($value->godown_id!=$updatekey[$value->stock_out_id]['godown_id'] &&
    //                     $value->stock_item_id!=$updatekey[$value->stock_out_id]['stock_item_id']){
    //                     // item change
    //                     // godown change
    //                     $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //                     $this->stockReCalculation($updatekey[$value->stock_out_id]['stock_item_id'],$updatekey[$value->stock_out_id]['godown_id']);
    //             }
    //         }
    //     }
    // }

    public function stockOutCalculation($update_data, $updatekey)
    {
        foreach ($update_data as $value) {
            $key = $value->stock_out_id;
            if (array_key_exists($key, $updatekey)) {
                $old = $updatekey[$key];

                $sameItem = $value->stock_item_id == $old['stock_item_id'];
                $sameGodown = $value->godown_id == $old['godown_id'];
                $sameQty = $value->qty == $old['qty'];
                $sameTotal = abs($value->total - $old['total']) < 0.5;

                // if ($sameItem && $sameGodown && $sameQty && $sameTotal) {
                //     continue; // No change
                // }

                if ($sameItem && !$sameGodown) {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                    $this->stockReCalculation($value->stock_item_id, $old['godown_id']);
                } elseif (!$sameItem && $sameGodown) {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                    $this->stockReCalculation($old['stock_item_id'], $value->godown_id);
                } elseif (!$sameItem && !$sameGodown) {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                    $this->stockReCalculation($old['stock_item_id'], $old['godown_id']);
                } else {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                }
            }
        }
    }

    // public function stockInCalculation($update_data,$updatekey){
    //     foreach ($update_data as  $value) {
    //         if(array_key_exists($value->stock_in_id,$updatekey)){
    //             if($value->godown_id==$updatekey[$value->stock_in_id]['godown_id'] &&
    //                 $value->stock_item_id==$updatekey[$value->stock_in_id]['stock_item_id'] &&
    //                 $value->qty==$updatekey[$value->stock_in_id]['qty'] &&
    //                 abs($value->total-$value->total-$updatekey[$value->stock_in_id]['total'])<.5
    //             ){
    //                 // No Change
    //                 continue;
    //             }else if($value->godown_id==$updatekey[$value->stock_in_id]['godown_id'] &&
    //                     $value->stock_item_id==$updatekey[$value->stock_in_id]['stock_item_id']){
    //                 // godown and item No change
    //                 // But Value or Qty change
    //                 $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //             }else if($value->godown_id==$updatekey[$value->stock_in_id]['godown_id'] &&
    //                 $value->stock_item_id!=$updatekey[$value->stock_in_id]['stock_item_id']){
    //                     // godown No change
    //                     // But item change
    //                     $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //                     $this->stockReCalculation($updatekey[$value->stock_in_id]['stock_item_id'],$value->godown_id);
    //             }else if($value->stock_item_id==$updatekey[$value->stock_in_id]['stock_item_id'] &&
    //                 $value->godown_id!=$updatekey[$value->stock_in_id]['godown_id']){
    //                     // item No change
    //                     // But godown  change
    //                     $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //                     $this->stockReCalculation($value->stock_item_id,$updatekey[$value->stock_in_id]['godown_id']);
    //             }else if($value->godown_id!=$updatekey[$value->stock_in_id]['godown_id'] &&
    //                     $value->stock_item_id!=$updatekey[$value->stock_in_id]['stock_item_id']){
    //                     // item change
    //                     // godown change
    //                     $this->stockReCalculation($value->stock_item_id,$value->godown_id);
    //                     $this->stockReCalculation($updatekey[$value->stock_in_id]['stock_item_id'],$updatekey[$value->stock_in_id]['godown_id']);
    //             }
    //         }
    //     }
    // }

    public function stockInCalculation($update_data, $updatekey)
    {
        foreach ($update_data as $value) {
            $key = $value->stock_in_id;
            if (array_key_exists($key, $updatekey)) {
                $old = $updatekey[$key];

                $sameItem = $value->stock_item_id == $old['stock_item_id'];
                $sameGodown = $value->godown_id == $old['godown_id'];
                $sameQty = $value->qty == $old['qty'];
                $sameTotal = abs($value->total - $old['total']) < 0.5;

                // if ($sameItem && $sameGodown && $sameQty && $sameTotal) {
                //     continue; // No change
                // }

                if ($sameItem && !$sameGodown) {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                    $this->stockReCalculation($value->stock_item_id, $old['godown_id']);
                } elseif (!$sameItem && $sameGodown) {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                    $this->stockReCalculation($old['stock_item_id'], $value->godown_id);
                } elseif (!$sameItem && !$sameGodown) {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                    $this->stockReCalculation($old['stock_item_id'], $old['godown_id']);
                } else {
                    $this->stockReCalculation($value->stock_item_id, $value->godown_id);
                }
            }
        }
    }

    public function currentStocks($data,$row){
        // return $data->filter(function ($r) use ($row) {
            // return $r['stock_item_id'] == $row->stock_item_id
            //     && $r['godown_id'] == $row->godown_id
            //     && (
            //         ($r['tran_date'] < $row->tran_date) ||
            //         ($r['tran_date'] == $row->tran_date && $r['tran_id'] < $row->tran_id)
            //     )
            //     && !empty($r['inwards_id']); // Only inwards
        // });
        return $data->filter(function ($r){
            return isset($r['stock_fifo']); // Only inwards
        });
    }

    public function lastStockValue($filtered){
        return $filtered->sum(function ($r) {
            if(!empty($r['inwards_qty']))return ($r['inwards_value'] / $r['inwards_qty']) * $r['stock_fifo'];
            else if(!empty($r['outwards_qty']))return $r['current_rate'] * $r['stock_fifo'];
            else return 0;
        });
    }

    public function consumeStock($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
        $total_quant = $row->outwards_qty;
        // Fetch the stock record by ID
        foreach ($updatedDataOut as $r) {
            if($total_quant<=0){
                break;
            }
            $index = $data->search($r);
            // If total_quant <= stock_fifo, update stock_fifo and set total_quant to 0
            if ($total_quant <= $r['stock_fifo']) {
                $r['stock_fifo'] -= $total_quant;
                $total_quant = 0;
            } else {
                // If total_quant > stock_fifo, set stock_fifo to 0 and decrease total_quant
                $total_quant -= $r['stock_fifo'];
                $r['stock_fifo'] = 0;
            }
            $data[$index] = $r;
            Redis::set('stock', json_encode($data));
        }
        return $total_quant;
    }

    public function consumeStockMinus($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
        $total_quant = $row->inwards_qty;
        // Fetch the stock record by ID
        foreach ($updatedDataOut as $r) {
            if($total_quant>=0){
                break;
            }
            $index = $data->search($r);
            if (abs($total_quant) <= $r['stock_fifo']) {
                $r['stock_fifo'] += $total_quant;
                $total_quant = 0;
            } else {
                $total_quant += $r['stock_fifo'];
                $r['stock_fifo'] = 0;
            }
            $data[$index] = $r;
            Redis::set('stock', json_encode($data));
        }
        return $total_quant;
    }


    public function consumeStockAdjustment($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
        // Log::info('Job Handle Started', [
        //     'updatedDataOut' => $updatedDataOut
        // ]);
        $total_quant = $row->inwards_qty;
        // Fetch the stock record by ID
        foreach ($updatedDataOut as $r) {
            if($total_quant<=0){
                break;
            }
            $index = $data->search($r);
            if ($total_quant <= abs($r['stock_fifo'])) {
                $r['stock_fifo'] += $total_quant;
                $total_quant = 0;
            } else {
                $total_quant += $r['stock_fifo'];
                $r['stock_fifo'] = 0;
            }
            $data[$index] = $r;
            // Log::info('Job Handle Started', [
            //     'data' => $data[$index],
            //     'r' => $r
            // ]);
            Redis::set('stock', json_encode($data));
        }
        return $total_quant;
    }

    public function consumeStockAdjustmentOut($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
        $total_quant = -$row->outwards_qty;
        // Fetch the stock record by ID
        foreach ($updatedDataOut as $r) {
            if($total_quant<=0){
                break;
            }
            $index = $data->search($r);
            if ($total_quant <= abs($r['stock_fifo'])) {
                $r['stock_fifo'] += $total_quant;
                $total_quant = 0;
            } else {
                $total_quant += $r['stock_fifo'];
                $r['stock_fifo'] = 0;
            }
            $data[$index] = $r;
            Redis::set('stock', json_encode($data));
        }
        return $total_quant;
    }


    public function stockReCalculation($stock_item_id,$godown_id){

        $stock_data=DB::table('stock')
        ->where('stock_item_id','=',$stock_item_id)
        ->where('godown_id','=',$godown_id)
        ->orderBy('tran_date','asc')
        ->orderBy('tran_id','asc')
        ->orderBy('id','asc')
        ->get();
        Redis::del('stock');
        Redis::set('stock', json_encode([]));
        // $data = collect(json_decode(Redis::get('stock'), true));
        $data = collect([]);
        foreach ($stock_data as $key=>$row) {
            $updatedData= $row;
            if($row->inwards_id){               
                if($row->inwards_qty<0){
                    $total_quant=$this->consumeStockMinus($data,$row);
                    $data = collect(json_decode(Redis::get('stock'), true));
                    if($total_quant>=0){
                        $filtered = $this->currentStocks($data,$row);
                        $lastValue = $this->lastStockValue($filtered);
                        $lastQty = $filtered->sum('stock_fifo');
                        $updatedData->current_rate = !empty($lastQty) ? ($lastValue / $lastQty) : 0;
                        $updatedData->current_qty=$lastQty+$total_quant;
                    }else if($total_quant<0){
                        $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
                        $updatedData->current_qty=$total_quant;
                    }
                    $updatedData->stock_fifo=$total_quant;
                    
                }else{
                    $filtered = $this->currentStocks($data,$row);
                    $lastQty = $filtered->sum('stock_fifo');
                    if($lastQty <0){
                       $total_quant=$this->consumeStockAdjustment($data,$row);
                       $data = collect(json_decode(Redis::get('stock'), true));
                        if($total_quant<=0){
                            $filtered = $this->currentStocks($data,$row);
                            $lastQty = $filtered->sum('stock_fifo');
                            $lastValue = $this->lastStockValue($filtered);
                            $updatedData->current_rate = !empty($lastQty) ? ($lastValue / $lastQty) : 0;
                            $updatedData->current_qty=$lastQty+$total_quant;
                        }else{
                            $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
                            $updatedData->current_qty=$total_quant;
                        }
                        $updatedData->stock_fifo=$total_quant;
                    }else if($lastQty==0){
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
                        if(!is_null($lastCurrentData) && $lastCurrentData['current_qty']<0){
                            if(abs($lastCurrentData['current_qty'])>=$row->inwards_qty){
                                $updatedData->stock_fifo=0;
                            }else{
                                $updatedData->stock_fifo=$lastCurrentData['current_qty']+$row->inwards_qty;
                            }
                            $updatedData->current_qty=$lastCurrentData['current_qty']+$row->inwards_qty;
                            $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
                        }else{
                            $lastValue = $this->lastStockValue($filtered);
                            $updatedData->stock_fifo=$row->inwards_qty;
                            $updatedData->current_rate = (isset($lastQty) && isset($row->inwards_qty) && ($lastQty+$row->inwards_qty)>0 ) ? (($lastValue+$row->inwards_value) / ($lastQty+$row->inwards_qty)) : 0;
                            $updatedData->current_qty = $lastQty+$row->inwards_qty;
                        }
                    }else{
                        // Calculate weighted sums                    
                        $lastValue = $this->lastStockValue($filtered);
                        $currentRate = (($lastQty+$row->inwards_qty) > 0) ? (($lastValue+$row->inwards_value) / ($lastQty+$row->inwards_qty)) : 0;
                        $updatedData->stock_fifo=$row->inwards_qty;
                        $updatedData->current_rate = $currentRate;
                        $updatedData->current_qty = $lastQty+$row->inwards_qty;
                    }
                    
                }
                
            }else if($row->outwards_id){
                if($row->outwards_qty<0){
                    $data = collect(json_decode(Redis::get('stock'), true));
                    
                    $filtered = $this->currentStocks($data,$row);
                    $lastQty = $filtered->sum('stock_fifo');
                    $updatedData->stock_fifo=-$row->outwards_qty;
                    if($lastQty > 0){
                        $updatedData->current_qty=$lastQty+(-$row->outwards_qty);
                        $lastValue = $this->lastStockValue($filtered);
                        $updatedData->current_rate =  (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
                    }else if($lastQty < 0){
                        $total_quant=$this->consumeStockAdjustmentOut($data,$row);
                        $updatedData->stock_fifo=$total_quant;
                        $updatedData->current_qty=$total_quant;
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                        if(!is_null($lastCurrentData)){
                            $updatedData->current_rate=$lastCurrentData['current_rate']??0;
                        }else{
                            $lastCurrentData = DB::table('stock')
                                ->where('stock_item_id','=',$stock_item_id)
                                ->orderBy('tran_date','DESC')
                                ->orderBy('tran_id','DESC')
                                ->orderBy('id','DESC')
                                ->first();
                            if(!is_null($lastCurrentData)){
                                $updatedData->current_rate=$lastCurrentData->current_rate;
                            }else{
                                $updatedData->current_rate=0;
                            }
                        }
                    }else{
                        // $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                        // $updatedData->current_qty=(-$row->outwards_qty);
                        // if(!is_null($lastCurrentData)){
                        //     $currentRate=$lastCurrentData['current_rate']??0;
                        // }else{
                        //     $currentRate=0;
                        // }                        
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
                        
                        if(!is_null($lastCurrentData) && $lastCurrentData['current_rate']>0){
                            $updatedData->stock_fifo=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
                            $updatedData->current_qty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
                            $updatedData->current_rate=$lastCurrentData['current_rate']??0;
                        }else{
                            $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                            if(!is_null($lastCurrentData)){
                                $updatedData->current_rate=$lastCurrentData['current_rate']??0;
                                $updatedData->stock_fifo=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
                                $updatedData->current_qty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
                            }else{
                                $updatedData->current_rate=!empty($row->outwards_qty) ? ($row->outwards_value / $row->outwards_qty) : 0;
                                $updatedData->stock_fifo=-$row->outwards_qty;
                                $updatedData->current_qty=-$row->outwards_qty;

                            }
                        }
                    }

                }else{
                    $total_quant=$this->consumeStock($data,$row);

                    $data = collect(json_decode(Redis::get('stock'), true));
    
                    $filtered = $this->currentStocks($data,$row);
    
                    // Calculate weighted sums
                    $lastValue = $this->lastStockValue($filtered);
    
    
                    if($total_quant > 0){
                        // ->where('inwards_id','>',0)
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
                        $lastQty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
                        if(!is_null($lastCurrentData) && $lastCurrentData['current_rate']>0){
                            $currentRate=$lastCurrentData['current_rate']??0;
                        }else{
                            // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
                            $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                            if(!is_null($lastCurrentData)){
                                $currentRate=$lastCurrentData['current_rate']??0;
                            }else{
                                $lastCurrentData = DB::table('stock')
                                ->where('stock_item_id','=',$stock_item_id)
                                ->orderBy('tran_date','DESC')
                                ->orderBy('tran_id','DESC')
                                ->orderBy('id','DESC')
                                ->first();
                                if(!is_null($lastCurrentData)){
                                    $currentRate=$lastCurrentData->current_rate;
                                }else{
                                    $currentRate=0;
                                }
                            }
    
                        }
    
                    }else{
                        $lastQty = $filtered->sum('stock_fifo');
                        if($lastQty==0){
                            $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                            if(!is_null($lastCurrentData)){
                                $currentRate=$lastCurrentData['current_rate']??0;
                            }else{
                                $lastCurrentData = DB::table('stock')
                                ->where('stock_item_id','=',$stock_item_id)
                                ->orderBy('tran_date','DESC')
                                ->orderBy('tran_id','DESC')
                                ->orderBy('id','DESC')
                                ->first();
                                if(!is_null($lastCurrentData)){
                                    $currentRate=$lastCurrentData->current_rate;
                                }else{
                                    $currentRate=0;
                                }
                            }
                        }else{
                            $currentRate = (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
                        } 
                    }
                    if($total_quant < 0){
                        $lastQty=-$total_quant;
                        if($lastQty==0){
                            $lastCurrentData= $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                            // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
                            if(!is_null($lastCurrentData)){
                                $currentRate=$lastCurrentData['current_rate']??0;
                            }else{
                                $lastCurrentData = DB::table('stock')
                                ->where('stock_item_id','=',$stock_item_id)
                                ->orderBy('tran_date','DESC')
                                ->orderBy('tran_id','DESC')
                                ->orderBy('id','DESC')
                                ->first();
                                if(!is_null($lastCurrentData)){
                                    $currentRate=$lastCurrentData->current_rate;
                                }else{
                                    $currentRate=0;
                                }
                            }
                        }
                    }
                    if(!$filtered->isEmpty()){
                        // if($lastQty!=$updatedData->current_qty || $updatedData->current_rate!=$currentRate){
                        //     $updatedData->change=1;
                        // }
                        $updatedData->current_qty=$lastQty;
                        $updatedData->current_rate=$currentRate;
    
                        if($total_quant < 0){
                            $updatedData->stock_fifo=-$row->outwards_qty;
                        }
                    }else{
                        // if($lastQty!=-$row->outwards_qty || $updatedData->current_rate!=0){
                        //     $updatedData->change=1;
                        // }
                        $updatedData->current_qty=-$row->outwards_qty;
                        // $updatedData->current_rate=0;
                        $updatedData->current_rate=$currentRate;
                    }
                }

                
            }

            $data[$key]=$updatedData;
            Redis::set('stock', json_encode($data));
            $data = collect(json_decode(Redis::get('stock'), true));
        }
        // if(!empty($data)){
            collect($data)
            ->values()
            ->chunk(2000)
            ->each(function ($chunk) {
                $records = $chunk->map(function ($item) {
                    $item = (array) $item;

                    return [
                        'id' => $item['id'] ?? null,
                        'current_rate' => isset($item['current_rate'])?$item['current_rate'] : 0,
                        'current_qty' => $item['current_qty'] ?? 0,
                        'stock_fifo' => isset($item['stock_fifo']) ? $item['stock_fifo'] : 0, // Fix for `?`
                    ];
                })->toArray();

                DB::table('stock')->upsert(
                    $records,
                    ['id'],
                    ['current_rate', 'current_qty', 'stock_fifo']
                );
            });
            Redis::del('stock');
        // }
    }

}
