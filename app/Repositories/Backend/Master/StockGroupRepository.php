<?php

namespace App\Repositories\Backend\Master;

use App\Models\StockGroup;
use App\Models\MirrorStockGroup;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\AuthRepository;

class StockGroupRepository implements StockGroupInterface
{
    private $tree;
    

    public function __construct(Tree $tree , AuthRepository $authRepository)
    {
         $this->tree = $tree;
         $this->authRepository = $authRepository;
    }

    public function getStockGroupOfIndex()
    {
       
        return StockGroup::select('stock_group_id', 'stock_group_name', 'alias', 'under', 'other_details', 'user_name')->orderBy('stock_group_name', 'DESC')->get();

    }

    public function storeStockGroup(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new StockGroup();
        $data->stock_group_name = $request->stock_group_name;
        $data->under = $request->under;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->item_add = $request->item_add;
        $data->alias = $request->alias;
        $data->group_category = $request->group_category;
        $data->sales_ledger = $request->sales_ledger??0;
        $data->purchase_ledger=$request->purchase_ledger??0; 
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->save();

        return $data;
    }

    public function getStockGroupId($id)
    {
        return StockGroup::find($id);
    }

    public function updateStockGroup(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = StockGroup::findOrFail($id);
        $mirrorData=clone $data;
        $data->stock_group_name = $request->stock_group_name;
        $data->under = $request->under;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->item_add = $request->item_add;
        $data->alias = $request->alias;
        $data->group_category = $request->group_category;
        $data->sales_ledger = $request->sales_ledger??0;
        $data->purchase_ledger=$request->purchase_ledger??0; 
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->save();

        $MirrorStockGroup=new MirrorStockGroup();
        $MirrorStockGroup->type='update';
        $MirrorStockGroup->stock_group_id=$id;
        $MirrorStockGroup->user_name=Auth::user()->user_name;
        $MirrorStockGroup->user_id=Auth::id();
        $MirrorStockGroup->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $MirrorStockGroup->old_data=json_encode($mirrorData);
        $MirrorStockGroup->new_data=json_encode($data);
        $MirrorStockGroup->save();

        return $data;

    }

    public function deleteStockGroup($id)
    {
        $stock_group= DB::table('stock_item')->where('stock_group_id',$id)->first();
        if(empty($stock_group)){
            $ip = $_SERVER['REMOTE_ADDR'];
            $mirrorData=StockGroup::findOrFail($id);
            $MirrorStockGroup=new MirrorStockGroup();
            $MirrorStockGroup->type='delete';
            $MirrorStockGroup->stock_group_id=$id;
            $MirrorStockGroup->user_name=Auth::user()->user_name;
            $MirrorStockGroup->user_id=Auth::id();
            $MirrorStockGroup->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
            $MirrorStockGroup->old_data=json_encode($mirrorData);
            $MirrorStockGroup->save();
            return StockGroup::findOrFail($id)->delete();
        }else{
            throw new \Exception("stock group can't delete");
        }
     
    }

    public function getTreeStockGroup()
    {
        $group_chart = $this->getStockGroupOfIndex();
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under');
    }

     public function getTreeSelectOption($under_id = null)
    {
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);

        // user wise group chart
        if ($get_user->user_level != 1) {
            if (array_sum(array_map('intval',explode(' ',$get_user->stock_group_id_multiple))) != 0) {
                $data = '';
                $group_chart_data = $this->tree->stock_group_tree_row_query($get_user->stock_group_id_multiple);
                $keys = array_keys(array_column($this->tree->stock_group_tree_row_query($get_user->stock_group_id_multiple), 'lvl'), 1);
                $new_array = array_map(function ($k) use ($group_chart_data) {
                    return $group_chart_data[$k];
                }, $keys);

                for ($i = 0; $i < count($keys); $i++) {
                    return $data .= $this->tree->getTreeViewSelectOptionStock_group_two($this->tree->stock_group_tree_row_query($get_user->stock_group_id_multiple), $new_array[$i]['under'], $under_id);
                }

            } else {
                $group_chart = $this->getStockGroupOfIndex();
                $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);
                $build_group_tree = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under');
                return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'stock_group_id', 'under', 'stock_group_name', $under_id);
            }
        } else {
            $group_chart = $this->getStockGroupOfIndex();
            $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);
            $build_group_tree = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under');

            return $this->tree->getTreeViewSelectOption($build_group_tree, 0, 'stock_group_id', 'under', 'stock_group_name', $under_id);
        }
        
    }
}
