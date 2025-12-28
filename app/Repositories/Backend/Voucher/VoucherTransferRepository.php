<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GoodsInTransit;
use App\Models\Voucher;
use App\Models\OrderDelivery;

class VoucherTransferRepository implements VoucherTransferInterface
{

    public function storeTransfer(Request $request, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        //tran master
        $data = new TransactionMaster();
        if (! empty($voucher_invoice)) {
            $data->invoice_no = $voucher_invoice;
        } else {
            $data->invoice_no = $request->invoice_no;
        }
        $data->ref_no = $request->ref_no;
        $data->transaction_date = $request->invoice_date;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->voucher_id = $request->voucher_id;
        $data->narration = $request->narration;
        $data->customer_id = $request->customer_id ?? 0;
        $data->dis_cen_id = $request->dis_cen_id ?? 0;
        $data->secret_narration = $request->secret_narration??'';
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

         // multiple stock out data
         $stock_out_data = [];
         for ($i = 0; $i < count($request->product_id); $i++) {
             if (! empty($request->product_id[$i])) {
                 $stock_out_data[] = [
                     'tran_id' => $data->tran_id ?? exit,
                     'tran_date' => $request->invoice_date,
                     'stock_item_id' => $request->product_id[$i],
                     'godown_id' => $request->godown_id[$i] ?? 0,
                     'qty' => (float) $request->qty[$i] ?? 0,
                     'rate' => (float) $request->rate[$i] ?? 0,
                     'total' => (float) $request->amount[$i] ?? 0,
                     'remark' => $request->remark[$i] ?? '',
                 ];
             }
         }

         $stockout= StockOut::insert($stock_out_data);
         $voucher = Voucher::findOrFail($request->voucher_id);
         if ($voucher->st_approval) {
             $GoodsInTransitData = new GoodsInTransit();
             $GoodsInTransitData->tran_id = $data->tran_id;
             $GoodsInTransitData->status = 0;
             $GoodsInTransitData->to_godown = $request->godown_id_in ?? 0;
             $GoodsInTransitData->save();

         } else {
        // multiple stock in data
                $stock_in_data = [];
                for ($i = 0; $i < count($request->product_id); $i++) {
                    if (! empty($request->product_id[$i])) {
                        $stock_in_data[] = [
                            'tran_id' => $data->tran_id ?? exit,
                            'tran_date' => $request->invoice_date,
                            'stock_item_id' => $request->product_id[$i],
                            'godown_id' => $request->godown_id_in ?? 0,
                            'qty' => (float) $request->qty[$i] ?? 0,
                            'rate' => (float) $request->rate[$i] ?? 0,
                            'total' => (float) $request->amount[$i] ?? 0,
                            'remark' => $request->remark[$i] ?? '',
                        ];
                    }
                }
                StockIn::insert($stock_in_data);

         }

        return $stockout;

    }

    public function getTransferId($id)
    {
        return TransactionMaster::findOrFail($id);
    }

    public function updateTransfer(Request $request, $id, $voucher_invoice)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = TransactionMaster::findOrFail($id);
        $update_history = json_decode($data->other_details);
        $data->invoice_no = $request->invoice_no;
        $data->ref_no = $request->ref_no;
        $data->transaction_date = $request->invoice_date;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->voucher_id = $request->voucher_id;
        $data->narration = $request->narration;
        $data->customer_id = $request->customer_id ?? 0;
        $data->dis_cen_id = $request->dis_cen_id ?? 0;
        $data->secret_narration = $request->secret_narration??'';
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

          // multiple stock out data
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (! empty($request->product_id[$i])) {
                if (! empty($request->stock_out_id[$i])) {
                    StockOut::where('stock_out_id', $request->stock_out_id[$i])->update([
                        'tran_date' => $request->invoice_date,
                        'godown_id' => $request->godown_id[$i],
                        'stock_item_id' => $request->product_id[$i],
                        'qty' => (float) $request->qty[$i] ?? 0,
                        'rate' => (float) $request->rate[$i] ?? 0,
                        'total' => (float) $request->amount[$i] ?? 0,
                        'remark' => $request->remark[$i] ?? '',
                    ]);
                } else {
                    $stock_out_data = new StockOut();
                    $stock_out_data->tran_id = $id;
                    $stock_out_data->tran_date = $request->invoice_date;
                    $stock_out_data->stock_item_id = $request->product_id[$i];
                    $stock_out_data->godown_id = $request->godown_id[$i] ?? 0;
                    $stock_out_data->qty = $request->qty[$i] ?? 0;
                    $stock_out_data->rate = $request->rate[$i];
                    $stock_out_data->total = $request->amount[$i];
                    $stock_out_data->save();
                }
            }
        }

        $voucher = Voucher::findOrFail($request->voucher_id);
        if ($voucher->st_approval) {
            StockIn::where('tran_id', $id)->delete();
            $GoodsInTransitData = GoodsInTransit::where('tran_id', $id)->first();
            $GoodsInTransitData->status = 0;
            $GoodsInTransitData->save();

        } else {
         // multiple stock in data
            for ($i = 0; $i < count($request->product_id); $i++) {
                if (! empty($request->product_id[$i])) {
                    if (! empty($request->stock_in_id[$i])) {
                        StockIn::where('stock_in_id', $request->stock_in_id[$i])->update([
                            'tran_date' => $request->invoice_date,
                            'godown_id' => $request->godown_id_in ?? 0,
                            'stock_item_id' => $request->product_id[$i] ?? 0,
                            'qty' => (float) $request->qty[$i] ?? 0,
                            'rate' => (float) $request->rate[$i] ?? 0,
                            'total' => (float) $request->amount[$i] ?? 0,
                            'remark' => $request->remark[$i] ?? '',
                        ]);
                    } else {
                        $stock_in_data = new StockIn();
                        $stock_in_data->tran_id = $id;
                        $stock_in_data->tran_date = $request->invoice_date;
                        $stock_in_data->stock_item_id = $request->product_id[$i];
                        $stock_in_data->godown_id = $request->godown_id_in ?? 0;
                        $stock_in_data->qty = $request->qty[$i] ?? 0;
                        $stock_in_data->rate = $request->rate[$i];
                        $stock_in_data->total = $request->amount[$i];
                        $stock_in_data->remark = $request->remark[$i];
                        $stock_in_data->save();
                    }
                }
            }

        }

        if (!empty($request->delete_stock_out_id)) {
            $delete_stock_out = explode(',', $request->delete_stock_out_id);
            for ($i = 0; $i < count($delete_stock_out); $i++) {
                if(!empty($delete_stock_out[$i])){
                    StockOut::find($delete_stock_out[$i])->delete();
                }
            }
        }

        if (!empty($request->delete_stock_in_id)) {
            $delete_stock_in = explode(',', $request->delete_stock_in_id);
            for ($i = 0; $i < count($delete_stock_in); $i++) {
                if(!empty($delete_stock_in[$i])){
                    StockIn::find($delete_stock_in[$i])->delete();
                }

            }
        }

        TransactionMaster::where('tran_id', $id)->update([
            'delivery_status' =>0,
        ]);
        OrderDelivery::where('tran_id', $id)->delete();

        return $data;
    }

    public function deleteTransfer($id)
    {
        StockIn::where('tran_id', $id)->delete();
        StockOut::where('tran_id', $id)->delete();
        OrderDelivery::where('tran_id', $id)->delete();
        GoodsInTransit::where('tran_id', $id)->delete();
        return TransactionMaster::findOrFail($id)->delete();
    }

    public function receiveApproval($id)
    {
               $GoodsInTransitData = GoodsInTransit::where('tran_id', $id)->first();
               GoodsInTransit::where('tran_id', $id)->update([
                   'status' =>1,
                ]);



               $stock_in_data = [];
                foreach (StockOut::where('tran_id', $id)->get() as $request ) {
                    if (!empty($request->stock_item_id)) {
                        $stock_in_data[] = [
                            'tran_id' => $id,
                            'tran_date' => $request->tran_date,
                            'stock_item_id' => $request->stock_item_id,
                            'godown_id' => $GoodsInTransitData->to_godown ?? 0,
                            'qty' => (float) $request->qty ?? 0,
                            'rate' => (float) $request->rate ?? 0,
                            'total' => (float) $request->amount ?? 0,
                            'remark' => $request->remark ?? '',
                        ];
                    }
                }
                StockIn::insert($stock_in_data);

                TransactionMaster::where('tran_id', $id)->update([
                    'delivery_status' =>3,
                ]);
                $order_approver = new OrderDelivery();
                $order_approver->tran_id = $id;
                $order_approver->user_id = Auth::id();
                $order_approver->order_approve_status =3;
                $order_approver->delivery_date = \Carbon\Carbon::now()->format('D, d M Y g:i:s A');
                $order_approver->other_details = json_encode('Challan On: ' . \Carbon\Carbon::now()->format('D, d M Y g:i:s A') . ' By:' . Auth::user()->user_name);
                $order_approver->save();

                return $order_approver;
    }
}
