<?php

namespace App\Repositories\Backend\Master;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierRepository implements SupplierInterface
{
    public function getSupplierOfIndex()
    {
        return Supplier::orderBy('supplier_name', 'ASC')->get(['other_details', 'user_name', 'id', 'supplier_name', 'proprietor', 'phone1', 'district']);
    }

    public function storeSupplier(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new Supplier();
        $data->supplier_name = $request->supplier_name;
        $data->ledger_id = $request->ledger_id;
        $data->proprietor = $request->proprietor;
        $data->phone1 = $request->phone1;
        $data->district = $request->district;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip:,'.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getSupplierId($id)
    {

        return Supplier::find($id, ['id', 'ledger_id', 'supplier_name', 'proprietor', 'phone1', 'district']);
    }

    public function updateSupplier(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = Supplier::findOrFail($id);
        $data->supplier_name = $request->supplier_name;
        $data->ledger_id = $request->ledger_id;
        $data->proprietor = $request->proprietor;
        $data->phone1 = $request->phone1;
        $data->district = $request->district;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On :'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip :'.$ip);
        $data->save();

        return $data;

    }

    public function deleteSupplier($id)
    {
        return Supplier::findOrFail($id)->delete();
    }
}
