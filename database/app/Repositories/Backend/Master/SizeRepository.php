<?php

namespace App\Repositories\Backend\Master;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SizeRepository implements SizeInterface
{
    public function getSizeOfIndex()
    {
        return Size::orderBy('symbol', 'ASC')->get(['other_details', 'user_name', 'unit_of_size_id', 'symbol', 'formal_name']);
    }

    public function StoreSize(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new Size();
        $data->symbol = $request->symbol;
        $data->formal_name = $request->formal_name;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').',  By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getSizeId($id)
    {
        return Size::find($id);
    }

    public function updateSize(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = Size::findOrFail($id);
        $data->symbol = $request->symbol;
        $data->formal_name = $request->formal_name;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').',  By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->save();

        return $data;
    }

    public function deleteSize($id)
    {
        return Size::findOrFail($id)->delete();
    }
}
