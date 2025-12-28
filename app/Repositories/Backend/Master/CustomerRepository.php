<?php

namespace App\Repositories\Backend\Master;

use App\Models\Customer;
use App\Models\MirrorCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerRepository implements CustomerInterface
{
    public function getCustomerOfIndex()
    {
        return Customer::select('other_details', 'user_name', 'customer_id', 'customer_name', 'proprietor', 'phone1', 'district')->orderBy('customer_name', 'ASC')->get();
    }

    public function storeCustomer(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new Customer();
        $data->customer_name = $request->customer_name;
        $data->ledger_id = $request->ledger_id;
        $data->proprietor = $request->proprietor;
        $data->phone1 = $request->phone1;
        $data->district = $request->district;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getCustomerId($id)
    {
        return Customer::find($id, ['customer_id', 'ledger_id', 'customer_name', 'proprietor', 'phone1', 'district']);
    }

    public function updateCustomer(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = Customer::findOrFail($id);
        $mirrorData=clone $data;
        $data->customer_name = $request->customer_name;
        $data->ledger_id = $request->ledger_id;
        $data->proprietor = $request->proprietor;
        $data->phone1 = $request->phone1;
        $data->district = $request->district;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->save();

        $MirrorCustomer=new MirrorCustomer();
        $MirrorCustomer->type='update';
        $MirrorCustomer->customer_id=$id;
        $MirrorCustomer->user_name=Auth::user()->user_name;
        $MirrorCustomer->user_id=Auth::id();
        $MirrorCustomer->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $MirrorCustomer->old_data=json_encode($mirrorData);
        $MirrorCustomer->new_data=json_encode($data);
        $MirrorCustomer->save();

        return $data;

    }

    public function deleteCustomer($id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $mirrorData=Customer::findOrFail($id);
        $MirrorCustomer=new MirrorCustomer();
        $MirrorCustomer->type='delete';
        $MirrorCustomer->customer_id=$id;
        $MirrorCustomer->user_name=Auth::user()->user_name;
        $MirrorCustomer->user_id=Auth::id();
        $MirrorCustomer->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $MirrorCustomer->old_data=json_encode($mirrorData);
        $MirrorCustomer->save();
        return Customer::findOrFail($id)->delete();
    }
}
