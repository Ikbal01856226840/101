<?php

namespace App\Repositories\Backend\Voucher;

use App\Models\DebitCredit;
use App\Models\StockItemCommissionVoucher;
use App\Models\TransactionMaster;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VoucherCommissionRepository implements VoucherCommissionInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }


    public function storeVoucherCommission($request, $voucher_invoice)
    {
        $lockKey = 'commission-create-lock-' . ($request->invoice_no ?? uniqid());
        return Cache::lock($lockKey, 10)->block(5, function () use ($request) {
        DB::beginTransaction();
        try {
            $ip = $_SERVER['REMOTE_ADDR'];
            $data = new TransactionMaster();
            if (! empty($voucher_invoice)) {
                $data->invoice_no = $voucher_invoice;
            } else {
                $data->invoice_no = $request->invoice_no;
            }
            $data->ref_no = $request->ref_no;
            $data->transaction_date = $request->invoice_date;
            $data->unit_or_branch = $request->unit_or_branch;
            $data->voucher_id = $request->voucher_id;
            $data->narration = $request->narration;
            $data->commission_from_date = $request->commission_from_date;
            $data->commission_to_date = $request->commission_to_date;
            $data->secret_narration = $request->secret_narration;
            $data->user_id = auth()->id();
            $data->entry_date = date('Y-m-d');
            $data->tran_time = date('H:i:s');
            $data->user_name = Auth::user()->user_name;
            $data->other_details = json_encode('Created on: '.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
            $data->save();

            if (empty($request->party_ledger_id) ||empty($request->commission_ledger_id)) {
                    throw new \Exception("The ledger Emtry");
            }
            //credit data
            $debit_data = new DebitCredit();
            $debit_data->tran_id = $data->tran_id ?? exit;
            $debit_data->ledger_head_id = (int)$request->party_ledger_id;
            $debit_data->debit = 0;
            $debit_data->credit = (float) $request->total_commission_per ?? 0;
            $debit_data->dr_cr = 'Cr';
            $debit_data->save();

            //debit data
            $debit_credit_data = new DebitCredit();
            $debit_credit_data->tran_id = $data->tran_id ?? exit;
            $debit_credit_data->ledger_head_id =(int) $request->commission_ledger_id;
            $debit_credit_data->debit = (float) $request->total_commission_per ?? 0;
            $debit_credit_data->credit = 0;
            $debit_credit_data->dr_cr = 'Dr';
            $debit_credit_data->save();

        
            $stock_out_data = [];

            //credit data stock item commission
            for ($i = 0; $i < count(array_filter($request->stock_item_id)); $i++) {
                if (! empty($request->commission_parqty[$i])) {
                    $stock_out_data[] = [
                        'tran_id' => $data->tran_id ?? exit,
                        'tran_date' => $request->invoice_date,
                        'stock_item_id' => $request->stock_item_id[$i],
                        'com_qty' => (float) $request->parqty[$i] ?? 0,
                        'com_rate' => (float) $request->commission_parqty[$i],
                        'com_percent' => (float) $request->commission_sale_value[$i],
                        'com_total' => (float) $request->commission_amount[$i],
                    ];
                }
            }
           StockItemCommissionVoucher::insert($stock_out_data);
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Commission Create Error: " . $e->getMessage());
            throw $e;
        }
     });
    }

    public function getVoucherCommissionId($id)
    {

        return TransactionMaster::findOrFail($id);
    }

    public function updateVoucherCommission(Request $request, $id, $voucher_invoice)
    {
         $lockKey = 'commission-update-lock-' . ($request->invoice_no ?? uniqid());
           return Cache::lock($lockKey, 10)->block(5, function () use ($request,$id) {
                DB::beginTransaction();
                try {
                $ip = $_SERVER['REMOTE_ADDR'];
                $data = TransactionMaster::findOrFail($id);
                $data->invoice_no = $request->invoice_no;
                $update_history = json_decode($data->other_details);
                $data->ref_no = $request->ref_no;
                $data->transaction_date = $request->invoice_date;
                $data->unit_or_branch = $request->unit_or_branch;
                $data->voucher_id = $request->voucher_id;
                $data->narration = $request->narration;
                $data->commission_from_date = $request->commission_from_date;
                $data->commission_to_date = $request->commission_to_date;
                $data->user_id = auth()->id();
                $data->entry_date = date('Y-m-d');
                $data->tran_time = date('H:i:s');
                $data->other_details = json_encode($update_history.'<br> Updated on:'.\Carbon\Carbon::now()->toDateTimeString().'By:'.Auth::user()->user_name.'Ip:'.$ip);
                $data->save();

                 if (empty($request->party_ledger_id) ||empty($request->commission_ledger_id)) {
                    throw new \Exception("The ledger Emtry");
                
                }
                
                //credit data
                if(!empty($request->credit_id)){
                    $debit_data = DebitCredit::find($request->credit_id);
                    $debit_data->ledger_head_id = $request->party_ledger_id;
                    $debit_data->debit = 0;
                    $debit_data->credit = (float) $request->total_commission_per ?? 0;
                    $debit_data->dr_cr = 'Cr';
                    $debit_data->save();
                }else{
                    //credit data
                    $debit_data = new DebitCredit();
                    $debit_data->tran_id = $id ?? exit;
                    $debit_data->ledger_head_id = $request->party_ledger_id;
                    $debit_data->debit = 0;
                    $debit_data->credit = (float) $request->total_commission_per ?? 0;
                    $debit_data->dr_cr = 'Cr';
                    $debit_data->save();
                }
                
                
                //debit data
                if(!empty($request->debit_id)){
                    $debit_credit_data = DebitCredit::find($request->debit_id);            
                    $debit_credit_data->ledger_head_id = $request->commission_ledger_id;
                    $debit_credit_data->debit = (float) $request->total_commission_per ?? 0;
                    $debit_credit_data->credit = 0;
                    $debit_credit_data->dr_cr = 'Dr';
                    $debit_credit_data->save();
                }else{
                    //debit data
                    $debit_credit_data = new DebitCredit();
                    $debit_credit_data->tran_id = $id ?? exit;
                    $debit_credit_data->ledger_head_id = $request->commission_ledger_id;
                    $debit_credit_data->debit = (float) $request->total_commission_per ?? 0;
                    $debit_credit_data->credit = 0;
                    $debit_credit_data->dr_cr = 'Dr';
                    $debit_credit_data->save();
                }      

                //credit data stock item commission
                for ($i = 0; $i < count(array_filter($request->stock_item_id)); $i++) {
                    if (! is_null($request->stock_comm_id[$i]) || ! empty($request->stock_comm_id[$i])) {
                        if (isset($request->commission_parqty[$i])) {
                            $stock_out_data = StockItemCommissionVoucher::where('stock_comm_id', $request->stock_comm_id[$i])->first();
                            if (isset($stock_out_data) && !is_null($stock_out_data)) {
                                $stock_out_data->com_qty = (float) $request->parqty[$i] ?? 0;
                                $stock_out_data->tran_date = $request->invoice_date;
                                $stock_out_data->com_rate = (float) $request->commission_parqty[$i];
                                $stock_out_data->com_percent = (float) $request->commission_sale_value[$i];
                                $stock_out_data->com_total = (float) $request->commission_amount[$i];
                                $stock_out_data->save();
                            } else {
                                if (!empty($request->commission_parqty[$i])) {
                                    $stock_out_data1 = new StockItemCommissionVoucher();
                                    $stock_out_data1->tran_id = $data->tran_id;
                                    $stock_out_data1->tran_date = $request->invoice_date;
                                    $stock_out_data1->stock_item_id = $request->stock_item_id[$i];
                                    $stock_out_data1->com_qty = (float) $request->parqty[$i] ?? 0;
                                    $stock_out_data1->com_rate = (float) $request->commission_parqty[$i];
                                    $stock_out_data1->com_percent = (float) $request->commission_sale_value[$i];
                                    $stock_out_data1->com_total = (float) $request->commission_amount[$i];
                                    $stock_out_data1->save();
                                }
                            }

                        }
                    }

                }
                   DB::commit();
                    return true;
                } catch (\Exception $e) {
                    DB::rollBack();

                    Log::error("Commission Update Error: " . $e->getMessage());
                    throw $e;
                }
            });
    }

    public function deleteVoucherCommission($id)
    {
        DebitCredit::where('tran_id', $id)->delete();
        StockItemCommissionVoucher::where('tran_id', $id)->delete();
        return TransactionMaster::findOrFail($id)->delete();
    }

    public function getCommission($request)
    {
        
                    $params=[];
                     $query= " SELECT   stock_group.stock_group_id,
                                        stock_group.stock_group_name,
                                        stock_group.under,
                                        t1.stock_item_id,
                                        t1.product_name,
                                        t1.qty,
                                        (t1.total/t1.qty) AS rate,
                                        t1.total,
                                        t1.qty AS stock_qty,
                                        t1.total AS stock_total
                                        FROM      stock_group

                                        LEFT JOIN
                                                (
                                                        SELECT      sum(stock_out.qty)        AS qty,
                                                                    sum(stock_out.total)      AS total,
                                                                    stock_out.stock_item_id AS product_out,
                                                                    stock_item.stock_item_id,
                                                                    stock_item.product_name,
                                                                    stock_item.stock_group_id
                                                        FROM       transaction_master
                                                        INNER JOIN `stock_out`
                                                        ON         transaction_master.tran_id=stock_out.tran_id
                                                        INNER JOIN stock_item
                                                        ON         stock_out.stock_item_id=stock_item.stock_item_id
                                                        INNER JOIN voucher_setup
                                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                                        INNER JOIN debit_credit  ON  transaction_master.tran_id=debit_credit.tran_id
                                                        WHERE      voucher_setup.voucher_type_id=19
                                                        AND        debit_credit.ledger_head_id=:ledger_head_id
                                                        AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                                        GROUP BY   stock_out.stock_item_id
                                                        ORDER BY   stock_item.product_name DESC
                                                         ) AS t1
                                        ON        stock_group.stock_group_id=t1.stock_group_id
                                        ORDER BY  stock_group.stock_group_name DESC, t1.product_name DESC
                     ";
                     $params['from_date'] = $request->from_date;
                     $params['to_date'] = $request->to_date;
                     $params['ledger_head_id'] =$request->party_ledger_id;

                     $data=  DB::select($query,$params);


            $group_chart_object_to_array = json_decode(json_encode($data, true), true);
            $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');
            $commission_ledger_voucher=$this->calculateGroupTotals($tree_data);
            $sum_of_children=$this->calculateSumOfChildren($commission_ledger_voucher);

         return ['commission_ledger_voucher'=>$commission_ledger_voucher,'sum_of_children'=>$sum_of_children];
    }

    public function stockItemCommissionGetId($id, $party_ledger_id, $request)
    {
        
            $params=[];
            $query= " SELECT   stock_group.stock_group_id,
                               stock_group.stock_group_name,
                               stock_group.under,
                               t1.stock_item_id,
                               t1.product_name,
                               t1.qty,
                               (t1.total/t1.qty) AS rate,
                               t1.total,
                               t1.qty AS stock_qty,
                               t1.total AS stock_total,
                               t1.stock_comm_id,
                               t1.com_qty,
                               t1.com_rate,
                               t1.com_percent,
                               t1.com_total
                               FROM      stock_group

                               LEFT JOIN
                                       (
                                               SELECT      sum(stock_out.qty)        AS qty,
                                                           sum(stock_out.total)      AS total,
                                                           stock_out.stock_item_id AS product_out,
                                                           stock_item.stock_item_id,
                                                           stock_item.product_name,
                                                           stock_item.stock_group_id,
                                                           stock_item_commission.stock_comm_id,
                                                           stock_item_commission.com_qty,
                                                           stock_item_commission.com_rate,
                                                           stock_item_commission.com_percent,
                                                           stock_item_commission.com_total
                                               FROM        transaction_master
                                               LEFT JOIN `stock_out`
                                               ON         transaction_master.tran_id=stock_out.tran_id
                                               LEFT JOIN stock_item
                                               ON         stock_out.stock_item_id=stock_item.stock_item_id
                                               LEFT JOIN stock_item_commission
                                               ON         stock_item.stock_item_id=stock_item_commission.stock_item_id  AND stock_item_commission.tran_id=:id
                                               LEFT JOIN voucher_setup
                                               ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                               LEFT JOIN debit_credit  ON  transaction_master.tran_id=debit_credit.tran_id
                                               WHERE      voucher_setup.voucher_type_id=19
                                               AND        debit_credit.ledger_head_id=:ledger_head_id
                                               AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                               GROUP BY   stock_out.stock_item_id
                                               ORDER BY   stock_item.product_name DESC
                                                ) AS t1
                               ON        stock_group.stock_group_id=t1.stock_group_id
                               ORDER BY  stock_group.stock_group_name DESC, t1.product_name DESC
            ";
            $params['from_date'] = $request->commission_from_date;
            $params['to_date'] = $request->commission_to_date;
            $params['ledger_head_id'] =$party_ledger_id;
            $params['id'] =$id;
            $data=  DB::select($query,$params);
           
        $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');
        $commission_ledger_voucher=$this->calculateGroupTotals($tree_data);

        $sum_of_children=$this->calculateSumOfChildren($commission_ledger_voucher);

        return ['commission_ledger_voucher'=>$commission_ledger_voucher,'sum_of_children'=>$sum_of_children];

    }

    public function calculateGroupTotals($arr)
    {
        foreach ($arr as &$obj) {
            if (isset($obj['children'])) {
                $obj['children'] = $this->calculateGroupTotals($obj['children']);
                $obj['stock_qty'] = array_sum(array_column($obj['children'], 'stock_qty')) + $obj['stock_qty'] ?? 0;
                $obj['stock_total'] = array_sum(array_column($obj['children'], 'stock_total')) + $obj['stock_total'] ?? 0;

            }
        }
        return $arr;
    }

    function calculateSumOfChildren($array)
    {
        $result = [];

        function sumProperties($array, $prop)
        {
            return array_reduce($array, function ($acc, $val) use ($prop) {
                return $acc + ($val[$prop] ?? 0);
            }, 0);
        }

        function processNode($node, &$result)
        {
            if (!isset($result[$node['stock_group_id']])) {
                $result[$node['stock_group_id']] = [
                    'stock_group_id' => $node['stock_group_id'],
                    'stock_qty' => 0,
                    'stock_total' => 0,

                ];
            }

            $currentNode = &$result[$node['stock_group_id']];

            $currentNode['stock_qty'] += $node['stock_qty'] ?? 0;
            $currentNode['stock_total'] += $node['stock_total'] ?? 0;

            if (isset($node['children'])) {
                foreach ($node['children'] as $child) {
                    processNode($child, $result);
                }
            }
        }

        foreach ($array as $node) {
            processNode($node, $result);
        }

        return array_values($result);
    }
}
