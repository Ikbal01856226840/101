<?php

namespace App\Repositories\Backend\Master;

use App\Models\Components;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComponentsRepository implements ComponentsInterface
{
    public function getComponentsOfIndex()
    {
        return Components::select('other_details', 'user_name', 'comp_id', 'comp_name', 'comp_alias', 'notes')->orderBy('comp_name', 'ASC')->get();
    }

    public function storeComponents(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new Components();
        $data->comp_name = $request->comp_name;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->comp_alias = $request->comp_alias;
        $data->notes = $request->notes;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getComponentsId($id)
    {
        return Components::find($id);
    }

    public function updateComponents(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = Components::findOrFail($id);
        $data->comp_name = $request->comp_name;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->comp_alias = $request->comp_alias;
        $data->notes = $request->notes;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->save();

        return $data;

    }

    public function deleteComponents($id)
    {
        return Components::findOrFail($id)->delete();
    }
}
