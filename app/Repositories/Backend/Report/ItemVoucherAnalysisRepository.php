<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class ItemVoucherAnalysisRepository implements ItemVoucherAnalysisInterface
{
    public function getItemVoucherAnalyisOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $stock_item_id = $request->stock_item_id;

        $godown_id=implode(',',$request->godown_id);
        // dd($godown_id);
        $godown_in = $godown_id==0?'':" stock_in.godown_id IN($godown_id) AND";
        $godown_out = $godown_id==0?'':" stock_out.godown_id IN($godown_id) AND";
        // stock in
        if ($request->purchase) {
            $purchase = $this->stock_in_analysis_query($request->purchase, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_in);
        }if ($request->grn) {
            $grn = $this->stock_in_analysis_query($request->grn, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_in);
        }if ($request->purchase_return) {
            $purchase_return = $this->stock_in_analysis_query($request->purchase_return, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_in);
        }if ($request->journal_in) {
            $journal_in = $this->stock_in_analysis_query($request->journal_in, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_in);
        }if ($request->stock_journal_in) {
            $stock_journal_in = $this->stock_in_analysis_query($request->stock_journal_in, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_in);
        }
        // stock out
        if ($request->sales_return) {
            $sales_return = $this->stock_out_analysis_query($request->sales_return, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_out);
        }if ($request->gtn) {
            $gtn = $this->stock_out_analysis_query($request->gtn, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_out);
        }if ($request->sales) {
            $sales = $this->stock_out_analysis_query($request->sales, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_out);
        }if ($request->journal_out) {
            $journal_out = $this->stock_out_analysis_query($request->journal_out, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_out);
        }if ($request->stock_journal_out) {
            $stock_journal_out = $this->stock_out_analysis_query($request->stock_journal_out, $stock_item_id, $from_date, $to_date, $request->ledger_id,$godown_out);
        }
        $unit_of_measure=DB::table('stock_item')->select('symbol')->join('unitsof_measure', 'unitsof_measure.unit_of_measure_id','=','stock_item.unit_of_measure_id')->where('stock_item.stock_item_id',$request->stock_item_id)->first();
       
        return ['unit_of_measure'=>$unit_of_measure??0,'purchase' => $purchase ?? '', 'grn' => $grn ?? '', 'purchase_return' => $purchase_return ?? '', 'journal_in' => $journal_in ?? '', 'stock_journal_in' => $stock_journal_in ?? '', 'sales_return' => $sales_return ?? '', 'gtn' => $gtn ?? '', 'sales' => $sales ?? '', 'journal_out' => $journal_out ?? '', 'stock_journal_out' => $stock_journal_out ?? ''];

    }

    public function stock_in_analysis_query($voucher_id, $stock_item_id, $from_date, $to_date, $ledger_head_id,$godown_in)
    {
           $params = [];
           $ledger_head__sql='';
           if(!empty($ledger_head_id)){
             $ledger_head__sql= $ledger_head_id == 0 ? '' : "ledger_head.ledger_head_id=:ledger_head_id AND";
             $params['ledger_head_id'] = $ledger_head_id;
           }
      
                      $query="SELECT    transaction_master.invoice_no,
                                        transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        voucher_setup.voucher_name,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        stock_in.stock_in_qty,
                                        stock_in.stock_in_total
                                FROM       transaction_master
                                INNER JOIN
                                        (
                                                SELECT   tran_id,
                                                         Sum(stock_in.qty) AS stock_in_qty,
                                                         Sum(stock_in.total) AS stock_in_total
                                                FROM     stock_in
                                                WHERE $godown_in stock_in.stock_item_id=:stock_item_id   GROUP BY   tran_id) AS stock_in
                               
                                ON         transaction_master.tran_id=stock_in.tran_id
                                LEFT JOIN  debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                LEFT  JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE       $ledger_head__sql voucher_setup.voucher_type_id IN(:voucher_id)
                                AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                GROUP BY   transaction_master.tran_id
                               
              ";
            $params['voucher_id'] = $voucher_id; 
            $params['stock_item_id'] = $stock_item_id;
            $params['from_date'] = $from_date;
            $params['to_date'] = $to_date;

           return DB::select($query,$params);
                               
            
    }

    public function stock_out_analysis_query($voucher_id, $stock_item_id, $from_date, $to_date, $ledger_head_id,$godown_out)
    {
           $params = [];
           $ledger_head__sql='';
           if(!empty($ledger_head_id)){
             $ledger_head__sql= $ledger_head_id == 0 ? '' : "ledger_head.ledger_head_id=:ledger_head_id AND";
             $params['ledger_head_id'] = $ledger_head_id;
           }

           $query="SELECT      transaction_master.invoice_no,
                                        transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        voucher_setup.voucher_name,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        stock_out.stock_out_qty,
                                        stock_out.stock_out_total

                                FROM       transaction_master
                                INNER JOIN
                                        (
                                                SELECT   tran_id,
                                                         Sum(stock_out.qty) AS stock_out_qty,
                                                         Sum(stock_out.total) AS stock_out_total
                                                FROM     stock_out
                                                WHERE  $godown_out stock_out.stock_item_id=:stock_item_id   GROUP BY   tran_id) AS stock_out
                                ON         transaction_master.tran_id=stock_out.tran_id
                                LEFT  JOIN debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                LEFT  JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      $ledger_head__sql voucher_setup.voucher_type_id IN(:voucher_id)
                                AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                GROUP BY   transaction_master.tran_id
        ";
         $params['voucher_id'] = $voucher_id; 
         $params['stock_item_id'] = $stock_item_id;
         $params['from_date'] = $from_date;
         $params['to_date'] = $to_date;

        return DB::select($query,$params);
    }
}
