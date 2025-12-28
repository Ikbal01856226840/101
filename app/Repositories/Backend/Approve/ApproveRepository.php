<?php

namespace App\Repositories\Backend\Approve;

use App\Models\OrderDelivery;
use App\Models\TransactionMaster;
use App\Repositories\Backend\Voucher\VoucherCommissionRepository;
use App\Services\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
class ApproveRepository implements ApproveInterface
{
    private $voucherCommissionRepository;
     private $tree;

    public function __construct(VoucherCommissionRepository $voucherCommissionRepository,Tree $tree)
    {
        $this->voucherCommissionRepository = $voucherCommissionRepository;
        $this->tree=$tree;
       
    }

    public function getApproveOfIndex($request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;


        $voucher_sql = '';
        $delivery_status ='';
        $params = [];

        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            if($delivery_status = $request->delivery_status != ''){
                $delivery_status = $request->delivery_status == '' ? "" : "delivery_status=:delivery_status AND";
                $params['delivery_status'] = $request->delivery_status;

            }

            if ($request->voucher_id != 0) {
                if (strpos($request->voucher_id, 'v') !== false) {
                    $voucher_type_id = str_replace('v', '', $request->voucher_id);
                    $voucher_sql = "AND voucher_setup.voucher_type_id = :voucher_type_id";
                    $params['voucher_type_id'] = $voucher_type_id;
                } else {
                    $voucher_sql = "AND transaction_master.voucher_id = :voucher_id";
                    $params['voucher_id'] = $request->voucher_id;
                }
            }else{
                if(Auth()->user()->user_level==1){
                    $voucher_sql = '';
                }else{
                    // Fetch user access titles as an array
                    $user_access = DB::table('user_privileges')
                    ->where('table_user_id', Auth()->user()->id)
                    ->where('status_type', 'Voucher')
                    ->where('display_role', 1)
                    ->pluck('title_details'); // Pluck the 'title_details' column as a collection
                    // Convert collection to an array for `whereIn` usage
                    $user_access_array=0;
                    if($user_access->isNotEmpty()){
                        $user_access_array = implode(',', array_map('intval', $user_access->toArray()));
                        $voucher_sql="AND transaction_master.voucher_id IN($user_access_array)";
                    }
                }
            }
        }
        $query= "SELECT         transaction_master.tran_id,
                                transaction_master.invoice_no,
                                transaction_master.transaction_date,
                                transaction_master.user_id,
                                transaction_master.voucher_id,
                                transaction_master.narration,
                                transaction_master.other_details,
                                transaction_master.delivery_status,
                                voucher_setup.voucher_type_id,
                                debit_credit.ledger_head_id,
                                ledger_head.ledger_name,
                                ledger_head.opening_balance,
                                ledger_head.DrCr,
                                ledger_head.credit_limit,
                                voucher_setup.voucher_name,
                                debit_credit.dr_cr,
                                godowns.godown_name,
                                IF(voucher_setup.voucher_type_id=19
                OR              voucher_setup.voucher_type_id=23,
                                (
                                        SELECT    Concat(group_chart.nature_group,',',Sum(debit.debit),',',Sum(debit.credit))
                                        FROM      group_chart
                                        LEFT JOIN ledger_head
                                        ON        group_chart.group_chart_id=ledger_head.group_id
                                        LEFT JOIN debit_credit AS debit
                                        ON        ledger_head.ledger_head_id= debit.ledger_head_id
                                        WHERE     debit.ledger_head_id=debit_credit.ledger_head_id ) ,'') AS debit_credit_sum,
                                IF(transaction_master.delivery_status=1
                OR              transaction_master.delivery_status=2,
                                (
                                    SELECT other_details
                                    FROM   order_approver
                                    WHERE  order_approver.tran_id=transaction_master.tran_id
                                    AND    order_approver.order_approve_status=transaction_master.delivery_status Group BY transaction_master.tran_id ) ,'') AS order_approver
                FROM            (transaction_master
                INNER JOIN      voucher_setup
                ON              voucher_setup.voucher_id=transaction_master.voucher_id )
                LEFT OUTER JOIN (debit_credit
                INNER JOIN      ledger_head
                ON              ledger_head.ledger_head_id=debit_credit.ledger_head_id )
                ON              (
                                                debit_credit.tran_id=transaction_master.tran_id)
                LEFT JOIN      stock_out
                ON              transaction_master.tran_id=stock_out.tran_id
                LEFT JOIN      godowns
                ON              stock_out.godown_id=godowns.godown_id
                WHERE           $delivery_status voucher_setup.voucher_type_id IN(19,22,23,21)
                AND             transaction_master.transaction_date BETWEEN :from_date AND             :to_date $voucher_sql
                GROUP BY        transaction_master.tran_id
                ORDER BY    transaction_master.transaction_date ASC,transaction_master.tran_id ASC";

                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;


        return DB::select($query,$params);


    }

    public function storeApprove($request)
    {
    }

    public function getApproveId($id)
    {
        $data = DB::table('transaction_master')
            ->Join('stock_out', 'stock_out.tran_id', '=', 'transaction_master.tran_id')
            ->Join('stock_item', 'stock_out.product_id', '=', 'stock_item.stock_item_id')
            ->where('transaction_master.tran_id', $id)
            ->get();
    }

    public function updateApprove(Request $request, $id)
    {
    }

    public function deleteApprove($id)
    {
    }

    public function getStockIn($id)
    {
        return DB::table('stock_in')
            ->select('stock_in.stock_in_id', 'stock_in.tran_id', 'stock_in.stock_item_id', 'stock_in.qty', 'stock_in.rate', 'stock_in.total', 'stock_in.remark', 'stock_item.product_name', 'godowns.godown_id', 'godowns.godown_name', 'unitsof_measure.symbol', 'unitsof_measure.unit_of_measure_id')
            ->leftJoin('stock_item', 'stock_in.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_in.godown_id', '=', 'godowns.godown_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('stock_in.tran_id', $id)
            ->get();
    }

    public function getStockOut($id)
    {
        return DB::table('transaction_master')
            ->select('stock_item.product_name', 'stock_out.qty', 'stock_out.rate', 'stock_out.total', 'godowns.godown_name', 'unitsof_measure.symbol', 'unitsof_measure.unit_of_measure_id', 'stock_out.disc','stock_out.remark')
            ->Join('stock_out', 'stock_out.tran_id', '=', 'transaction_master.tran_id')
            ->leftJoin('godowns', 'stock_out.godown_id', '=', 'godowns.godown_id')
            ->Join('stock_item', 'stock_out.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('transaction_master.tran_id', $id)
            ->get();
    }

    public function getdebitCredit($id)
    {
        return DB::table('transaction_master')
            ->select('debit_credit.debit', 'debit_credit.credit', 'debit_credit.dr_cr', 'ledger_head.ledger_name','debit_credit.remark')
            ->LeftJoin('debit_credit', 'debit_credit.tran_id', '=', 'transaction_master.tran_id')
            ->LeftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
            ->where('transaction_master.tran_id', $id)
            ->where('debit_credit.commission_type', '=', null)
            ->get();
    }

    public function getdebitCreditCommission($id)
    {
        return DB::table('transaction_master')
            ->select('debit_credit.debit', 'debit_credit.credit', 'debit_credit.commission_type', 'debit_credit.commission')
            ->Join('debit_credit', 'debit_credit.tran_id', '=', 'transaction_master.tran_id')
            ->where('transaction_master.tran_id', $id)
            ->where('debit_credit.commission_type', '!=', null)
            ->Where('debit_credit.commission', '!=', 0)
            ->get();
    }

    public function getdebitCreditCommissionProductWise($id)
    {
        return DB::table('transaction_master')
            ->select('debit_credit.debit', 'debit_credit.credit', 'debit_credit.commission_type', 'debit_credit.commission')
            ->Join('debit_credit', 'debit_credit.tran_id', '=', 'transaction_master.tran_id')
            ->where('transaction_master.tran_id', $id)
            ->where('debit_credit.commission_type', '!=', null)
            ->Where('debit_credit.commission', '!=', 0)
            ->Where('debit_credit.comm_level', '=', 1)
            ->get();
    }

    public function tranmasterAndLedger($id)
    {

        return DB::table('transaction_master')
            ->select('transaction_master.tran_id','transaction_master.invoice_no', 'transaction_master.delivery_status', 'transaction_master.ref_no', 'transaction_master.transaction_date', 'transaction_master.narration','ledger_head.alias','ledger_head.ledger_head_id', 'ledger_head.ledger_name', 'ledger_head.mailing_add', 'ledger_head.trade_licence_no', 'ledger_head.credit_limit', 'ledger_head.mobile', 'ledger_head.trade_licence_no', 'ledger_head.tin_certificate', 'ledger_head.bank_cheque', 'ledger_head.national_id','ledger_head.alias', 'voucher_setup.voucher_name', 'voucher_setup.voucher_type_id', 'voucher_setup.commission_is', 'transaction_master.received_amount','debit_credit.remark','transaction_master.gprf','transaction_master.commission_from_date','transaction_master.commission_to_date')
            ->Join('voucher_setup', 'voucher_setup.voucher_id', '=', 'transaction_master.voucher_id')
            ->LeftJoin('debit_credit', 'debit_credit.tran_id', '=', 'transaction_master.tran_id')
            ->LeftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
            ->where('transaction_master.tran_id', $id)
            ->groupBy('transaction_master.tran_id')
            ->first();
    }

    public function tranmasterAndLedgerWithOutDebitCredit($id)
    {
        return DB::table('transaction_master')
            ->select('transaction_master.invoice_no', 'transaction_master.delivery_status', 'transaction_master.ref_no', 'transaction_master.transaction_date', 'transaction_master.narration','ledger_head.alias','ledger_head.ledger_head_id', 'ledger_head.ledger_name', 'ledger_head.mailing_add', 'ledger_head.mobile', 'ledger_head.national_id', 'voucher_setup.voucher_name', 'voucher_setup.voucher_type_id', 'voucher_setup.commission_is')
            ->Join('voucher_setup', 'voucher_setup.voucher_id', '=', 'transaction_master.voucher_id')
            ->LeftJoin('ledger_head', 'transaction_master.ledger_id_optional', '=', 'ledger_head.ledger_head_id')
            ->where('transaction_master.tran_id', $id)
            ->groupBy('transaction_master.tran_id')
            ->first();
    }
    public function voucherType($id)
    {
        return DB::table('transaction_master')
            ->select('voucher_setup.voucher_type_id','remark_is','st_approval')
            ->Join('voucher_setup', 'voucher_setup.voucher_id', '=', 'transaction_master.voucher_id')
            ->where('transaction_master.tran_id', $id)
            ->groupBy('transaction_master.tran_id')
            ->first();
    }
    public function deliveryApproved($id)
    {

        $lockKey = 'delivery-lock-' . date('Y-m-d H:i:s');
                $maxAttempts = 5;
                $orderNumber = null;

                $order_approver = retry($maxAttempts, function () use ($lockKey, $id) {
                    return Cache::lock($lockKey, 10)->block(5, function () use ($id) {
                        DB::beginTransaction();
                        try {
                            $delivery_status = TransactionMaster::where('tran_id', '=', $id)->first()->delivery_status;

                            if ($delivery_status == 0) {
                                $tran = TransactionMaster::findOrFail($id);
                                $tran->delivery_status = 1;
                                $tran->save();

                                $order_approver = new OrderDelivery();
                                $order_approver->tran_id = $id;
                                $order_approver->user_id = Auth::id();
                                $order_approver->order_approve_status = 1;
                                $order_approver->delivery_date = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $order_approver->other_details = json_encode('Approve On: ' . \Carbon\Carbon::now()->format('D, d M Y g:i:s A') . ' By:' . Auth::user()->user_name);
                                $order_approver->save();

                            } elseif ($delivery_status == 1&&(Auth()->user()->user_level == 5)) {
                                $tran = TransactionMaster::findOrFail($id);
                                $tran->delivery_status = 2;
                                $tran->save();

                                $order_approver = new OrderDelivery();
                                $order_approver->tran_id = $id;
                                $order_approver->user_id = Auth::id();
                                $order_approver->order_approve_status = 2;
                                $order_approver->delivery_date = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                                $order_approver->other_details = json_encode('Delivered On: ' . \Carbon\Carbon::now()->format('D, d M Y g:i:s A') . ' By:' . Auth::user()->user_name);
                                $order_approver->save();
                            }

                            DB::commit();
                            return $order_approver;

                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('Error: ' . $e->getMessage());
                            throw $e;
                        }
                    });
                }, 200);

           return $order_approver; 
    }

    public function approvedDetails($status_id)
    {
        return DB::table('order_approver')->where('delivery_status', $status_id)->get();
    }

    public function getSalesOrder($id)
    {
        return DB::table('transaction_master')
            ->select('stock_item.product_name', 'sales_order.qty', 'sales_order.rate', 'sales_order.total', 'unitsof_measure.symbol', 'unitsof_measure.unit_of_measure_id')
            ->Join('sales_order', 'sales_order.tran_id', '=', 'transaction_master.tran_id')
            ->Join('stock_item', 'sales_order.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('transaction_master.tran_id', $id)
            ->get();
    }

    public function tranmasterAndLedgerSalesOrder($id)
    {

        return DB::table('transaction_master')
            ->select('transaction_master.invoice_no', 'transaction_master.delivery_status', 'transaction_master.ref_no', 'transaction_master.transaction_date', 'transaction_master.narration', 'ledger_head.ledger_name', 'ledger_head.mailing_add', 'ledger_head.trade_licence_no', 'ledger_head.credit_limit', 'ledger_head.mobile', 'ledger_head.trade_licence_no', 'ledger_head.tin_certificate', 'ledger_head.bank_cheque', 'ledger_head.national_id', 'voucher_setup.voucher_name', 'voucher_setup.voucher_type_id', 'voucher_setup.commission_is')
            ->Join('voucher_setup', 'voucher_setup.voucher_id', '=', 'transaction_master.voucher_id')
            ->LeftJoin('sales_order', 'sales_order.tran_id', '=', 'transaction_master.tran_id')
            ->LeftJoin('ledger_head', 'sales_order.credit_ledger_id', '=', 'ledger_head.ledger_head_id')
            ->where('transaction_master.tran_id', $id)
            ->groupBy('transaction_master.tran_id')
            ->first();
    }
    public function DestinationGodown($id)
    {
        return DB::table('stock_in')
            ->select('godowns.godown_id', 'godowns.godown_name','godowns.address')
            ->leftJoin('stock_item', 'stock_in.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_in.godown_id', '=', 'godowns.godown_id')
            ->where('stock_in.tran_id', $id)
            ->first();
    }

     public function StockTransferAddress($id)
    {
        return DB::table('transaction_master')
            ->select('distribution_center.address')
            ->leftJoin('distribution_center', 'transaction_master.dis_cen_id', '=', 'distribution_center.dis_cen_id')
            ->where('transaction_master.tran_id', $id)
            ->first();
    }
    
    public function getCreditReceived($id)
    {
        return DB::table('transaction_master')
            ->select(DB::raw('SUM(debit_credit.credit) AS credit'),'debit_credit.dr_cr', 'ledger_head.ledger_name','ledger_head.mailing_add')
            ->LeftJoin('debit_credit', 'debit_credit.tran_id', '=', 'transaction_master.tran_id')
            ->LeftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
            ->where('transaction_master.tran_id', $id)
            ->where('debit_credit.dr_cr', '=','Cr' )
            ->groupBy('transaction_master.tran_id')
            ->first();
    }

    public function purchaseOrder($id)
    {
        
        return DB::table('order_requisition')
            ->select('stock_item.product_name','unitsof_measure.symbol', 'unitsof_measure.unit_of_measure_id','order_requisition.remark', 'order_requisition.order_tran_id', 'order_requisition.stock_item_id', 'order_requisition.stock_item_id', 'order_requisition.qty', 'order_requisition.rate', 'order_requisition.total')
            ->leftJoin('stock_item', 'order_requisition.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('order_requisition.order_tran_id', $id)
            ->get();
    }
    public function purchaseOrdertransaction($id)
    {
        return DB::table('order_requisition_transaction_master')
            ->select('date','reference_no','invoice_no','narration','ledger_head.ledger_name','ledger_head.mailing_add','voucher_setup.voucher_name','remark_is')
            ->LeftJoin('ledger_head', 'order_requisition_transaction_master.ledger_id', '=', 'ledger_head.ledger_head_id')
            ->Join('voucher_setup', 'voucher_setup.voucher_id', '=', 'order_requisition_transaction_master.voucher_id')
            ->where('order_requisition_transaction_master.id', $id)
            ->first();
    }

    public function stockItemCommission($id,$request){
                 
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
                                        INNER JOIN `stock_out`
                                        ON         transaction_master.tran_id=stock_out.tran_id
                                        INNER JOIN  stock_item
                                        ON         stock_out.stock_item_id=stock_item.stock_item_id
                                        INNER JOIN stock_item_commission
                                        ON         stock_item.stock_item_id=stock_item_commission.stock_item_id   AND stock_item_commission.tran_id=:id
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        INNER JOIN debit_credit  ON  transaction_master.tran_id=debit_credit.tran_id
                                        WHERE      voucher_setup.voucher_type_id=19
                                        AND       debit_credit.ledger_head_id=:ledger_head_id
                                        AND       transaction_master.transaction_date 
                                        BETWEEN   :from_date AND        :to_date
                                        
                                        GROUP BY   stock_out.stock_item_id
                                        ORDER BY   stock_item.product_name DESC
                                            ) AS t1
                        ON        stock_group.stock_group_id=t1.stock_group_id
                        ORDER BY  stock_group.stock_group_name DESC, t1.product_name DESC
        ";
        $params['from_date'] = $request->from_date;
        $params['to_date'] = $request->to_date;
        $params['ledger_head_id'] =$request->ledger_head_id;
        $params['id'] =$id;
       $data= DB::select($query,$params);
       $group_chart_object_to_array = json_decode(json_encode($data, true), true);
        $tree_data = $this->tree->buildTree($group_chart_object_to_array, 0, 0, 'stock_group_id', 'under', 'stock_item_id');
        $commission_ledger_voucher=$this->voucherCommissionRepository->calculateGroupTotals($tree_data);
        $sum_of_children=$this->voucherCommissionRepository->calculateSumOfChildren($commission_ledger_voucher);
        return ['commission_ledger_voucher'=>$commission_ledger_voucher,'sum_of_children'=>$sum_of_children];
   
}
}
