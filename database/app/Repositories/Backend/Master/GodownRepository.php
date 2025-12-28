<?php

namespace App\Repositories\Backend\Master;

use App\Models\Godown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GodownRepository implements GodownInterface
{
    public function getGodownOfIndex()
    {
        return Godown::select('other_details', 'user_name', 'alias', 'godown_id', 'godown_name', 'godown_under', 'godown_type')->orderBy('godown_name', 'ASC')->get();
    }

    public function StoreGodown(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new Godown();
        $data->godown_name = $request->godown_name;
        $data->godown_type = $request->godown_type;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->alias = $request->alias;
        $data->address = $request->address;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By :'.Auth::user()->user_name.', Ip: '.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getGodownId($id)
    {
        return Godown::find($id);
    }

    public function updateGodown(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = Godown::findOrFail($id);
        $data->godown_name = $request->godown_name;
        $data->godown_type = $request->godown_type;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->alias = $request->alias;
        $data->address = $request->address;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->save();

        return $data;
    }

    public function deleteGodown($id)
    {
        return Godown::findOrFail($id)->delete();
    }

    public function withoutDamagegodown(){
        return Godown::select('other_details', 'user_name', 'alias', 'godown_id', 'godown_name', 'godown_under', 'godown_type')->whereNot('godown_type','Damage')->orderBy('godown_name', 'ASC')->get();
    }
}
