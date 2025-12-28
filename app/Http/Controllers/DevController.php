<?php

namespace App\Http\Controllers;

use App\Models\DebitCredit;
use App\Models\StockIn;
use App\Repositories\Backend\Master\GroupChartRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DevController extends Controller
{
    public function __construct()
    {

    }

    public function stockInRate(Request $request){
        $data=[];
        if($request->isMethod('post')) {
            $StockItemId = $request->input('StockItemId');
            $data = StockIn::where('stock_item_id', $StockItemId)->get();
        }
        return view('dev.stockInRate',compact('data'));
    }

    public function stockInRateStore(Request $request){
        $stock_in_id = $request->stock_in_id;
        $rate = $request->rate;
        $total = $request->total;
        $data = StockIn::where('stock_in_id', $stock_in_id)->update([
            'rate' => $rate,
            'total'=> $total
        ]);
        return response()->json($data);
    }

    public function stockCalculation(){
        // $data=DB::table('stock_item')->get();

        // foreach ($data as $key => $value) {
        //         DB::select('CALL ProductWiseUpdateStock(?, ?, ?)', [
        //             $value->stock_item_id,
        //            0,
        //            0,
        //         ]);

        // }
        dd('ok');
    }


    public function stockCalculationFIFO(Request $request){
        // $start_date=date('Y-m-d');
        // $end_date=date('Y-m-d');
        // if($request->isMethod('post')) {
        //     $start_date=$request->start_date;
        //     $end_date=$request->end_date;
        //     $data=DB::table('transaction_master')->whereBetween('transaction_date', [$start_date, $end_date])->orderBy('transaction_date','ASC')->orderBy('tran_id','ASC')->get();
           
        //     foreach ($data as $key => $value) {
        //         $stock_out = DB::select('SELECT stock_out.tran_id,stock_out.stock_item_id,stock_out.qty,stock_out.rate,
        //                                 stock_out.total,stock_out.godown_id,stock_out.stock_out_id,stock_out.tran_date
        //                                 FROM `stock_out`
        //                                WHERE stock_out.tran_id='.$value->tran_id.' ORDER BY stock_out.tran_id');
                                     
        //         if(!empty($stock_out)){
        //             foreach ($stock_out as $key => $value_1) {
        //                 //(IN `p_stock_item_id` INT,
        //                 //IN `p_godown_id` INT,
        //                 //IN `p_stock_out_id` INT,
        //                 // IN `p_qty` DOUBLE,
        //                 //IN `p_total` DOUBLE,
        //                 //IN `p_tran_id` INT,
        //                 // IN `p_tran_date` DATE)
        //                 DB::select('CALL StockOutInsertProcedure(?, ?, ?, ?, ?, ?, ?)', [
        //                     $value_1->stock_item_id,
        //                    $value_1->godown_id,
        //                    $value_1->stock_out_id,
        //                    $value_1->qty,
        //                    $value_1->total,
        //                    $value_1->tran_id,
        //                    $value_1->tran_date,
        //                 ]);
        //             }
        //         }

        //         $stock_in=DB::select('SELECT stock_in.tran_id,stock_in.stock_item_id,stock_in.qty,stock_in.rate,
        //                                 stock_in.total,stock_in.godown_id,stock_in.stock_in_id,stock_in.tran_date,stock_in.status
        //                                 FROM `stock_in`
        //                                WHERE stock_in.tran_id='.$value->tran_id.' ORDER BY stock_in.tran_id');
        //         if(!empty($stock_in)){
        //             // `StockInInsetProcedure`
        //             // (IN `p_stock_item_id` INT,
        //             // IN `p_godown_id` INT,
        //             //  IN `p_stock_in_id` INT,
        //             //   IN `p_qty` DOUBLE,
        //             //    IN `p_total` DOUBLE, IN
        //             //    `p_status` INT,
        //             //    IN `p_tran_id` INT,
        //             //    IN `p_tran_date` DATE)
        //             foreach ($stock_in as $key => $value_1) {
        //                 DB::select('CALL StockInInsetProcedure(?, ?, ?, ?, ?, ?, ?, ?)', [
        //                     $value_1->stock_item_id,
        //                    $value_1->godown_id,
        //                    $value_1->stock_in_id,
        //                    $value_1->qty,
        //                    $value_1->total,
        //                    $value_1->status,
        //                    $value_1->tran_id,
        //                    $value_1->tran_date
        //                 ]);
        //             }
        //         }


        //     }
        // }
        // return view('dev.stockCalculationFIFO',compact('start_date','end_date'));
    }

    public function stockCalculationUpdate(Request $request){
        $max_id=DB::table('stock_item')->max('stock_item_id');
        $start_id=0;
        $end_id=0;
        $godown=DB::table('godowns')->get();
        if($request->isMethod('post')) {
            $start_id=$request->start_id;
            $end_id=$request->end_id;
            for($i=$start_id;$i<=$end_id;$i++){
                foreach($godown as $g){
                    $this->stockReCalculation($i,$g->godown_id);
                }            
            }
    
        }
        return view('dev.stockCalculationUpdate',compact('max_id','start_id','end_id'));
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

    public function consumeStock($data,$row,$storage){
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
            Redis::set($storage, json_encode($data));
        }
        return $total_quant;
    }

    public function consumeStockMinus($data,$row,$storage){
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
            Redis::set($storage, json_encode($data));
        }
        return $total_quant;
    }


    public function consumeStockAdjustment($data,$row,$storage){
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
            Redis::set($storage, json_encode($data));
        }
        return $total_quant;
    }
    public function consumeStockAdjustmentOut($data,$row,$storage){
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
            Redis::set($storage, json_encode($data));
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
        $storage='stock_update';
        Redis::del($storage);
        Redis::set($storage, json_encode([]));
        // $data = collect(json_decode(Redis::get('stock'), true));
        $data = collect([]);
        foreach ($stock_data as $key=>$row) {
            $updatedData= $row;
            if($row->inwards_id){               
                if($row->inwards_qty<0){
                    $total_quant=$this->consumeStockMinus($data,$row,$storage);
                    $data = collect(json_decode(Redis::get($storage), true));
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
                       $total_quant=$this->consumeStockAdjustment($data,$row,$storage);
                       $data = collect(json_decode(Redis::get($storage), true));
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
                    $data = collect(json_decode(Redis::get($storage), true));
                    
                    $filtered = $this->currentStocks($data,$row);
                    $lastQty = $filtered->sum('stock_fifo');
                    $updatedData->stock_fifo=-$row->outwards_qty;
                    if($lastQty > 0){
                        $updatedData->current_qty=$lastQty+(-$row->outwards_qty);
                        $lastValue = $this->lastStockValue($filtered);
                        $updatedData->current_rate =  (($lastQty) > 0) ? ($lastValue / $lastQty) : 0;
                    }else if($lastQty < 0){
                        $total_quant=$this->consumeStockAdjustmentOut($data,$row,$storage);
                        $updatedData->stock_fifo=$total_quant;
                        $updatedData->current_qty=$total_quant;
                        $lastCurrentData = $data->where('tran_date','<=',$row->tran_date)->where('current_rate','>',0)->last();
                        if(!is_null($lastCurrentData)){
                            $updatedData->current_rate=$lastCurrentData['current_rate']??0;
                        }else{
                            $lastCurrentData = DB::table('stock')
                                ->where('stock_item_id','=',$stock_item_id)
                                ->where('current_rate','>',0)
                                ->where('tran_id','<',$row->tran_id)
                                ->where('inwards_id','>',0)
                                // ->where('id','<',$row->id)
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
                    $total_quant=$this->consumeStock($data,$row,$storage);

                    $data = collect(json_decode(Redis::get($storage), true));
    
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
                                ->where('current_rate','>',0)
                                ->where('tran_id','<',$row->tran_id)
                                ->where('inwards_id','>',0)
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
                                ->where('current_rate','>',0)
                                ->where('tran_id','<',$row->tran_id)
                                ->where('inwards_id','>',0)
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
                                ->where('current_rate','>',0)
                                ->where('tran_id','<',$row->tran_id)
                                ->where('inwards_id','>',0)
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
            Redis::set($storage, json_encode($data));
            $data = collect(json_decode(Redis::get($storage), true));
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
            Redis::del($storage);
        // }
    }


    public function salesReturnTransfer(Request $request)
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $outData=null;
        if ($request->isMethod('post')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            // Fetch data to be inserted
            $outData = DB::table('transaction_master as tm')
                ->join('voucher_setup as vs', 'vs.voucher_id', '=', 'tm.voucher_id')
                ->join('stock_out as sto', 'sto.tran_id', '=', 'tm.tran_id')
                ->where('vs.voucher_type_id', 25)
                ->whereBetween('tm.transaction_date', [$start_date, $end_date])
                ->orderBy('tm.transaction_date', 'asc')
                ->orderBy('tm.tran_id', 'asc')
                ->orderBy('sto.stock_out_id', 'asc')
                ->selectRaw('
                    sto.tran_id,
                    sto.stock_item_id,
                    sto.godown_id,
                    ABS(sto.qty) as qty,
                    ABS(sto.rate) as rate,
                    sto.disc,
                    ABS(sto.total) as total,
                    sto.tran_date,
                    sto.customer_id,
                    sto.debit_credit_id,
                    sto.remark
                ')
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->toArray();

            // Insert in chunks of 100 (you can adjust this number as needed)
            foreach (array_chunk($outData, 100) as $chunk) {
                StockIn::insert($chunk);
            }

            // Get IDs for deletion
            $stoToDelete = DB::table('transaction_master as tm')
                ->join('voucher_setup as vs', 'vs.voucher_id', '=', 'tm.voucher_id')
                ->join('stock_out as sto', 'sto.tran_id', '=', 'tm.tran_id')
                ->where('vs.voucher_type_id', 25)
                ->whereBetween('tm.transaction_date', [$start_date, $end_date])
                ->pluck('sto.stock_out_id');

            DB::table('stock_out')
                ->whereIn('stock_out_id', $stoToDelete)
                ->delete();
        }

        return view('dev.salesReturnTransfer', compact('start_date', 'end_date','outData'));
    }

}
