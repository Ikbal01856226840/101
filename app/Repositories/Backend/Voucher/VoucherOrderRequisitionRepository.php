<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\OrderRequisitionTransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderRequisition;

class VoucherOrderRequisitionRepository implements VoucherOrderRequisitionInterface
{

    public function storeOrderRequisition($request, $voucher_invoice)
    {
          //order requisition transaction_master
            $ip = $_SERVER['REMOTE_ADDR'];
            $data = new OrderRequisitionTransactionMaster();
            if (! empty($voucher_invoice)) {
                $data->invoice_no = $voucher_invoice;
            } else {
                $data->invoice_no = $request->invoice_no;
            }
            $data->reference_no = $request->reference_no;
            $data->date = $request->date;
            $data->user_id = auth()->id();
            $data->ledger_id=$request->ledger_id;
            $data->narration=$request->narration;
            $data->voucher_id = $request->voucher_id;
            $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
            $data->save();

            //multiple order requisition data
            $stock_in_data = [];
            for ($i = 0; $i < count($request->product_id); $i++) {
                if (! empty($request->product_id[$i])) {
                    $stock_in_data[] = [
                        'order_tran_id' =>(int) $data->id ?? exit,
                        'stock_item_id' =>(int) $request->product_id[$i],
                        'qty' => (float) $request->qty[$i] ?? 0,
                        'rate' => (float) $request->rate[$i],
                        'total' => (float) $request->amount[$i],
                        'remark' => $request->remark[$i]??'',
                        'brand' => $request->brand[$i]??'',
                        'order_no' => $request->order_no[$i]??'',
                        'measurement' => $request->measurement[$i]??'',
                    ];
                }
            }
            OrderRequisition::insert($stock_in_data);
          return  $data;

    }
    public function getOrderRequisitionId($id)
    {

        return OrderRequisitionTransactionMaster::findOrFail($id);
    }

    public function updateOrderRequisition(Request $request, $id, $voucher_invoice)
    {
        //  dd($request->all());
            $ip = $_SERVER['REMOTE_ADDR'];
            $data = OrderRequisitionTransactionMaster::findOrFail($id);
            if (! empty($voucher_invoice)) {
                $data->invoice_no = $voucher_invoice;
            } else {
                $data->invoice_no = $request->invoice_no;
            }
            $data->reference_no = $request->reference_no;
            $data->date = $request->date;
            $data->user_id = auth()->id();
            $data->ledger_id=$request->ledger_id;
            $data->narration=$request->narration;
            $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
            $data->save();


            // stock in insert or update
        if (!empty($request->product_id)) {
            for ($i = 0; $i < count($request->product_id); $i++) {

                if (!empty($request->id[$i])) {

                    OrderRequisition::where('id', $request->id[$i])->update([
                        'stock_item_id' =>(int) $request->product_id[$i],
                        'qty' => (float) $request->qty[$i] ?? 0,
                        'rate' => (float) $request->rate[$i],
                        'total' => (float) $request->amount[$i],
                        'remark' => $request->remark[$i]??'',
                        'brand' => $request->brand[$i]??'',
                        'order_no' => $request->order_no[$i]??'',
                        'measurement' => $request->measurement[$i]??'',
                    ]);

                } else {
                    if (!empty($request->product_id[$i])) {
                        $stock_in_data = new OrderRequisition();
                        $stock_in_data->order_tran_id=(int) $id ?? exit;
                        $stock_in_data->stock_item_id=(int) $request->product_id[$i];
                        $stock_in_data->qty= (float) $request->qty[$i] ?? 0;
                        $stock_in_data->rate= (float) $request->rate[$i];
                        $stock_in_data->total = (float) $request->amount[$i];
                        $stock_in_data->remark= $request->remark[$i]??'';
                        $stock_in_data->brand = $request->brand[$i]??'';
                        $stock_in_data->order_no = $request->order_no[$i]??'';
                        $stock_in_data->measurement = $request->measurement[$i]??'';
                        //dd($stock_in_data);
                        $stock_in_data->save();
                    }
                }
            }


        }
            //multiple or single debit credit delete
            if (! empty($request->delete_id)) {
                $delete_debit_credit = explode(',', $request->delete_id);
                for ($i = 0; $i < count($delete_debit_credit); $i++) {
                    if (!empty($delete_debit_credit[$i])) {
                        OrderRequisition::find($delete_debit_credit[$i])->delete();
                    }
                }
            }
            return $data;

    }

    public function deleteOrderRequisition($id)
    {
            OrderRequisition::where('order_tran_id', $id)->delete();
            return OrderRequisitionTransactionMaster::findOrFail($id)->delete();
    }

    public function PurchaseOrderData($id){
        return DB::table('order_requisition')
        ->select('order_requisition.brand','order_requisition.order_no','order_requisition.measurement','stock_item.product_name','unitsof_measure.symbol', 'unitsof_measure.unit_of_measure_id','order_requisition.remark', 'order_requisition.order_tran_id',  'order_requisition.stock_item_id', 'order_requisition.qty', 'order_requisition.rate', 'order_requisition.total','id')
        ->leftJoin('stock_item', 'order_requisition.stock_item_id', '=', 'stock_item.stock_item_id')
        ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
        ->where('order_requisition.order_tran_id', $id)
        ->get();
    }


}
