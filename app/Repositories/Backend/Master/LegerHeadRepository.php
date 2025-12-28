<?php

namespace App\Repositories\Backend\Master;

use App\Models\LegerHead;
use App\Models\MirrorLedgerHead;
use App\Services\Tree;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockIn;

class LegerHeadRepository implements LegerHeadInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * getLegerHeadOfIndex
     *
     * ledger head all data get
     *
     * @return object
     */
    public function getLegerHeadOfIndex()
    {
        
        return DB::select("SELECT l.bangla_ledger_name,l.other_details,l.user_name,l.ledger_head_id,l.DrCr, l.ledger_name,gc.group_chart_id,gc.group_chart_name ,l.ledger_type,l.alias,gc.under,g.group_chart_name as o,l.inventory_value,l.opening_balance FROM ledger_head as l LEFT JOIN group_chart as gc ON gc.group_chart_id=l.group_id  LEFT join group_chart as g on g.group_chart_id=gc.nature_group WHERE gc.group_chart_name != 'Reserved'  ORDER BY l.ledger_name ASC ");
    }

    /**
     * StoreLegerHead
     *
     * ledger head data store
     *
     * @return object
     */
    public function StoreLegerHead($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $ledger_head = new LegerHead();
        $ledger_head->ledger_name = $request->ledger_name;
        $ledger_head->bangla_ledger_name = $request->bangla_ledger_name;
        $ledger_head->alias = $request->alias;
        $ledger_head->alias_type = $request->alias_type;
        $ledger_head->group_id = $request->group_id;
        $ledger_head->ledger_type = $request->ledger_type;
        $ledger_head->under_ledger_id=$request->under_ledger_id??0;
        $ledger_head->unit_or_branch = $request->unit_or_branch;
        $ledger_head->nature_activity = $request->nature_activity;
        $ledger_head->inventory_value = $request->inventory_value;
        $ledger_head->opening_balance = $request->opening_balance;
        $ledger_head->DrCr = $request->DrCr;
        $ledger_head->credit_limit = $request->credit_limit;
        $ledger_head->mailing_name = $request->mailing_name;
        $ledger_head->mobile = $request->mobile;
        $ledger_head->mailing_add = $request->mailing_add;
        $ledger_head->national_id = $request->national_id;
        $ledger_head->trade_licence_no = $request->trade_licence_no;
        $ledger_head->tin_certificate=$request->tin_certificate;
        $ledger_head->bank_cheque = $request->bank_cheque;
        $ledger_head->user_id = Auth::id();
        $ledger_head->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $ledger_head->user_name = Auth::user()->user_name;



        $ledger_head->save();
        //store procedure
        // DB::select("CALL ledger_head_opening_blance($ledger_head->group_id,'$request->DrCr', $ledger_head->opening_balance,$ledger_head->ledger_head_id)");

        return $ledger_head;
    }

    public function getLegerHeadId($id)
    {
        return LegerHead::findOrFail($id, ['bangla_ledger_name', 'ledger_head_id', 'ledger_name', 'alias', 'group_id', 'nature_activity', 'inventory_value', 'opening_balance', 'DrCr', 'credit_limit', 'mailing_name', 'mailing_add', 'national_id', 'trade_licence_no', 'unit_or_branch','bank_cheque','tin_certificate','mobile','ledger_type','under_ledger_id','alias_type']);
    }

    public function updateLegerHead($request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ledger_head = LegerHead::findOrFail($id);
        $mirrorData=clone $ledger_head;
        $ledger_head->ledger_name = $request->ledger_name;
        $ledger_head->bangla_ledger_name = $request->bangla_ledger_name;
        $ledger_head->alias = $request->alias;
        $ledger_head->alias_type = $request->alias_type;
        $ledger_head->group_id = $request->group_id;
        $ledger_head->ledger_type = $request->ledger_type;
        $ledger_head->under_ledger_id=$request->under_ledger_id??0;
        $ledger_head->unit_or_branch = $request->unit_or_branch;
        $ledger_head->nature_activity = $request->nature_activity;
        $ledger_head->inventory_value = $request->inventory_value;
        $ledger_head->opening_balance = $request->opening_balance;
        $ledger_head->DrCr = $request->DrCr;
        $ledger_head->credit_limit = $request->credit_limit;
        $ledger_head->mailing_name = $request->mailing_name;
        $ledger_head->mobile = $request->mobile;
        $ledger_head->mailing_add = $request->mailing_add;
        $ledger_head->national_id = $request->national_id;
        $ledger_head->trade_licence_no = $request->trade_licence_no;
        $ledger_head->tin_certificate=$request->tin_certificate;
        $ledger_head->bank_cheque = $request->bank_cheque;
        $update_history = json_decode($ledger_head->other_details);
        $ledger_head->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By:'.Auth::user()->user_name.', Ip: '.$ip);
        $ledger_head->save();

        $MirrorLedgerHead=new MirrorLedgerHead();
        $MirrorLedgerHead->type='update';
        $MirrorLedgerHead->ledger_head_id=$id;
        $MirrorLedgerHead->user_name=Auth::user()->user_name;
        $MirrorLedgerHead->user_id=Auth::id();
        $MirrorLedgerHead->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $MirrorLedgerHead->old_data=json_encode($mirrorData);
        $MirrorLedgerHead->new_data=json_encode($ledger_head);
        $MirrorLedgerHead->save();

        //store procedure
        // DB::select("CALL ledger_head_opening_blance($ledger_head->group_id,'$request->DrCr',$ledger_head->opening_balance,$ledger_head->ledger_head_id)");

        return $ledger_head;
    }

    public function deleteLegerHead($id)
    {
        $transaction_ledger= DB::table('debit_credit')->where('ledger_head_id',$id)->first();
        if(empty($transaction_ledger)){
            $ip = $_SERVER['REMOTE_ADDR'];
            $mirrorData=LegerHead::findOrFail($id);
            $MirrorLedgerHead=new MirrorLedgerHead();
            $MirrorLedgerHead->type='delete';
            $MirrorLedgerHead->ledger_head_id=$id;
            $MirrorLedgerHead->user_name=Auth::user()->user_name;
            $MirrorLedgerHead->user_id=Auth::id();
            $MirrorLedgerHead->other_details=json_encode('<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
            $MirrorLedgerHead->old_data=json_encode($mirrorData);
            $MirrorLedgerHead->save();
            return LegerHead::findOrFail($id)->delete();
        }else{
            throw new \Exception("ledger can't delete");
        }
       
    }

    public function getTree()
    {
        $group_chart = $this->getLedgerHeadtData();
        $group_chart_object_to_array = json_decode(json_encode($group_chart, true), true);

        return $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'group_chart_id', 'under');
    }

    public function debitLedgerTreeSelectOption()
    {
        return LegerHead::get();
    }

    public function creditLedgerTreeSelectOption()
    {
        return LegerHead::get();
    }

    public function getLedgerHeadtData()
    {
        return DB::select("SELECT l.other_details,l.user_name,l.mobile,l.mailing_add,l.ledger_head_id ,l.DrCr, 
                l.ledger_name,gc.group_chart_id,gc.group_chart_name ,gc.alias,gc.under,g.group_chart_name as o,
                l.inventory_value,l.opening_balance, l.ledger_type
                FROM ledger_head as l 
                Right JOIN group_chart as gc ON gc.group_chart_id=l.group_id  
                LEFT join group_chart as g on g.group_chart_id=gc.nature_group 
                WHERE gc.group_chart_name != 'Reserved'  ORDER BY gc.group_chart_id DESC");
    }

    public function debit_nature_group()
    {
        $data = DB::table('group_chart')
            ->select('group_chart.group_chart_id', 'group_chart.under', 'group_chart.group_chart_name', 'ledger_head.ledger_name', 'ledger_head.group_id', 'ledger_head.ledger_head_id')
            ->leftJoin('ledger_head', 'group_chart.group_chart_id', '=', 'ledger_head.group_id')
            ->where('group_chart.group_chart_id', 32)
            ->get();

        return json_decode(json_encode($data, true), true);
    }

    public function getSpecificLedgerData()
    {
        $ledger = DB::select("SELECT ledger_head.ledger_head_id,ledger_head.ledger_name,ledger_head.ledger_type,group_chart.group_chart_id,group_chart.group_chart_name,group_chart.under FROM group_chart LEFT JOIN ledger_head  ON  group_chart.group_chart_id=ledger_head.group_id  WHERE group_chart.group_chart_name != 'Reserved'  ORDER BY group_chart.group_chart_id DESC");
        $ledger_object_to_array = json_decode(json_encode($ledger, true), true);

        return $this->tree->buildTree($ledger_object_to_array, 0, 0, 'group_chart_id', 'under');

    }

    public function searchingData($request){
        if (array_filter($request->all())) {
            $group_chart = explode('-', $request->group_id, 2);
        }
        $data = DB::select(
            "WITH recursive tree
                                AS
                                (
                                        SELECT group_chart.group_chart_id,
                                                group_chart.group_chart_name,
                                                group_chart.nature_group,
                                                group_chart.under
                                        FROM   group_chart
                                        WHERE  find_in_set(group_chart.group_chart_id,:group_chart)
                                        UNION
                                    SELECT e.group_chart_id,
                                                e.group_chart_name,
                                                e.nature_group,
                                                e.under
                                        FROM   tree h
                                        JOIN   group_chart e
                                        ON     h.group_chart_id=e.under )
                                SELECT     group_chart.group_chart_id,
                                            group_chart.nature_group,
                                            group_chart.under,
                                            group_chart.group_chart_name,
                                            ledger_head.ledger_name,
                                            ledger_head.ledger_head_id,
                                            ledger_head.alias,
                                            ledger_head.mobile,
                                            ledger_head.mailing_add,
                                            ledger_head.opening_balance,
                                            ledger_head.other_details,
                                            ledger_head.user_name,
                                            ledger_head.DrCr,
                                            ledger_head.ledger_type,
                                            ledger_head.alias_type	
                                FROM      tree                                                                          AS group_chart
                                LEFT JOIN ledger_head
                                ON        group_chart.group_chart_id=ledger_head.group_id
                                ORDER BY  group_chart_name DESC,ledger_head.ledger_name DESC
        ",['group_chart'=>$group_chart[0]]);
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        return  $this->tree->buildTree($group_chart_object_to_array, $group_chart[1], 0, 'group_chart_id', 'under', 'ledger_head_id');
    }

    public function getAutoAlias(){
        $lastAlias = DB::table('ledger_head')->whereNotNull('alias')->orderByDesc(DB::raw('CAST(alias AS UNSIGNED)'))->value('alias');
        $nextAlias = $lastAlias ? strval(intval($lastAlias) + 1) : '100001';
        return $nextAlias;
    }

    public function duplicateAliasCheck($request){
        $count=DB::table('ledger_head')->where('alias',$request->alias)->count();
        $duplicates=[];
        if(!empty($request->alias)){
            $duplicates = DB::table('ledger_head')
            ->select('alias')
            ->where('alias', 'like', $request->alias . '%')
            ->limit(5)
            ->get();
            if(count($duplicates)<=0){
                $duplicates = DB::table('ledger_head')
                ->select('alias')
                ->where('alias', 'like', '%' . $request->alias . '%')
                ->limit(5)
                ->get();
            }
        }
        return ['count'=>$count,'duplicates'=>$duplicates];
    }

    public function getLastManualAlias(){
        $lastAlias = DB::table('ledger_head')->where('alias_type',1)->orderByDesc(DB::raw('CAST(alias AS UNSIGNED)'))->value('alias');
        return $lastAlias;
    }


    public function aliasUpdate($request){
        $data=[];
        for ($i = 0; $i < count($request->ledger_head_id); $i++) {
            $id = $request->ledger_head_id[$i];
            $alias = $request->alias[$i];
            $alias_type = $request->alias_type[$i];
            DB::table('ledger_head')
                ->where('ledger_head_id', $request->ledger_head_id[$i])
                ->update([
                    'alias' => $alias,
                    'alias_type' => $alias_type,
                ]);
            $data[] = [
                    'ledger_head_id' => $id,
                    'alias' => $alias,
                    'alias_type' => $alias_type,
                ];
        }
    
        return $data;
    }
}
