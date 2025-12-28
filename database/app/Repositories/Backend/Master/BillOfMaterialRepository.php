<?php

namespace App\Repositories\Backend\Master;

use App\Models\BillOfMaterial;
use App\Models\BillOfMaterialDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillOfMaterialRepository implements BillOfMaterialInterface
{
    public function getBillOfMaterialOfIndex()
    {

        return DB::table('bom')->orderBy('bom.bom_name', 'ASC')->get(['other_details', 'user_name', 'bom.bom_id', 'bom.bom_name', 'bom.bom_date']);
    }

    public function storeBillOfMaterial($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new BillOfMaterial();
        $data->bom_date = date('Y-m-d');
        $data->bom_name = $request->bom_name;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();
        // stock in
        for ($i = 0; $i < count($request->product_in_id); $i++) {
            if (! empty($request->product_in_id[$i])) {
                $bill_of_material_details = new BillOfMaterialDetails();
                $bill_of_material_details->bom_id = $data->bom_id;
                $bill_of_material_details->stock_item_id = $request->product_in_id[$i];
                $bill_of_material_details->qty = $request->qty_in[$i] ?? 0;
                $bill_of_material_details->details_copy = 1;
                $bill_of_material_details->save();
            }
        }
        // stock out
        for ($i = 0; $i < count($request->product_out_id); $i++) {
            if (! empty($request->product_out_id[$i])) {
                $bill_of_material_details = new BillOfMaterialDetails();
                $bill_of_material_details->bom_id = $data->bom_id;
                $bill_of_material_details->stock_item_id = $request->product_out_id[$i];
                $bill_of_material_details->qty = $request->qty_out[$i] ?? 0;
                $bill_of_material_details->details_copy = 2;
                $bill_of_material_details->save();
            }
        }

        return $data;
    }

    public function getBillOfMaterialId($id)
    {
        return BillOfMaterial::find($id, ['bom_id', 'bom_name']);
    }

    public function updateBillOfMaterial(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = BillOfMaterial::findOrFail($id);
        $data->bom_date = date('Y-m-d');
        $data->bom_name = $request->bom_name;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->save();
        // stock in
        for ($i = 0; $i < count($request->product_in_id); $i++) {
            if (! empty($request->stock_in_id[$i])) {
                if (! empty($request->product_in_id[$i])) {
                    $bill_of_material_details = BillOfMaterialDetails::findOrFail($request->stock_in_id[$i]);
                    $bill_of_material_details->stock_item_id = $request->product_in_id[$i];
                    $bill_of_material_details->qty = $request->qty_in[$i] ?? 0;
                    $bill_of_material_details->save();
                }
            } else {
                if (! empty($request->product_in_id[$i])) {
                    $bill_of_material_details = new BillOfMaterialDetails();
                    $bill_of_material_details->bom_id = $data->bom_id;
                    $bill_of_material_details->stock_item_id = $request->product_in_id[$i];
                    $bill_of_material_details->qty = $request->qty_in[$i] ?? 0;
                    $bill_of_material_details->details_copy = 1;
                    $bill_of_material_details->save();
                }
            }
        }
        // stock out
        for ($i = 0; $i < count($request->product_out_id); $i++) {
            if (! empty($request->stock_out_id[$i])) {
                if (! empty($request->product_out_id[$i])) {
                    $bill_of_material_details = BillOfMaterialDetails::findOrFail($request->stock_out_id[$i]);
                    $bill_of_material_details->stock_item_id = $request->product_out_id[$i];
                    $bill_of_material_details->qty = $request->qty_out[$i] ?? 0;
                    $bill_of_material_details->save();
                }
            } else {
                if (! empty($request->product_out_id[$i])) {
                    $bill_of_material_details = new BillOfMaterialDetails();
                    $bill_of_material_details->bom_id = $data->bom_id;
                    $bill_of_material_details->stock_item_id = $request->product_out_id[$i];
                    $bill_of_material_details->qty = $request->qty_out[$i] ?? 0;
                    $bill_of_material_details->details_copy = 2;
                    $bill_of_material_details->save();
                }
            }
        }

        if (! empty($request->delete_stock_out_id) || ! empty($request->delete_stock_in_id)) {
            $delete_stock_out = explode(',', $request->delete_stock_out_id);
            $delete_stock_in = explode(',', $request->delete_stock_in_id);

            for ($i = 0; $i < count(($delete_stock_out)); $i++) {
                if (! empty($delete_stock_out[$i])) {
                    BillOfMaterialDetails::find($delete_stock_out[$i])->delete();
                }
            }
            for ($i = 0; $i < count(($delete_stock_in)); $i++) {
                if (! empty($delete_stock_in[$i])) {
                    BillOfMaterialDetails::find($delete_stock_in[$i])->delete();
                }
            }
        }

        return $data;
    }

    public function deleteBillOfMaterial($id)
    {
        BillOfMaterial::findOrFail($id)->delete();

        return BillOfMaterialDetails::where('bom_id', $id)->delete();
    }

    public function getBillOfMaterialDetailsId($id)
    {
        return DB::table('bom_details')->leftJoin('stock_item', 'bom_details.stock_item_id', '=', 'stock_item.stock_item_id')->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')->where('bom_id', $id)->get(['bom_details.details_id', 'bom_details.bom_id', 'bom_details.stock_item_id', 'bom_details.qty', 'stock_item.product_name', 'bom_details.details_copy', 'unitsof_measure.symbol']);
    }
}
