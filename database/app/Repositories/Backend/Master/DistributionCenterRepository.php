<?php

namespace App\Repositories\Backend\Master;

use App\Models\DistributionCenter;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributionCenterRepository implements DistributionCenterInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getDistributionCenterOfIndex()
    {
        return DistributionCenter::select('other_details', 'user_name', 'dis_cen_id', 'dis_cen_name', 'alias', 'discount', 'dis_cen_under')->orderBy('dis_cen_name', 'ASC')->get();
    }

    public function StoreDistributionCenter(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new DistributionCenter();
        $data->dis_cen_name = $request->dis_cen_name;
        $data->dis_cen_under = $request->dis_cen_under;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->alias = $request->alias;
        $data->address = $request->address;
        $data->discount = $request->discount;
        $data->date_start = $request->date_start;
        $data->date_end = $request->date_end;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getDistributionCenterId($id)
    {
        return DistributionCenter::find($id);
    }

    public function updateDistributionCenter(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = DistributionCenter::findOrFail($id);
        $data->dis_cen_name = $request->dis_cen_name;
        $data->dis_cen_under = $request->dis_cen_under;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->alias = $request->alias;
        $data->address = $request->address;
        $data->discount = $request->discount;
        $data->date_start = $request->date_start;
        $data->date_end = $request->date_end;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->save();

        return $data;
    }

    public function deleteDistributionCenter($id)
    {
        return DistributionCenter::findOrFail($id)->delete();
    }

    public function getTreeSelectOption()
    {
        $group_chart = $this->getDistributionCenterOfIndex();
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);
        $build_group_tree = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'dis_cen_id', 'dis_cen_under');

        return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'dis_cen_id', 'dis_cen_under', 'dis_cen_name');
    }
}
