<?php

namespace App\Repositories\Backend\Master;

use App\Models\Godown;
use App\Models\MirrorGodowns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\AuthRepository;

class GodownRepository implements GodownInterface
{
    private $authRepository;
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;

    }
    
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
        $mirrorData=clone $data;
        $data->godown_name = $request->godown_name;
        $data->godown_type = $request->godown_type;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->alias = $request->alias;
        $data->address = $request->address;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->save();

        $MirrorGodown=new MirrorGodowns();
        $MirrorGodown->type='update';
        $MirrorGodown->godown_id=$id;
        $MirrorGodown->user_name=Auth::user()->user_name;
        $MirrorGodown->user_id=Auth::id();
        $MirrorGodown->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $MirrorGodown->old_data=json_encode($mirrorData);
        $MirrorGodown->new_data=json_encode($data);
        $MirrorGodown->save();

        return $data;
    }

    public function deleteGodown($id)
    {
        $stock_in= DB::table('stock_in')->where('godown_id',$id)->first();
        $stock_out= DB::table('stock_out')->where('godown_id',$id)->first();
        if(empty($stock_in) && empty($stock_out)){
            $ip = $_SERVER['REMOTE_ADDR'];
            $mirrorData=Godown::findOrFail($id);
            $MirrorGodown=new MirrorGodowns();
            $MirrorGodown->type='delete';
            $MirrorGodown->godown_id=$id;
            $MirrorGodown->user_name=Auth::user()->user_name;
            $MirrorGodown->user_id=Auth::id();
            $MirrorGodown->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
            $MirrorGodown->old_data=json_encode($mirrorData);
            $MirrorGodown->save();
            return Godown::findOrFail($id)->delete();
        }else{
            throw new \Exception("Godown can't delete");
        }
        
    }

    public function withoutDamagegodown(){
        return Godown::select('other_details', 'user_name', 'alias', 'godown_id', 'godown_name', 'godown_under', 'godown_type')->whereNot('godown_type','Damage')->orderBy('godown_name', 'ASC')->get();
    }

    public function godownAccess()
    {

        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        if (array_sum(array_map('intval', explode(',', $get_user->godown_id))) != 0 &&(auth()->user()->user_level != 1)) {
            return DB::table('godowns')->whereIn('godown_id',explode(",",$get_user->godown_id))->get(['godown_id', 'godown_name']);
        } else {
            return $this->getGodownOfIndex();
        }
    }
}
