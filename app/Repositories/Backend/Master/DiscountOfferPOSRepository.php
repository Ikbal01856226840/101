<?php

namespace App\Repositories\Backend\Master;

use App\Models\DiscountOfferPOS;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class   DiscountOfferPOSRepository implements DiscountOfferPOSInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getDiscountOfferPOSOfIndex()
    {
        return DB::table('offer_setup')
              ->select('offer_setup.offer_id','stock_group.stock_group_name','offer_setup.price','offer_setup.discount','offer_setup.date_from','offer_setup.date_to','distribution_center.dis_cen_name')
              ->leftJoin('stock_group', 'offer_setup.stock_group_id', '=', 'stock_group.stock_group_id')
              ->leftJoin('distribution_center', 'offer_setup.dis_cen_id', '=', 'distribution_center.dis_cen_id')
              ->orderBy('stock_group.stock_group_name', 'ASC')->get();
    }

    public function StoreDiscountOfferPOS(Request $request)
    {

        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new DiscountOfferPOS();
        $data->stock_group_id=$request->stock_group_id;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->price =(float) $request->price;
        $data->discount =(float) $request->discount;
        $data->date_from = $request->date_from;
        $data->date_to = $request->date_to;
        $data->remarks = $request->remarks;
        $data->approved_by = $request->approved_by;
        $data->dis_cen_id = $request->dis_cen_id;
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->user_name = Auth::user()->user_name;

        $data->save();

        return $data;
    }

    public function getDiscountOfferPOSId($id)
    {
        return DiscountOfferPOS::find($id);
    }

    public function updateDiscountOfferPOS(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = DiscountOfferPOS::findOrFail($id);
        $data->stock_group_id=$request->stock_group_id;
        $data->unit_or_branch = $request->unit_or_branch;
        $data->price =(float) $request->price;
        $data->discount =(float) $request->discount;
        $data->date_from = $request->date_from;
        $data->date_to = $request->date_to;
        $data->remarks = $request->remarks;
        $data->entry_date = $request->entry_date;
        $data->user_id = $request->user_id;
        $data->approved_by = $request->approved_by;
        $data->dis_cen_id = $request->dis_cen_id;
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On:'.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name.' Ip:'.$ip);
        $data->save();

        return $data;
    }

    public function deleteDiscountOfferPOS($id)
    {
        return DiscountOfferPOS::findOrFail($id)->delete();
    }

}
