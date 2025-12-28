<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\SalesOrder;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherSalesOrderRepository implements VoucherSalesOrderInterface
{


    public function StoreVoucherSalesOrder(Request $request, $voucher_invoice)
    {

        $ip = $_SERVER['REMOTE_ADDR'];
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
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->user_name = Auth::user()->user_name;
        $data->secret_narration = $request->secret_narration??'';
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        // multiple sales order data
        $sales_order_data = [];
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (!empty($request->product_id[$i])) {
                $sales_order_data[] = [
                    'tran_id' => $data->tran_id ?? exit,
                    'stock_item_id' => $request->product_id[$i],
                    'godown_id' => $request->godown_id[$i] ?? 0,
                    'qty' => (float) $request->qty[$i] ?? 0,
                    'rate' => (float) $request->rate[$i] ?? 0,
                    'total' => (float) $request->amount[$i] ?? 0,
                    'credit_ledger_id' =>$request->credit_ledger_id ?? 0,
                    'debit_ledger_id'=>$request->debit_ledger_id[$i] ?? 0,
                    'comission_type'=>$request->product_wise_commission_cal[$i] ?? 0,
                    'commission' => $request->product_wise_commission_amount[$i] ?? 0,
                    'commission_amount'=> $request->product_wise_get_commission[$i] ?? '',
                ];
            }
        }
        SalesOrder::insert($sales_order_data);
        return $data;
    }

    public function getVoucherSalesOrderId($id)
    {
        return DB::table('transaction_master')
                 ->select('transaction_master.*','ledger_head.ledger_head_id','ledger_head.ledger_name')
                 ->leftJoin('sales_order', 'transaction_master.tran_id', '=', 'sales_order.tran_id')
                 ->leftJoin('ledger_head', 'sales_order.credit_ledger_id', '=', 'ledger_head.ledger_head_id')
                 ->where('transaction_master.tran_id',$id)
                 ->first();
    }

    public function updateVoucherSalesOrder(Request $request, $id, $voucher_invoice)
    {
          
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = TransactionMaster::findOrFail($id);;
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
        $data->secret_narration = $request->secret_narration??'';
        $data->user_id = auth()->id();
        $data->entry_date = date('Y-m-d');
        $data->tran_time = date('H:i:s');
        $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
        $data->save();

        // multiple stock out data
          $sales_order_data_update = [];
        for ($i = 0; $i < count($request->product_id); $i++) {
            if (!empty($request->product_id[$i])) {
                if (!empty($request->sales_order_id[$i])) {
                  SalesOrder::where('id', $request->sales_order_id[$i])->update([
                                    'stock_item_id' => $request->product_id[$i],
                                    'godown_id' => $request->godown_id[$i] ?? 0,
                                    'qty' => (float) $request->qty[$i] ?? 0,
                                    'rate' => (float) $request->rate[$i] ?? 0,
                                    'total' => (float) $request->amount[$i] ?? 0,
                                    'credit_ledger_id' =>$request->credit_ledger_id ?? 0,
                                    'debit_ledger_id'=>$request->debit_ledger_id[$i] ?? 0,
                                    'comission_type'=>$request->product_wise_commission_cal[$i] ?? 0,
                                    'commission' => $request->product_wise_commission_amount[$i] ?? 0,
                                    'commission_amount'=> $request->product_wise_get_commission[$i] ?? '',
                    ]);
                } else {
                $sales_order_data_update[] = [
                                    'tran_id' => $data->tran_id ?? exit,
                                    'stock_item_id' => $request->product_id[$i],
                                    'godown_id' => $request->godown_id[$i] ?? 0,
                                    'qty' => (float) $request->qty[$i] ?? 0,
                                    'rate' => (float) $request->rate[$i] ?? 0,
                                    'total' => (float) $request->amount[$i] ?? 0,
                                    'credit_ledger_id' =>$request->credit_ledger_id ?? 0,
                                    'debit_ledger_id'=>$request->debit_ledger_id[$i] ?? 0,
                                    'comission_type'=>$request->product_wise_commission_cal[$i] ?? 0,
                                    'commission' => $request->product_wise_commission_amount[$i] ?? 0,
                                    'commission_amount'=> $request->product_wise_get_commission[$i] ?? '',
                    ];
                }
            }
        }
        SalesOrder::insert($sales_order_data_update);

        //single or multiple  delete
        if (!empty($request->delete_sales_order_id)) {
            $delete_sales_order = explode(',', $request->delete_sales_order_id);
            for ($i = 0; $i < count(array_filter($delete_sales_order)); $i++) {
                SalesOrder::find($delete_sales_order[$i])->delete();
            }
        }


        return $data;
    }

    public function deleteVoucherSalesOrder($id)
    {
        SalesOrder::where('tran_id', $id)->delete();
        return  TransactionMaster::findOrFail($id)->delete();

    }
    public function getSalesOrderData($id)
    {

        return DB::table('sales_order')
        ->select('sales_order.*','godowns.godown_name','stock_item.product_name','unitsof_measure.unit_of_measure_id','unitsof_measure.symbol',
          DB::raw('(SELECT SUM(st_in.qty)  FROM stock_in as st_in
                        WHERE st_in.stock_item_id=sales_order.stock_item_id AND st_in.godown_id=sales_order.godown_id  GROUP BY st_in.stock_item_id )as stock_in_sum'),
           DB::raw('(SELECT SUM(st_out.qty)  FROM stock_out as st_out
                    WHERE st_out.stock_item_id=sales_order.stock_item_id AND sales_order.godown_id=st_out.godown_id  GROUP BY st_out.stock_item_id )as stock_out_sum')
        )
        ->leftJoin('stock_item', 'sales_order.stock_item_id', '=', 'stock_item.stock_item_id')
        ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
         ->leftJoin('godowns', 'sales_order.godown_id', '=', 'godowns.godown_id')
        ->where('sales_order.tran_id',$id)
        ->get();

    }

    public function statusUpadteSalesOrder($id)
    {
        return SalesOrder::where('tran_id', $id)->update([
               'status' => 1,
          ]);

    }


}
