<?php

namespace App\Repositories\Backend\Approve;

use App\Models\OrderDelivery;
use App\Models\TransactionMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApproveRepository implements ApproveInterface
{
    public function getApproveOfIndex($request)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $delivery_status = $request->delivery_status=='' ? "" : "delivery_status=$request->delivery_status AND";
        if ($request->voucher_id == 0) {
            $voucher_sql = '';
        } else {
            if (strpos($request->voucher_id, 'v') !== false) {
                $voucher_type_id = str_replace('v', '', $request->voucher_id);

                $voucher_sql = "AND voucher_setup.voucher_type_id='$voucher_type_id'";
            } else {
                $voucher_sql = "AND transaction_master.voucher_id='$request->voucher_id'";
            }
        }

        return DB::select(
            "SELECT
                        transaction_master.tran_id,
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
                        ledger_head.credit_limit,
                        voucher_setup.voucher_name,
                        debit_credit.dr_cr,
                        godowns.godown_name,
                        IF(voucher_setup.voucher_type_id=19 OR voucher_setup.voucher_type_id=24,
                            (SELECT CONCAT(group_chart.nature_group,',',SUM(debit.debit),',',SUM(debit.credit))
                                FROM group_chart
                                LEFT JOIN ledger_head ON group_chart.group_chart_id=ledger_head.group_id
                                LEFT JOIN debit_credit AS  debit ON ledger_head.ledger_head_id= debit.ledger_head_id
                            WHERE  debit.ledger_head_id=debit_credit.ledger_head_id
                        ) ,'') AS debit_credit_sum,
                        IF(transaction_master.delivery_status=1 OR transaction_master.delivery_status=2,
                            (SELECT other_details
                                FROM order_approver
                            WHERE order_approver.tran_id=transaction_master.tran_id AND order_approver.order_approve_status=transaction_master.delivery_status
                        ) ,'') AS order_approver
                    FROM (transaction_master
                            INNER JOIN voucher_setup
                            ON voucher_setup.voucher_id=transaction_master.voucher_id
                        )
                    LEFT OUTER JOIN
                    (debit_credit INNER JOIN ledger_head
                        ON ledger_head.ledger_head_id=debit_credit.ledger_head_id
                    )
                    ON (debit_credit.tran_id=transaction_master.tran_id)
                    INNER JOIN stock_out ON transaction_master.tran_id=stock_out.tran_id
                    INNER JOIN godowns ON stock_out.godown_id=godowns.godown_id
                    WHERE $delivery_status voucher_setup.voucher_type_id IN(19,22,24) AND transaction_master.transaction_date BETWEEN '$from_date' AND '$to_date' $voucher_sql
                    Group by transaction_master.tran_id
                    ORDER BY transaction_master.tran_id DESC"
        );
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
            ->select('stock_in.stock_in_id', 'stock_in.tran_id', 'stock_in.stock_item_id', 'stock_in.qty', 'stock_in.rate', 'stock_in.total', 'stock_in.remark', 'stock_item.product_name', 'godowns.godown_id', 'godowns.godown_name', 'unitsof_measure.symbol','unitsof_measure.unit_of_measure_id')
            ->leftJoin('stock_item', 'stock_in.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_in.godown_id', '=', 'godowns.godown_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('stock_in.tran_id', $id)
            ->get();
    }

    public function getStockOut($id)
    {
        return DB::table('transaction_master')
            ->select('stock_item.product_name', 'stock_out.qty', 'stock_out.rate', 'stock_out.total', 'godowns.godown_name', 'unitsof_measure.symbol','unitsof_measure.unit_of_measure_id')
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
            ->select('debit_credit.debit', 'debit_credit.credit', 'debit_credit.dr_cr', 'ledger_head.ledger_name')
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
            ->select('transaction_master.invoice_no', 'transaction_master.delivery_status', 'transaction_master.ref_no', 'transaction_master.transaction_date', 'transaction_master.narration', 'ledger_head.ledger_name', 'ledger_head.mailing_add','ledger_head.trade_licence_no','ledger_head.credit_limit', 'ledger_head.mobile','ledger_head.trade_licence_no','ledger_head.tin_certificate','ledger_head.bank_cheque','ledger_head.national_id', 'voucher_setup.voucher_name', 'voucher_setup.voucher_type_id', 'voucher_setup.commission_is')
            ->Join('voucher_setup', 'voucher_setup.voucher_id', '=', 'transaction_master.voucher_id')
            ->LeftJoin('debit_credit', 'debit_credit.tran_id', '=', 'transaction_master.tran_id')
            ->LeftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
            ->where('transaction_master.tran_id', $id)
            ->groupBy('transaction_master.tran_id')
            ->first();
    }

    public function deliveryApproved($id)
    {

        $tran = TransactionMaster::findOrFail($id);
        $tran->delivery_status = (($tran->delivery_status == 0) ? 1 : 2);
        $tran->save();

        $order_approver = new OrderDelivery();
        $order_approver->tran_id = $id;
        $order_approver->user_id = Auth::id();
        $order_approver->order_approve_status = $tran->delivery_status;
        $order_approver->delivery_date = \Carbon\Carbon::now()->format('D, d M Y g:i:s A');
        if ($tran->delivery_status == 1) {
            $order_approver->other_details = json_encode('Approve On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name);
        } elseif ($tran->delivery_status == 2) {
            $order_approver->other_details = json_encode('Delivered On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').' By:'.Auth::user()->user_name);
        }
        $order_approver->save();

        return $order_approver;

    }

    public function approvedDetails($status_id)
    {
        return DB::table('order_approver')->where('delivery_status', $status_id)->get();
    }
}
