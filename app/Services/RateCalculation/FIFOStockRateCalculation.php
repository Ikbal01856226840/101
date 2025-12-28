<?php

// namespace App\Services\RateCalculation;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Redis;
// use Illuminate\Support\Facades\Log;

// class FIFOStockRateCalculation
// {

//     public function currentStocks($data,$row){
//         return $data->filter(function ($r){
//             // return isset($r['stock_fifo']); // Only inwards
//             if (is_array($r)) {
//                 return isset($r['stock_fifo']);
//             } elseif (is_object($r)) {
//                 return isset($r->stock_fifo);
//             }
//             return false;
//         });
//     }   
   

//     public function lastStockValue($filtered){
//         return $filtered->sum(function ($r) {
//             // if(!empty($r['inwards_qty']))return ($r['inwards_value'] / $r['inwards_qty']) * $r['stock_fifo'];
//             // else if(!empty($r['outwards_qty']))return $r['current_rate'] * $r['stock_fifo'];
//             // else return 0;
//             $inwardsQty   = is_array($r) ? ($r['inwards_qty'] ?? null) : ($r->inwards_qty ?? null);
//             $inwardsValue = is_array($r) ? ($r['inwards_value'] ?? null) : ($r->inwards_value ?? null);
//             $stockFifo    = is_array($r) ? ($r['stock_fifo'] ?? 0) : ($r->stock_fifo ?? 0);
//             $outwardsQty  = is_array($r) ? ($r['outwards_qty'] ?? null) : ($r->outwards_qty ?? null);
//             $currentRate  = is_array($r) ? ($r['current_rate'] ?? 0) : ($r->current_rate ?? 0);

//             if (!empty($inwardsQty) && $inwardsQty != 0) {
//                 return ($inwardsValue / $inwardsQty) * $stockFifo;
//             } elseif (!empty($outwardsQty)) {
//                 return $currentRate * $stockFifo;
//             } else {
//                 return 0;
//             }
//         });
//     }


//     public function CheckFifoValidation()
//     {
//         $count = DB::table('stock')
//             ->whereNull('inwards_id')
//             ->where('stock_fifo', '>', 0)
//             ->where('outwards_qty', '>=', 0)
//             ->count();
    
//         if ($count > 0) {
//             DB::update("
//                 UPDATE `stock`
//                 SET `stock_fifo` = 0
//                 WHERE `inwards_id` IS NULL
//                 AND `stock_fifo` > 0
//                 AND `outwards_qty` >= 0
//             ");
//         }
//     }


//     public function consumeStock($data,$row,$storage){
//         // $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
//         // $total_quant = $row->outwards_qty;
//         // // Fetch the stock record by ID
//         // foreach ($updatedDataOut as $r) {
//         //     if($total_quant<=0){
//         //         break;
//         //     }
//         //     $index = $data->search($r);
//         //     // If total_quant <= stock_fifo, update stock_fifo and set total_quant to 0
//         //     if ($total_quant <= $r['stock_fifo']) {
//         //         $r['stock_fifo'] -= $total_quant;
//         //         $total_quant = 0;
//         //     } else {
//         //         // If total_quant > stock_fifo, set stock_fifo to 0 and decrease total_quant
//         //         $total_quant -= $r['stock_fifo'];
//         //         $r['stock_fifo'] = 0;
//         //     }
//         //     $data[$index] = $r;
//         //     Redis::set($storage, json_encode($data));
//         // }
//         // return $total_quant;

//         $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
//         $totalQuant = is_array($row) ? ($row['outwards_qty'] ?? 0) : ($row->outwards_qty ?? 0);
//         foreach ($updatedDataOut as $key => $r) {
//             if ($totalQuant <= 0) break;
//             $index = $data->search($r);
//             $stockFifo = is_array($r) ? ($r['stock_fifo'] ?? 0) : ($r->stock_fifo ?? 0);
//             if ($totalQuant <= $stockFifo) {
//                 if(is_array($r)){
//                     $r['stock_fifo'] -= $totalQuant;
//                 }else{
//                     $r->stock_fifo -= $totalQuant;
//                 }
//                 $totalQuant=0;
//             }else{
//                 if(is_array($r)){
//                      $totalQuant-=$r['stock_fifo'];
//                      $r['stock_fifo']=0;
//                 }else{
//                     $totalQuant-=$r->stock_fifo;
//                     $r->stock_fifo=0;
//                 }
//             }
//             $data[$index] = $r;
//             Redis::set($storage, json_encode($data));
//         }
//         return $totalQuant;

//     }

//     public function consumeStockMinus($data,$row,$storage){
//         // $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
//         // $total_quant = $row->inwards_qty;
//         // // Fetch the stock record by ID
//         // foreach ($updatedDataOut as $r) {
//         //     if($total_quant>=0){
//         //         break;
//         //     }
//         //     $index = $data->search($r);
//         //     if (abs($total_quant) <= $r['stock_fifo']) {
//         //         $r['stock_fifo'] += $total_quant;
//         //         $total_quant = 0;
//         //     } else {
//         //         $total_quant += $r['stock_fifo'];
//         //         $r['stock_fifo'] = 0;
//         //     }
//         //     $data[$index] = $r;
//         //     Redis::set($storage, json_encode($data));
//         // }
//         // return $total_quant;

//         $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
//         $totalQuant = is_array($row) ? ($row['inwards_qty'] ?? 0) : ($row->inwards_qty ?? 0);
//         foreach ($updatedDataOut as $r) {
//             if ($totalQuant >= 0) break;
//             $index = $data->search($r);
//             $stockFifo = is_array($r) ? ($r['stock_fifo'] ?? 0) : ($r->stock_fifo ?? 0);
//             if (abs($totalQuant) <= $stockFifo) {
//                 if(is_array($r)){
//                     $r['stock_fifo'] += $totalQuant;
//                 }else{
//                     $r->stock_fifo += $totalQuant;
//                 }
//                 $totalQuant=0;
//             }else{
//                 if(is_array($r)){
//                      $totalQuant+=$r['stock_fifo'];
//                      $r['stock_fifo']=0;
//                 }else{
//                     $totalQuant+=$r->stock_fifo;
//                     $r->stock_fifo=0;
//                 }
//             }
//             $data[$index] = $r;
//             Redis::set($storage, json_encode($data));
//         }
//     }

//     public function consumeStockAdjustment($data,$row,$storage){
//         // $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
//         // $total_quant = $row->inwards_qty;
//         // // Fetch the stock record by ID
//         // foreach ($updatedDataOut as $r) {
//         //     if($total_quant<=0){
//         //         break;
//         //     }
//         //     $index = $data->search($r);
//         //     if ($total_quant <= abs($r['stock_fifo'])) {
//         //         $r['stock_fifo'] += $total_quant;
//         //         $total_quant = 0;
//         //     } else {
//         //         $total_quant += $r['stock_fifo'];
//         //         $r['stock_fifo'] = 0;
//         //     }
//         //     $data[$index] = $r;

//         //     Redis::set($storage, json_encode($data));
//         // }
//         // return $total_quant;

//         $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
//         $totalQuant = is_array($row) ? ($row['inwards_qty'] ?? 0) : ($row->inwards_qty ?? 0);
//         foreach ($updatedDataOut as $r) {
//             if ($totalQuant <= 0) break;
//             $index = $data->search($r);
//             $stockFifo = is_array($r) ? ($r['stock_fifo'] ?? 0) : ($r->stock_fifo ?? 0);
//             if ($totalQuant <= abs($stockFifo)) {
//                 if(is_array($r)){
//                     $r['stock_fifo'] += $totalQuant;
//                 }else{
//                     $r->stock_fifo += $totalQuant;
//                 }
//                 $totalQuant=0;
//             }else{
//                 if(is_array($r)){
//                      $totalQuant+=$r['stock_fifo'];
//                      $r['stock_fifo']=0;
//                 }else{
//                     $totalQuant+=$r->stock_fifo;
//                     $r->stock_fifo=0;
//                 }
//             }
//             $data[$index] = $r;
//             Redis::set($storage, json_encode($data));
//         }
//     }


//     public function consumeStockAdjustmentOut($data,$row,$storage){
//         // $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
//         // $total_quant = -$row->outwards_qty;
//         // // Fetch the stock record by ID
//         // foreach ($updatedDataOut as $r) {
//         //     if($total_quant<=0){
//         //         break;
//         //     }
//         //     $index = $data->search($r);
//         //     if ($total_quant <= abs($r['stock_fifo'])) {
//         //         $r['stock_fifo'] += $total_quant;
//         //         $total_quant = 0;
//         //     } else {
//         //         $total_quant += $r['stock_fifo'];
//         //         $r['stock_fifo'] = 0;
//         //     }
//         //     $data[$index] = $r;
//         //     Redis::set($storage, json_encode($data));
//         // }
//         // return $total_quant;

//         $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
//         $totalQuant = is_array($row) ? ($row['outwards_qty'] ?? 0) : ($row->outwards_qty ?? 0);
//         foreach ($updatedDataOut as $r) {
//             if ($totalQuant <= 0) break;
//             $index = $data->search($r);
//             $stockFifo = is_array($r) ? ($r['stock_fifo'] ?? 0) : ($r->stock_fifo ?? 0);
//             if ($totalQuant <= abs($stockFifo)) {
//                 if(is_array($r)){
//                     $r['stock_fifo'] += $totalQuant;
//                 }else{
//                     $r->stock_fifo += $totalQuant;
//                 }
//                 $totalQuant=0;
//             }else{
//                 if(is_array($r)){
//                      $totalQuant+=$r['stock_fifo'];
//                      $r['stock_fifo']=0;
//                 }else{
//                     $totalQuant+=$r->stock_fifo;
//                     $r->stock_fifo=0;
//                 }
//             }
//             $data[$index] = $r;
//             Redis::set($storage, json_encode($data));
//         }
//     }


//     public function StockInInsert($stockItemId, $godownId,$tranId){
//         $stock_data=DB::table('stock')
//             ->where(function($query) {
//                 $query->where('stock_fifo', '>', 0)
//                     ->orWhere('stock_fifo', '<', 0);
//             })
//             ->where('stock_item_id', '=', $stockItemId)
//             ->where('godown_id', '=', $godownId)
//             ->where('tran_id', '!=', $tranId)
//             ->orderBy('tran_date','asc')
//             ->orderBy('tran_id','asc')
//             ->orderBy('id','asc')
//             ->get();

//         $storage='stockIn';
//         Redis::del($storage);
//         Redis::set($storage, json_encode($stock_data));
//         $data = collect(json_decode(Redis::get($storage), true));
//         $stock_in_data=DB::table('stock')
//             ->where('stock_item_id', '=', $stockItemId)
//             ->where('godown_id', '=', $godownId)
//             ->where('tran_id', '=', $tranId)
//             ->orderBy('id','asc')
//             ->get();
//         foreach ($stock_in_data as $key=>$row) {
//             $updatedData= $row;
//             if($row->inwards_qty<0){
//                 $total_quant=$this->consumeStockMinus($data,$row,$storage);
//                 $data = collect(json_decode(Redis::get($storage), true));
//                 if($total_quant>=0){
//                     $filtered = $this->currentStocks($data,$row);
//                     $lastValue = $this->lastStockValue($filtered);
//                     $lastQty = $filtered->sum('stock_fifo');
//                     $updatedData->current_rate = !empty($lastQty) ? ($lastValue / $lastQty) : 0;
//                     $updatedData->current_qty=$lastQty+$total_quant;
//                 }else if($total_quant<0){
//                     $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
//                     $updatedData->current_qty=$total_quant;
//                 }
//                 $updatedData->stock_fifo=$total_quant;

//             }else{
//                 $filtered = $this->currentStocks($data,$row);
//                 $lastQty = $filtered->sum('stock_fifo');
//                 if($lastQty <0){
//                     $total_quant=$this->consumeStockAdjustment($data,$row,$storage);
//                     $data = collect(json_decode(Redis::get($storage), true));
//                     if($total_quant<=0){
//                         $filtered = $this->currentStocks($data,$row);
//                         $lastQty = $filtered->sum('stock_fifo');
//                         $lastValue = $this->lastStockValue($filtered);
//                         $updatedData->current_rate = !empty($lastQty) ? ($lastValue / $lastQty) : 0;
//                         $updatedData->current_qty=$lastQty+$total_quant;
//                     }else{
//                         $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
//                         $updatedData->current_qty=$total_quant;
//                     }
//                     $updatedData->stock_fifo=$total_quant;
//                 }else if($lastQty==0){
//                     $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
//                     if(!is_null($lastCurrentData) && $lastCurrentData['current_qty']<0){
//                         if(abs($lastCurrentData['current_qty'])>=$row->inwards_qty){
//                             $updatedData->stock_fifo=0;
//                             }else{
//                             $updatedData->stock_fifo=$lastCurrentData['current_qty']+$row->inwards_qty;
//                             }
//                         $updatedData->current_qty=$lastCurrentData['current_qty']+$row->inwards_qty;
//                         $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
//                     }else{
//                         $lastValue = $this->lastStockValue($filtered);
//                         $updatedData->stock_fifo=$row->inwards_qty;
//                         $updatedData->current_rate = (isset($lastQty) && isset($row->inwards_qty) && ($lastQty+$row->inwards_qty)>0 ) ? (($lastValue+$row->inwards_value) / ($lastQty+$row->inwards_qty)) : 0;
//                         $updatedData->current_qty = $lastQty+$row->inwards_qty;
//                     }
//                 }else{
//                     // Calculate weighted sums
//                     $lastValue = $this->lastStockValue($filtered);
//                     $currentRate = (($lastQty+$row->inwards_qty) > 0) ? (($lastValue+$row->inwards_value) / ($lastQty+$row->inwards_qty)) : 0;
//                     $updatedData->stock_fifo=$row->inwards_qty;
//                     $updatedData->current_rate = $currentRate;
//                     $updatedData->current_qty = $lastQty+$row->inwards_qty;
//                 }

//             }


//             $data[]=$updatedData;
//             Redis::set($storage, json_encode($data));
//             $data = collect(json_decode(Redis::get($storage), true));
//         }

//         collect($data)
//         ->values()
//         ->chunk(1000)
//         ->each(function ($chunk) {
//             $records = $chunk->map(function ($item) {
//                 $item = (array) $item;

//                 return [
//                     'id' => $item['id'] ?? null,
//                     'current_rate' => isset($item['current_rate'])?$item['current_rate'] : 0,
//                     'current_qty' => $item['current_qty'] ?? 0,
//                     'stock_fifo' => isset($item['stock_fifo']) ? $item['stock_fifo'] : 0, // Fix for `?`
//                 ];
//             })->toArray();

//             DB::table('stock')->upsert(
//                 $records,
//                 ['id'],
//                 ['current_rate', 'current_qty', 'stock_fifo']
//             );
//         });
//         Redis::del($storage);
//     }

//     public function StockOutInsert($stockItemId, $godownId,$tranId){
//         $stock_data=DB::table('stock')
//             ->where(function($query) {
//                 $query->where('stock_fifo', '>', 0)
//                     ->orWhere('stock_fifo', '<', 0);
//             })
//             ->where('stock_item_id', '=', $stockItemId)
//             ->where('godown_id', '=', $godownId)
//             ->where('tran_id', '!=', $tranId)
//             ->orderBy('tran_date','asc')
//             ->orderBy('tran_id','asc')
//             ->orderBy('id','asc')
//             ->get();
//         $storage='stockOut';
//         Redis::del($storage);
//         Redis::set($storage, json_encode($stock_data));
//         $data = collect(json_decode(Redis::get($storage), true));
//         $stock_out_data=DB::table('stock')
//             ->where('stock_item_id', '=', $stockItemId)
//             ->where('godown_id', '=', $godownId)
//             ->where('tran_id', '=', $tranId)
//             ->orderBy('id','asc')
//             ->get();
//         foreach ($stock_out_data as $key=>$row) {
//             $updatedData= $row;
//             if($row->outwards_qty<0){
//                 $data = collect(json_decode(Redis::get($storage), true));

//                 $filtered = $this->currentStocks($data,$row);
//                 $lastQty = $filtered->sum('stock_fifo');
//                 $updatedData->stock_fifo=-$row->outwards_qty;
//                 if($lastQty > 0){
//                     $updatedData->current_qty=$lastQty+(-$row->outwards_qty);
//                     $lastValue = $this->lastStockValue($filtered);
//                     $updatedData->current_rate =  (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
//                 }else if($lastQty < 0){
//                     $total_quant=$this->consumeStockAdjustmentOut($data,$row,$storage);
//                     $updatedData->stock_fifo=$total_quant;
//                     $updatedData->current_qty=$total_quant;
//                     $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                     if(!is_null($lastCurrentData)){
//                         $updatedData->current_rate=$lastCurrentData['current_rate']??0;
//                     }else{
//                         $lastCurrentData = DB::table('stock')
//                             ->where('stock_item_id','=',$stockItemId)
//                             ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                             ->orderBy('tran_date','DESC')
//                             ->orderBy('tran_id','DESC')
//                             ->orderBy('id','DESC')
//                             ->first();
//                         if(!is_null($lastCurrentData)){
//                             $updatedData->current_rate=$lastCurrentData->current_rate;
//                         }else{
//                             $updatedData->current_rate=0;
//                         }
//                     }
//                 }else{
//                     // $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                     // $updatedData->current_qty=(-$row->outwards_qty);
//                     // if(!is_null($lastCurrentData)){
//                     //     $currentRate=$lastCurrentData['current_rate']??0;
//                     // }else{
//                     //     $currentRate=0;
//                     // }
//                     $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();

//                     if(!is_null($lastCurrentData) && $lastCurrentData['current_rate']>0){
//                         $updatedData->stock_fifo=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                         $updatedData->current_qty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                         $updatedData->current_rate=$lastCurrentData['current_rate']??0;
//                     }else{
//                         $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                         if(!is_null($lastCurrentData)){
//                             $updatedData->current_rate=$lastCurrentData['current_rate']??0;
//                             $updatedData->stock_fifo=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                             $updatedData->current_qty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                         }else{
//                             $updatedData->current_rate=!empty($row->outwards_qty) ? ($row->outwards_value / $row->outwards_qty) : 0;
//                             $updatedData->stock_fifo=-$row->outwards_qty;
//                             $updatedData->current_qty=-$row->outwards_qty;

//                         }
//                     }
//                 }

//             }else{
//                 $total_quant=$this->consumeStock($data,$row,$storage);

//                 $data = collect(json_decode(Redis::get($storage), true));

//                 $filtered = $this->currentStocks($data,$row);

//                 // Calculate weighted sums
//                 $lastValue = $this->lastStockValue($filtered);


//                 if($total_quant > 0){
//                     // ->where('inwards_id','>',0)
//                     $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
//                     $lastQty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                     if(!is_null($lastCurrentData) && $lastCurrentData['current_rate']>0){
//                         $currentRate=$lastCurrentData['current_rate']??0;
//                     }else{
//                         // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
//                         $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                         if(!is_null($lastCurrentData)){
//                             $currentRate=$lastCurrentData['current_rate']??0;
//                         }else{
//                             $lastCurrentData = DB::table('stock')
//                             ->where('stock_item_id','=',$stockItemId)
//                             ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                             ->orderBy('tran_date','DESC')
//                             ->orderBy('tran_id','DESC')
//                             ->orderBy('id','DESC')
//                             ->first();
//                             if(!is_null($lastCurrentData)){
//                                 $currentRate=$lastCurrentData->current_rate;
//                             }else{
//                                 $currentRate=0;
//                             }
//                         }

//                     }

//                 }else{
//                     $lastQty = $filtered->sum('stock_fifo');
//                     if($lastQty==0){
//                         $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                         if(!is_null($lastCurrentData)){
//                             $currentRate=$lastCurrentData['current_rate']??0;
//                         }else{
//                             $lastCurrentData = DB::table('stock')
//                             ->where('stock_item_id','=',$stockItemId)
//                             ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                             ->orderBy('tran_date','DESC')
//                             ->orderBy('tran_id','DESC')
//                             ->orderBy('id','DESC')
//                             ->first();
//                             if(!is_null($lastCurrentData)){
//                                 $currentRate=$lastCurrentData->current_rate;
//                             }else{
//                                 $currentRate=0;
//                             }
//                         }
//                     }else{
//                         $currentRate = (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
//                     }
//                 }
//                 if($total_quant < 0){
//                     $lastQty=-$total_quant;
//                     if($lastQty==0){
//                         $lastCurrentData= $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                         // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
//                         if(!is_null($lastCurrentData)){
//                             $currentRate=$lastCurrentData['current_rate']??0;
//                         }else{
//                             $lastCurrentData = DB::table('stock')
//                             ->where('stock_item_id','=',$stockItemId)
//                             ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                             ->orderBy('tran_date','DESC')
//                             ->orderBy('tran_id','DESC')
//                             ->orderBy('id','DESC')
//                             ->first();
//                             if(!is_null($lastCurrentData)){
//                                 $currentRate=$lastCurrentData->current_rate;
//                             }else{
//                                 $currentRate=0;
//                             }
//                         }
//                     }
//                 }
//                 if(!$filtered->isEmpty()){
//                     // if($lastQty!=$updatedData->current_qty || $updatedData->current_rate!=$currentRate){
//                     //     $updatedData->change=1;
//                     // }
//                     $updatedData->current_qty=$lastQty;
//                     $updatedData->current_rate=$currentRate;

//                     if($total_quant < 0){
//                         // $updatedData->stock_fifo=-$row->outwards_qty;
//                     }
//                 }else{
//                     // if($lastQty!=-$row->outwards_qty || $updatedData->current_rate!=0){
//                     //     $updatedData->change=1;
//                     // }
//                     $updatedData->current_qty=-$row->outwards_qty;
//                     // $updatedData->current_rate=0;
//                     $updatedData->current_rate=$currentRate;
//                 }
//                 $updatedData->stock_fifo=0;
//             }
//             $data[]=$updatedData;
//             Redis::set($storage, json_encode($data));
//             $data = collect(json_decode(Redis::get($storage), true));
//         }

//         collect($data)
//         ->values()
//         ->chunk(1000)
//         ->each(function ($chunk) {
//             $records = $chunk->map(function ($item) {
//                 $item = (array) $item;

//                 return [
//                     'id' => $item['id'] ?? null,
//                     'current_rate' => isset($item['current_rate'])?$item['current_rate'] : 0,
//                     'current_qty' => $item['current_qty'] ?? 0,
//                     'stock_fifo' => isset($item['stock_fifo']) ? $item['stock_fifo'] : 0, // Fix for `?`
//                 ];
//             })->toArray();

//             DB::table('stock')->upsert(
//                 $records,
//                 ['id'],
//                 ['current_rate', 'current_qty', 'stock_fifo']
//             );
//         });
//         Redis::del($storage);
//     }


//     public function stockOutCalculation($update_data, $updatekey)
//     {
//         foreach ($update_data as $value) {
//             $key = $value->stock_out_id;
//             if (array_key_exists($key, $updatekey)) {
//                 $old = $updatekey[$key];

//                 $sameItem = $value->stock_item_id == $old['stock_item_id'];
//                 $sameGodown = $value->godown_id == $old['godown_id'];
//                 $sameQty = $value->qty == $old['qty'];
//                 $sameTotal = abs($value->total - $old['total']) < 0.5;
//                 $sameTranDate = $value->tran_date == $old['tran_date']; // add this line
//                 if ($sameItem && $sameGodown && $sameQty && $sameTotal && $sameTranDate) {
//                     continue; // No change
//                 }

//                 if ($sameItem && !$sameGodown) {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                     $this->stockReCalculation($value->stock_item_id, $old['godown_id']);
//                 } elseif (!$sameItem && $sameGodown) {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                     $this->stockReCalculation($old['stock_item_id'], $value->godown_id);
//                 } elseif (!$sameItem && !$sameGodown) {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                     $this->stockReCalculation($old['stock_item_id'], $old['godown_id']);
//                 } else {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                 }
//             }
//         }
//     }

//     public function stockInCalculation($update_data, $updatekey)
//     {
//         foreach ($update_data as $value) {
//             $key = $value->stock_in_id;
//             if (array_key_exists($key, $updatekey)) {
//                 $old = $updatekey[$key];

//                 $sameItem = $value->stock_item_id == $old['stock_item_id'];
//                 $sameGodown = $value->godown_id == $old['godown_id'];
//                 $sameQty = $value->qty == $old['qty'];
//                 $sameTotal = abs($value->total - $old['total']) < 0.5;
//                 $sameTranDate = $value->tran_date == $old['tran_date']; // add this line
//                 if ($sameItem && $sameGodown && $sameQty && $sameTotal && $sameTranDate) {
//                     continue; // No change
//                 }

//                 if ($sameItem && !$sameGodown) {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                     $this->stockReCalculation($value->stock_item_id, $old['godown_id']);
//                 } elseif (!$sameItem && $sameGodown) {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                     $this->stockReCalculation($old['stock_item_id'], $value->godown_id);
//                 } elseif (!$sameItem && !$sameGodown) {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                     $this->stockReCalculation($old['stock_item_id'], $old['godown_id']);
//                 } else {
//                     $this->stockReCalculation($value->stock_item_id, $value->godown_id);
//                 }
//             }
//         }
//     }

//     public function stockReCalculation($stock_item_id,$godown_id){

//         $stock_data=DB::table('stock')
//         ->where('stock_item_id','=',$stock_item_id)
//         ->where('godown_id','=',$godown_id)
//         ->orderBy('tran_date','asc')
//         ->orderBy('tran_id','asc')
//         ->orderBy('id','asc')
//         ->get();
//         $storage='stock';
//         Redis::del($storage);
//         Redis::set($storage, json_encode([]));
//         $data = collect([]);
//         foreach ($stock_data as $key=>$row) {
//             $updatedData= $row;
//             if($row->inwards_id){               
//                 if($row->inwards_qty<0){
//                     $total_quant=$this->consumeStockMinus($data,$row,$storage);
//                     $data = collect(json_decode(Redis::get($storage), true));
//                     if($total_quant>=0){
//                         $filtered = $this->currentStocks($data,$row);
//                         $lastValue = $this->lastStockValue($filtered);
//                         $lastQty = $filtered->sum('stock_fifo');
//                         $updatedData->current_rate = !empty($lastQty) ? ($lastValue / $lastQty) : 0;
//                         $updatedData->current_qty=$lastQty+$total_quant;
//                     }else if($total_quant<0){
//                         $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
//                         $updatedData->current_qty=$total_quant;
//                     }
//                     $updatedData->stock_fifo=$total_quant;
                    
//                 }else{
//                     $filtered = $this->currentStocks($data,$row);
//                     $lastQty = $filtered->sum('stock_fifo');
//                     if($lastQty <0){
//                        $total_quant=$this->consumeStockAdjustment($data,$row,$storage);
//                        $data = collect(json_decode(Redis::get($storage), true));
//                         if($total_quant<=0){
//                             $filtered = $this->currentStocks($data,$row);
//                             $lastQty = $filtered->sum('stock_fifo');
//                             $lastValue = $this->lastStockValue($filtered);
//                             $updatedData->current_rate = !empty($lastQty) ? ($lastValue / $lastQty) : 0;
//                             $updatedData->current_qty=$lastQty+$total_quant;
//                         }else{
//                             $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
//                             $updatedData->current_qty=$total_quant;
//                         }
//                         $updatedData->stock_fifo=$total_quant;
//                     }else if($lastQty==0){
//                         $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
//                         if(!is_null($lastCurrentData) && $lastCurrentData['current_qty']<0){
//                             if(abs($lastCurrentData['current_qty'])>=$row->inwards_qty){
//                                 $updatedData->stock_fifo=0;
//                               }else{
//                                 $updatedData->stock_fifo=$lastCurrentData['current_qty']+$row->inwards_qty;
//                               }
//                             $updatedData->current_qty=$lastCurrentData['current_qty']+$row->inwards_qty;
//                             $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
//                         }else{
//                             // $lastValue = $this->lastStockValue($filtered);
//                             $updatedData->stock_fifo=$row->inwards_qty;
//                             $updatedData->current_rate = (isset($row->inwards_qty) && ($row->inwards_qty)>0 ) ? (($row->inwards_value) / ($row->inwards_qty)) : 0;
//                             $updatedData->current_qty = $row->inwards_qty;
//                         }
//                     }else{
//                         // Calculate weighted sums                    
//                         $lastValue = $this->lastStockValue($filtered);
//                         $currentRate = (($lastQty+$row->inwards_qty) > 0) ? (($lastValue+$row->inwards_value) / ($lastQty+$row->inwards_qty)) : 0;
//                         $updatedData->stock_fifo=$row->inwards_qty;
//                         $updatedData->current_rate = $currentRate;
//                         $updatedData->current_qty = $lastQty+$row->inwards_qty;
//                     }
                    
//                 }
                
//             }else if($row->outwards_id){
//                 if($row->outwards_qty<0){
//                     $data = collect(json_decode(Redis::get($storage), true));
                    
//                     $filtered = $this->currentStocks($data,$row);
//                     $lastQty = $filtered->sum('stock_fifo');
//                     $updatedData->stock_fifo=-$row->outwards_qty;
//                     if($lastQty > 0){
//                         $updatedData->current_qty=$lastQty+(-$row->outwards_qty);
//                         $lastValue = $this->lastStockValue($filtered);
//                         $updatedData->current_rate =  (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
//                     }else if($lastQty < 0){
//                         $total_quant=$this->consumeStockAdjustmentOut($data,$row,$storage);
//                         $updatedData->stock_fifo=$total_quant;
//                         $updatedData->current_qty=$total_quant;
//                         $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                         if(!is_null($lastCurrentData)){
//                             $updatedData->current_rate=$lastCurrentData['current_rate']??0;
//                         }else{
//                             $lastCurrentData = DB::table('stock')
//                                 ->where('stock_item_id','=',$stock_item_id)
//                                 ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                                 ->orderBy('tran_date','DESC')
//                                 ->orderBy('tran_id','DESC')
//                                 ->orderBy('id','DESC')
//                                 ->first();
//                             if(!is_null($lastCurrentData)){
//                                 $updatedData->current_rate=$lastCurrentData->current_rate;
//                             }else{
//                                 $updatedData->current_rate=0;
//                             }
//                         }
//                     }else{
//                         // $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                         // $updatedData->current_qty=(-$row->outwards_qty);
//                         // if(!is_null($lastCurrentData)){
//                         //     $currentRate=$lastCurrentData['current_rate']??0;
//                         // }else{
//                         //     $currentRate=0;
//                         // }                        
//                         $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
                        
//                         if(!is_null($lastCurrentData) && $lastCurrentData['current_rate']>0){
//                             $updatedData->stock_fifo=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                             $updatedData->current_qty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                             $updatedData->current_rate=$lastCurrentData['current_rate']??0;
//                         }else{
//                             $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                             if(!is_null($lastCurrentData)){
//                                 $updatedData->current_rate=$lastCurrentData['current_rate']??0;
//                                 $updatedData->stock_fifo=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                                 $updatedData->current_qty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                             }else{
//                                 $updatedData->current_rate=!empty($row->outwards_qty) ? ($row->outwards_value / $row->outwards_qty) : 0;
//                                 $updatedData->stock_fifo=-$row->outwards_qty;
//                                 $updatedData->current_qty=-$row->outwards_qty;

//                             }
//                         }
//                     }

//                 }else{
//                     $total_quant=$this->consumeStock($data,$row,$storage);

//                     $data = collect(json_decode(Redis::get($storage), true));
    
//                     $filtered = $this->currentStocks($data,$row);
    
//                     // Calculate weighted sums
//                     $lastValue = $this->lastStockValue($filtered);
    
    
//                     if($total_quant > 0){
//                         // ->where('inwards_id','>',0)
//                         $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
//                         $lastQty=($lastCurrentData['current_qty']??0)-$row->outwards_qty;
//                         if(!is_null($lastCurrentData) && $lastCurrentData['current_rate']>0){
//                             $currentRate=$lastCurrentData['current_rate']??0;
//                         }else{
//                             // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
//                             $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                             if(!is_null($lastCurrentData)){
//                                 $currentRate=$lastCurrentData['current_rate']??0;
//                             }else{
//                                 $lastCurrentData = DB::table('stock')
//                                 ->where('stock_item_id','=',$stock_item_id)
//                                 ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                                 ->orderBy('tran_date','DESC')
//                                 ->orderBy('tran_id','DESC')
//                                 ->orderBy('id','DESC')
//                                 ->first();
//                                 if(!is_null($lastCurrentData)){
//                                     $currentRate=$lastCurrentData->current_rate;
//                                 }else{
//                                     $currentRate=0;
//                                 }
//                             }
    
//                         }
    
//                     }else{
//                         $lastQty = $filtered->sum('stock_fifo');
//                         if($lastQty==0){
//                             $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                             if(!is_null($lastCurrentData)){
//                                 $currentRate=$lastCurrentData['current_rate']??0;
//                             }else{
//                                 $lastCurrentData = DB::table('stock')
//                                 ->where('stock_item_id','=',$stock_item_id)
//                                 ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                                 ->orderBy('tran_date','DESC')
//                                 ->orderBy('tran_id','DESC')
//                                 ->orderBy('id','DESC')
//                                 ->first();
//                                 if(!is_null($lastCurrentData)){
//                                     $currentRate=$lastCurrentData->current_rate;
//                                 }else{
//                                     $currentRate=0;
//                                 }
//                             }
//                         }else{
//                             $currentRate = (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
//                         }                        
//                     }
//                     if($total_quant < 0){
//                         $lastQty=-$total_quant;
//                         if($lastQty==0){
//                             $lastCurrentData= $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
//                             // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
//                             if(!is_null($lastCurrentData)){
//                                 $currentRate=$lastCurrentData['current_rate']??0;
//                             }else{
//                                 $lastCurrentData = DB::table('stock')
//                                 ->where('stock_item_id','=',$stock_item_id)
//                                 ->where('current_rate','>',0)
//                                 ->where('tran_id','<',$row->tran_id)
//                                 ->where('inwards_id','>',0)
//                                 ->orderBy('tran_date','DESC')
//                                 ->orderBy('tran_id','DESC')
//                                 ->orderBy('id','DESC')
//                                 ->first();
//                                 if(!is_null($lastCurrentData)){
//                                     $currentRate=$lastCurrentData->current_rate;
//                                 }else{
//                                     $currentRate=0;
//                                 }
//                             }
//                         }
//                     }
//                     if(!$filtered->isEmpty()){
//                         // if($lastQty!=$updatedData->current_qty || $updatedData->current_rate!=$currentRate){
//                         //     $updatedData->change=1;
//                         // }
//                         $updatedData->current_qty=$lastQty;
//                         $updatedData->current_rate=$currentRate;
    
//                         if($total_quant < 0){
//                             // $updatedData->stock_fifo=-$row->outwards_qty;
//                         }
//                     }else{
//                         // if($lastQty!=-$row->outwards_qty || $updatedData->current_rate!=0){
//                         //     $updatedData->change=1;
//                         // }
//                         $updatedData->current_qty=-$row->outwards_qty;
//                         // $updatedData->current_rate=0;
//                         $updatedData->current_rate=$currentRate;
//                     }
//                     $updatedData->stock_fifo=0;
//                 }                
//             }

//             $data[$key]=$updatedData;
//             Redis::set($storage, json_encode($data));
//             $data = collect(json_decode(Redis::get($storage), true));
//         }
//         // if(!empty($data)){
//             collect($data)
//             ->values()
//             ->chunk(2000)
//             ->each(function ($chunk) {
//                 $records = $chunk->map(function ($item) {
//                     $item = (array) $item;

//                     return [
//                         'id' => $item['id'] ?? null,
//                         'current_rate' => isset($item['current_rate'])?$item['current_rate'] : 0,
//                         'current_qty' => $item['current_qty'] ?? 0,
//                         'stock_fifo' => isset($item['stock_fifo']) ? $item['stock_fifo'] : 0, // Fix for `?`
//                     ];
//                 })->toArray();

//                 DB::table('stock')->upsert(
//                     $records,
//                     ['id'],
//                     ['current_rate', 'current_qty', 'stock_fifo']
//                 );
//             });
//             Redis::del($storage);
//         // }
//     }
// }








namespace App\Services\RateCalculation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class FIFOStockRateCalculation
{

    public function currentStocks($data,$row){
        return $data->filter(function ($r){
            return fast_get($r, 'stock_fifo') !== null;
        });
    }


    public function lastStockValue($filtered){
        return $filtered->sum(function ($r) {
            $inwardsQty   = fast_get($r,'inwards_qty',0);
            $inwardsValue = fast_get($r,'inwards_value',0);
            $stockFifo    = fast_get($r,'stock_fifo',0);
            $outwardsQty  = fast_get($r,'outwards_qty',0);
            $currentRate  = fast_get($r,'current_rate',0);
            if (!empty($inwardsQty) && $inwardsQty != 0) {
                return ($inwardsValue / $inwardsQty) * $stockFifo;
            } elseif (!empty($outwardsQty)) {
                return $currentRate * $stockFifo;
            } else {
                return 0;
            }
        });
    }


    public function CheckFifoValidation()
    {
        $count = DB::table('stock')
            ->whereNull('inwards_id')
            ->where('stock_fifo', '>', 0)
            ->where('outwards_qty', '>=', 0)
            ->count();

        if ($count > 0) {
            DB::update("
                UPDATE `stock`
                SET `stock_fifo` = 0
                WHERE `inwards_id` IS NULL
                AND `stock_fifo` > 0
                AND `outwards_qty` >= 0
            ");
        }
    }


    public function consumeStock($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
        $totalQuant = fast_get($row,'outwards_qty',0);
        foreach ($updatedDataOut as $key => $r) {
            if ($totalQuant <= 0) break;
            $index = $data->search($r);
            $stockFifo = fast_get($r,'stock_fifo',0);
            if ($totalQuant <= $stockFifo) {
                if(is_array($r)){
                    $r['stock_fifo'] -= $totalQuant;
                }else{
                    $r->stock_fifo -= $totalQuant;
                }
                $totalQuant=0;
            }else{
                if(is_array($r)){
                     $totalQuant-=$r['stock_fifo'];
                     $r['stock_fifo']=0;
                }else{
                    $totalQuant-=$r->stock_fifo;
                    $r->stock_fifo=0;
                }
            }
            $data[$index] = $r;
        }
        return ['totalQuant'=>$totalQuant,'data'=>$data];
    }

    public function consumeStockMinus($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '>' , 0)->values()->all();
        $totalQuant = fast_get($row,'inwards_qty',0);
        foreach ($updatedDataOut as $r) {
            if ($totalQuant >= 0) break;
            $index = $data->search($r);
            $stockFifo = fast_get($r,'stock_fifo',0);
            if (abs($totalQuant) <= $stockFifo) {
                if(is_array($r)){
                    $r['stock_fifo'] += $totalQuant;
                }else{
                    $r->stock_fifo += $totalQuant;
                }
                $totalQuant=0;
            }else{
                if(is_array($r)){
                     $totalQuant+=$r['stock_fifo'];
                     $r['stock_fifo']=0;
                }else{
                    $totalQuant+=$r->stock_fifo;
                    $r->stock_fifo=0;
                }
            }
            $data[$index] = $r;
        }
        return ['totalQuant'=>$totalQuant,'data'=>$data];
    }

    public function consumeStockAdjustment($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
        $totalQuant = fast_get($row,'inwards_qty',0);
        foreach ($updatedDataOut as $r) {
            if ($totalQuant <= 0) break;
            $index = $data->search($r);
            $stockFifo = fast_get($r,'stock_fifo',0);
            if ($totalQuant <= abs($stockFifo)) {
                if(is_array($r)){
                    $r['stock_fifo'] += $totalQuant;
                }else{
                    $r->stock_fifo += $totalQuant;
                }
                $totalQuant=0;
            }else{
                if(is_array($r)){
                     $totalQuant+=$r['stock_fifo'];
                     $r['stock_fifo']=0;
                }else{
                    $totalQuant+=$r->stock_fifo;
                    $r->stock_fifo=0;
                }
            }
            $data[$index] = $r;
        }
        return ['totalQuant'=>$totalQuant,'data'=>$data];
    }


    public function consumeStockAdjustmentOut($data,$row){
        $updatedDataOut= $data->where('stock_fifo', '<' , 0)->values()->all();
        $totalQuant = fast_get($row,'outwards_qty',0);
        foreach ($updatedDataOut as $r) {
            if ($totalQuant <= 0) break;
            $index = $data->search($r);
            $stockFifo = fast_get($r,'stock_fifo',0);
            if ($totalQuant <= abs($stockFifo)) {
                if(is_array($r)){
                    $r['stock_fifo'] += $totalQuant;
                }else{
                    $r->stock_fifo += $totalQuant;
                }
                $totalQuant=0;
            }else{
                if(is_array($r)){
                     $totalQuant+=$r['stock_fifo'];
                     $r['stock_fifo']=0;
                }else{
                    $totalQuant+=$r->stock_fifo;
                    $r->stock_fifo=0;
                }
            }
            $data[$index] = $r;
        }
        return ['totalQuant'=>$totalQuant,'data'=>$data];
    }


    public function lastCurrentData($stockItemId,$tran_id){
        return  DB::table('stock')
                            ->where('stock_item_id','=',$stockItemId)
                            ->where('current_rate','>',0)
                            ->where('tran_id','<',$tran_id)
                            ->where('inwards_id','>',0)
                            ->orderBy('tran_date','DESC')
                            ->orderBy('tran_id','DESC')
                            ->orderBy('id','DESC')
                            ->first();
    }


    public function StockInInsert($stockItemId, $godownId,$tranId){
        $stock_data=DB::table('stock')
            ->where(function($query) {
                $query->where('stock_fifo', '>', 0)
                    ->orWhere('stock_fifo', '<', 0);
            })
            ->where('stock_item_id', '=', $stockItemId)
            ->where('godown_id', '=', $godownId)
            ->where('tran_id', '!=', $tranId)
            ->orderBy('tran_date','asc')
            ->orderBy('tran_id','asc')
            ->orderBy('id','asc')
            ->get();
        $data = collect($stock_data);
        $stock_in_data=DB::table('stock')
            ->where('stock_item_id', '=', $stockItemId)
            ->where('godown_id', '=', $godownId)
            ->where('tran_id', '=', $tranId)
            ->orderBy('id','asc')
            ->get();
        foreach ($stock_in_data as $key=>$row) {
            $updatedData= $row;
            if($row->inwards_qty<0){
                $consumeStockMinus=$this->consumeStockMinus($data,$row);
                $total_quant=$consumeStockMinus['totalQuant'];
                $data =$consumeStockMinus['data'];
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
                    $consumeStockAdjustment=$this->consumeStockAdjustment($data,$row);
                    $total_quant=$consumeStockAdjustment['totalQuant'];
                    $data =$consumeStockAdjustment['data'];
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
                    if(!is_null($lastCurrentData) && fast_get($lastCurrentData, 'current_qty', 0)<0){
                        if(abs(fast_get($lastCurrentData, 'current_qty', 0))>=$row->inwards_qty){
                            $updatedData->stock_fifo=0;
                            }else{
                            $updatedData->stock_fifo=fast_get($lastCurrentData, 'current_qty', 0)+$row->inwards_qty;
                            }
                        $updatedData->current_qty=fast_get($lastCurrentData, 'current_qty', 0)+$row->inwards_qty;
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
            $data[]=$updatedData;
        }
        collect($data)
        ->values()
        ->chunk(1000)
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
    }

    public function StockOutInsert($stockItemId, $godownId,$tranId){
        $stock_data=DB::table('stock')
            ->where(function($query) {
                $query->where('stock_fifo', '>', 0)
                    ->orWhere('stock_fifo', '<', 0);
            })
            ->where('stock_item_id', '=', $stockItemId)
            ->where('godown_id', '=', $godownId)
            ->where('tran_id', '!=', $tranId)
            ->orderBy('tran_date','asc')
            ->orderBy('tran_id','asc')
            ->orderBy('id','asc')
            ->get();
        $data = collect($stock_data);
        $stock_out_data=DB::table('stock')
            ->where('stock_item_id', '=', $stockItemId)
            ->where('godown_id', '=', $godownId)
            ->where('tran_id', '=', $tranId)
            ->orderBy('id','asc')
            ->get();
        foreach ($stock_out_data as $key=>$row) {
            $updatedData= $row;
            if($row->outwards_qty<0){
                $filtered = $this->currentStocks($data,$row);
                $lastQty = $filtered->sum('stock_fifo');
                $updatedData->stock_fifo=-$row->outwards_qty;
                if($lastQty > 0){
                    $updatedData->current_qty=$lastQty+(-$row->outwards_qty);
                    $lastValue = $this->lastStockValue($filtered);
                    $updatedData->current_rate =  (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
                }else if($lastQty < 0){
                    $consumeStockAdjustmentOut=$this->consumeStockAdjustmentOut($data,$row);
                    $total_quant=$consumeStockAdjustmentOut['totalQuant'];
                    $data =$consumeStockAdjustmentOut['data'];
                    $updatedData->stock_fifo=$total_quant;
                    $updatedData->current_qty=$total_quant;
                    $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                    if(!is_null($lastCurrentData)){
                        $updatedData->current_rate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                    }else{
                        $lastCurrentData =lastCurrentData($stockItemId,$row->tran_id);
                        if(!is_null($lastCurrentData)){
                            $updatedData->current_rate=$lastCurrentData->current_rate;
                        }else{
                            $updatedData->current_rate=0;
                        }
                    }
                }else{
                    $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();

                    if(!is_null($lastCurrentData) && fast_get($lastCurrentData, 'current_rate', 0)>0){
                        $updatedData->stock_fifo=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                        $updatedData->current_qty=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                        $updatedData->current_rate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                    }else{
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                        if(!is_null($lastCurrentData)){
                            $updatedData->current_rate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                            $updatedData->stock_fifo=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                            $updatedData->current_qty=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                        }else{
                            $updatedData->current_rate=!empty($row->outwards_qty) ? ($row->outwards_value / $row->outwards_qty) : 0;
                            $updatedData->stock_fifo=-$row->outwards_qty;
                            $updatedData->current_qty=-$row->outwards_qty;

                        }
                    }
                }
            }else{
                $consumeStock=$this->consumeStock($data,$row);
                $total_quant=$consumeStock['totalQuant'];
                $data =$consumeStock['data'];
                $filtered = $this->currentStocks($data,$row);
                $lastValue = $this->lastStockValue($filtered);
                if($total_quant > 0){
                    // ->where('inwards_id','>',0)
                    $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
                    $lastQty=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                    if(!is_null($lastCurrentData) && fast_get($lastCurrentData, 'current_rate', 0)>0){
                        $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                    }else{
                        // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                        if(!is_null($lastCurrentData)){
                            $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                        }else{
                            $lastCurrentData = lastCurrentData($stockItemId,$row->tran_id);
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
                            $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                        }else{
                            $lastCurrentData = lastCurrentData($stockItemId,$row->tran_id);
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
                            $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                        }else{
                            $lastCurrentData = lastCurrentData($stockItemId,$row->tran_id);
                            if(!is_null($lastCurrentData)){
                                $currentRate=$lastCurrentData->current_rate;
                            }else{
                                $currentRate=0;
                            }
                        }
                    }
                }
                if(!$filtered->isEmpty()){
                    $updatedData->current_qty=$lastQty;
                    $updatedData->current_rate=$currentRate;

                    if($total_quant < 0){
                    }
                }else{
                    $updatedData->current_qty=-$row->outwards_qty;
                    $updatedData->current_rate=$currentRate;
                }
                $updatedData->stock_fifo=0;
            }
            $data[]=$updatedData;
        }

        collect($data)
        ->values()
        ->chunk(1000)
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
        $data=[];
    }


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
                $sameTranDate = $value->tran_date == $old['tran_date']; // add this line
                if ($sameItem && $sameGodown && $sameQty && $sameTotal && $sameTranDate) {
                    continue; // No change
                }

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
                $sameTranDate = $value->tran_date == $old['tran_date']; // add this line
                if ($sameItem && $sameGodown && $sameQty && $sameTotal && $sameTranDate) {
                    continue; // No change
                }

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

    public function stockReCalculation($stock_item_id,$godown_id){

        $stock_data=DB::table('stock')
        ->where('stock_item_id','=',$stock_item_id)
        ->where('godown_id','=',$godown_id)
        ->orderBy('tran_date','asc')
        ->orderBy('tran_id','asc')
        ->orderBy('id','asc')
        ->get();
        $data = collect([]);
        $StockOutData = collect([]);
        foreach ($stock_data as $key=>$row) {
            $updatedData= $row;
            if($row->inwards_id){
                if($row->inwards_qty<0){
                    $consumeStockMinus=$this->consumeStockMinus($data,$row);
                    $total_quant=$consumeStockMinus['totalQuant'];
                    $data =$consumeStockMinus['data'];
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
                        $consumeStockAdjustment=$this->consumeStockAdjustment($data,$row);
                        $total_quant=$consumeStockAdjustment['totalQuant'];
                        $data =$consumeStockAdjustment['data'];
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
                        if(!is_null($lastCurrentData) && fast_get($lastCurrentData, 'current_qty', 0)<0){
                            if(abs(fast_get($lastCurrentData, 'current_qty', 0))>=$row->inwards_qty){
                                $updatedData->stock_fifo=0;
                              }else{
                                $updatedData->stock_fifo=fast_get($lastCurrentData, 'current_qty', 0)+$row->inwards_qty;
                              }
                            $updatedData->current_qty=fast_get($lastCurrentData, 'current_qty', 0)+$row->inwards_qty;
                            $updatedData->current_rate = !empty($row->inwards_qty) ? ($row->inwards_value / $row->inwards_qty) : 0;
                        }else{
                            // $lastValue = $this->lastStockValue($filtered);
                            $updatedData->stock_fifo=$row->inwards_qty;
                            $updatedData->current_rate = (isset($row->inwards_qty) && ($row->inwards_qty)>0 ) ? (($row->inwards_value) / ($row->inwards_qty)) : 0;
                            $updatedData->current_qty = $row->inwards_qty;
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
                    $filtered = $this->currentStocks($data,$row);
                    $lastQty = $filtered->sum('stock_fifo');
                    $updatedData->stock_fifo=-$row->outwards_qty;
                    if($lastQty > 0){
                        $updatedData->current_qty=$lastQty+(-$row->outwards_qty);
                        $lastValue = $this->lastStockValue($filtered);
                        $updatedData->current_rate =  (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
                    }else if($lastQty < 0){
                        $consumeStockAdjustmentOut=$this->consumeStockAdjustmentOut($data,$row);
                        $total_quant=$consumeStockAdjustmentOut['totalQuant'];
                        $data =$consumeStockAdjustmentOut['data'];
                        $updatedData->stock_fifo=$total_quant;
                        $updatedData->current_qty=$total_quant;
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                        if(!is_null($lastCurrentData)){
                            $updatedData->current_rate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                        }else{
                            $lastCurrentData = $this->lastCurrentData($stock_item_id,$row->tran_id);
                            if(!is_null($lastCurrentData)){
                                $updatedData->current_rate=$lastCurrentData->current_rate;
                            }else{
                                $updatedData->current_rate=0;
                            }
                        }
                    }else{
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
                        if(!is_null($lastCurrentData) && fast_get($lastCurrentData, 'current_rate', 0)>0){
                            $updatedData->stock_fifo=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                            $updatedData->current_qty=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                            $updatedData->current_rate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                        }else{
                            $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                            if(!is_null($lastCurrentData)){
                                $updatedData->current_rate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                                $updatedData->stock_fifo=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                                $updatedData->current_qty=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                            }else{
                                $updatedData->current_rate=!empty($row->outwards_qty) ? ($row->outwards_value / $row->outwards_qty) : 0;
                                $updatedData->stock_fifo=-$row->outwards_qty;
                                $updatedData->current_qty=-$row->outwards_qty;

                            }
                        }
                    }

                }else{
                    $consumeStock=$this->consumeStock($data,$row);
                    $total_quant=$consumeStock['totalQuant'];
                    $data =$consumeStock['data'];
                    $filtered = $this->currentStocks($data,$row);
                    $lastValue = $this->lastStockValue($filtered);
                    if($total_quant > 0){
                        // ->where('inwards_id','>',0)
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->last();
                        $lastQty=(fast_get($lastCurrentData, 'current_qty', 0)??0)-$row->outwards_qty;
                        if(!is_null($lastCurrentData) && fast_get($lastCurrentData, 'current_rate', 0)>0){
                            $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                        }else{
                            // $currentRate=($lastCurrentData['inwards_qty']>0)?($lastCurrentData['inwards_value']/$lastCurrentData['inwards_qty']):0;
                            $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                            if(!is_null($lastCurrentData)){
                                $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                            }else{
                                $lastCurrentData = $this->lastCurrentData($stock_item_id,$row->tran_id);
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
                                $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                            }else{
                                $lastCurrentData = $this->lastCurrentData($stock_item_id,$row->tran_id);
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
                                $currentRate=fast_get($lastCurrentData, 'current_rate', 0)??0;
                            }else{
                                $lastCurrentData = $this->lastCurrentData($stock_item_id,$row->tran_id);
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
                            // $updatedData->stock_fifo=-$row->outwards_qty;
                        }
                    }else{
                        // if($lastQty!=-$row->outwards_qty || $updatedData->current_rate!=0){
                        //     $updatedData->change=1;
                        // }
                        $updatedData->current_qty=-$row->outwards_qty;
                        // $updatedData->current_rate=0;
                        $updatedData->current_rate=$currentRate;
                    }
                    $updatedData->stock_fifo=0;
                }
            }
            if($updatedData->stock_fifo>0 || $updatedData->stock_fifo<0){
                $data[]=$updatedData;
            }else{
                 $StockOutData[]=$updatedData;
            }
            
        }
        // if(!empty($data)){
            collect($data)
            ->values()
            ->chunk(1000)
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
            $data=[];
            collect($StockOutData)
            ->values()
            ->chunk(1000)
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
            $StockOutData=[];
        // }
    }
}
