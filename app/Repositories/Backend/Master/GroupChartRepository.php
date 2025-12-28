<?php

namespace App\Repositories\Backend\Master;

use App\Models\GroupChart;
use App\Models\MirrorGroupChart;
use App\Repositories\Backend\AuthRepository;
use App\Services\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupChartRepository implements GroupChartInterface
{
    private $tree;

    private $authRepository;

    public function __construct(Tree $tree, AuthRepository $authRepository)
    {
        $this->tree = $tree;
        $this->authRepository = $authRepository;
    }

    public function getGroupChartOfIndex()
    {
        return DB::select(" SELECT gc.other_details,gc.user_name,gc.alias,gc.group_chart_id,gc.group_chart_name ,gc.alias,gc.under,g.group_chart_name as o FROM group_chart as gc LEFT join group_chart as g on g.group_chart_id=gc.nature_group WHERE gc.group_chart_name != 'Reserved' ORDER BY gc.group_chart_name ASC  ");
    }

    public function StoreGroupChart($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $group_chart = new GroupChart();
        $group_chart->group_chart_name = $request->group_chart_name;
        $group_chart->unit_or_branch = $request->unit_or_branch;
        $group_chart->alias = $request->alias;
        $group_chart->under = $request->under;
        $group_chart->nature_group = $request->nature_group;
        $group_chart->user_id = Auth::id();
        $group_chart->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $group_chart->user_name = Auth::user()->user_name;
        $group_chart->save();

        return $group_chart;
    }

    public function getGroupChartId($id)
    {  
     return GroupChart::where('group_chart_id', $id)->first();       
    }

    public function updateGroupChart($request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = GroupChart::findOrFail($id);
        $mirrorData=clone $data;
        $data->group_chart_name = $request->group_chart_name;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->under = $request->under;
        $data->alias = $request->alias;
        $data->nature_group = $request->nature_group;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->save();

        $MirrorGroupChart=new MirrorGroupChart();
        $MirrorGroupChart->type='update';
        $MirrorGroupChart->group_chart_id=$id;
        $MirrorGroupChart->user_name=Auth::user()->user_name;
        $MirrorGroupChart->user_id=Auth::id();
        $MirrorGroupChart->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $MirrorGroupChart->old_data=json_encode($mirrorData);
        $MirrorGroupChart->new_data=json_encode($data);
        $MirrorGroupChart->save();

        return $data;
    }

    public function deleteGroupChart($id)
    {
        $group_chart= DB::table('ledger_head')->where('group_id',$id)->first();

        if(empty($group_chart)){
            $ip = $_SERVER['REMOTE_ADDR'];
            $mirrorData=GroupChart::findOrFail($id);
            $MirrorGroupChart=new MirrorGroupChart();
            $MirrorGroupChart->type='delete';
            $MirrorGroupChart->group_chart_id=$id;
            $MirrorGroupChart->user_name=Auth::user()->user_name;
            $MirrorGroupChart->user_id=Auth::id();
            $MirrorGroupChart->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
            $MirrorGroupChart->old_data=json_encode($mirrorData);
            $MirrorGroupChart->save();
          return GroupChart::where('group_chart_id', $id)->delete();
        }else{
            throw new \Exception("group chart can't delete");
        }
    }

    public function getTree()
    {
        $group_chart = $this->getGroupChartData();
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'group_chart_id', 'under');
    }

    public function get_nature_group($request)
    {
        return DB::select("SELECT g.group_chart_name as o,gc.nature_group FROM group_chart as gc LEFT join group_chart as g on g.group_chart_id=gc.nature_group WHERE gc.group_chart_id=:group_chart_id ORDER BY gc.group_chart_id ASC",['group_chart_id'=>$request->group_chart_id]);
    }

    public function getTreeSelectOption($under_id = null)
    {

        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);

        // user wise group chart
        if ($get_user->user_level != 1) {
            if (array_sum(array_map('intval',explode(' ',$get_user->agar))) != 0) {
                $data = '';
                $group_chart_data = $this->tree->group_chart_tree_row_query($get_user->agar);
                $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
                $new_array = array_map(function ($k) use ($group_chart_data) {
                    return $group_chart_data[$k];
                }, $keys);

                for ($i = 0; $i < count($keys); $i++) {
                    return $data .= $this->tree->getTreeViewSelectOptionGroup_chart_two($this->tree->group_chart_tree_row_query($get_user->agar), $new_array[$i]['under'], $under_id);
                }

            } else {
                $group_chart = $this->getGroupChartData();
                $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);
                $build_group_tree = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'group_chart_id', 'under');

                return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'group_chart_id', 'under', 'group_chart_name', $under_id);
            }
        } else {
            $group_chart = $this->getGroupChartData();
            $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);
            $build_group_tree = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'group_chart_id', 'under');

            return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'group_chart_id', 'under', 'group_chart_name', $under_id);
        }
    }

    public function getGroupChartData()
    {
        return DB::select("SELECT gc.other_details,gc.user_name,gc.alias,gc.group_chart_id,gc.group_chart_name ,gc.alias,gc.under,g.group_chart_name as o FROM group_chart as gc LEFT join group_chart as g on g.group_chart_id=gc.nature_group WHERE gc.group_chart_name != 'Reserved'  ORDER BY gc.group_chart_name DESC ");
    }
}
