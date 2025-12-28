<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;

class StockItemAnalysisRepository implements StockItemAnalysisInterface
{
    public function stockItemAnalysisOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $stock_item_id = $request->stock_item_id;

        // stock in
        if ($request->purchase) {
            $purchase = $this->stock_in_analysis_query($request->purchase, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        if ($request->grn) {
            $grn = $this->stock_in_analysis_query($request->grn, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        if ($request->purchase_return) {
            if(company()->sales_return==2){
                $purchase_return = $this->stock_in_analysis_query($request->purchase_return, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
            }else if(company()->sales_return==1){
                $purchase_return = $this->stock_out_analysis_query($request->purchase_return, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
            }
        }
        if ($request->journal_in) {
            $journal_in = $this->stock_in_analysis_query($request->journal_in, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        if ($request->stock_journal_in) {
            $stock_journal_in = $this->stock_in_analysis_query($request->stock_journal_in, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        // stock out
        if ($request->sales_return) {
              if(company()->sales_return==2){
                $sales_return = $this->stock_out_analysis_query($request->sales_return, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
              }else if(company()->sales_return==1){
                  $sales_return = $this->stock_in_analysis_query($request->sales_return, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
              }
        }
        if ($request->gtn) {
            $gtn = $this->stock_out_analysis_query($request->gtn, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        if ($request->sales) {
            $sales = $this->stock_out_analysis_query($request->sales, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        if ($request->journal_out) {
            $journal_out = $this->stock_out_analysis_query($request->journal_out, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        if ($request->stock_journal_out) {
            $stock_journal_out = $this->stock_out_analysis_query($request->stock_journal_out, $request->godown_id, $stock_item_id, $from_date, $to_date, $request->sort_by, $request->stort_type);
        }
        $unit_of_measure = DB::table('stock_item')->select('symbol')->join('unitsof_measure', 'unitsof_measure.unit_of_measure_id', '=', 'stock_item.unit_of_measure_id')->where('stock_item.stock_item_id', $request->stock_item_id)->first();

        return ['unit_of_measure' => $unit_of_measure, 'purchase' => $purchase ?? '', 'grn' => $grn ?? '', 'purchase_return' => $purchase_return ?? '', 'journal_in' => $journal_in ?? '', 'stock_journal_in' => $stock_journal_in ?? '', 'sales_return' => $sales_return ?? '', 'gtn' => $gtn ?? '', 'sales' => $sales ?? '', 'journal_out' => $journal_out ?? '', 'stock_journal_out' => $stock_journal_out ?? ''];
    }

    public function stock_in_analysis_query($voucher_id, $godown, $stock_item_id, $from_date, $to_date, $sort_by, $stort_type)
    {
        $params = [];

        // Prepare godown
        if (!empty($godown[0])) {

            $godown_in_id = $godown; // Array of IDs
            $placeholders_in = implode(',', array_map(function ($i) {
                return ':godown_id_' . $i;
            }, array_keys($godown_in_id)));

            $godown_sql = "stock_in.godown_id IN ($placeholders_in) AND";

            foreach ($godown_in_id as $i => $id) {
                $params['godown_id_' . $i] = $id;
            }
        } else {
            $godown_sql = "";
        }

        $sql_sort_by = ''; // Initialize $sort_by
        if ($sort_by == 1) {
            $sql_sort_by = "ORDER BY ledger_head.ledger_name";
        } 
        elseif($sort_by == 2) {
            $sql_sort_by = "ORDER BY stock_in_qty";
        } elseif ($sort_by == 3) {
            $sql_sort_by = "ORDER BY stock_in_total";
        }

        // Initialize $sort_type
        $sort_type = '';

        if ($stort_type == 1) {
            $sort_type = " ASC";
        } elseif ($stort_type == 2) {
            $sort_type = " DESC";
        }

        if($stort_type==3){
            $final_sort ='';
        }else{
            $final_sort = trim($sql_sort_by . ' ' .($sql_sort_by ? $sort_type:""));
        }
        $groupBy=$voucher_id==21?", transaction_master.voucher_id":"";

       
              $query = "     SELECT    transaction_master.invoice_no,
                                        transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        transaction_master.voucher_id,
                                        voucher_setup.voucher_name,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        Sum(stock_in.qty)   AS stock_in_qty,
                                        Sum(stock_in.total) AS stock_in_total
                                FROM       transaction_master
                                INNER JOIN stock_in ON transaction_master.tran_id=stock_in.tran_id
                                LEFT  JOIN
                                        (
                                                SELECT   tran_id,
                                                        ledger_head_id
                                                FROM     debit_credit
                                                GROUP BY tran_id ) AS debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                LEFT JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      $godown_sql stock_in.stock_item_id=:stock_item_id
                                AND        voucher_setup.voucher_type_id IN(:voucher_id)
                                AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                GROUP BY   ledger_head.ledger_head_id  $groupBy $final_sort
                                
              ";

        $params['stock_item_id'] = $stock_item_id;
        $params['voucher_id'] = $voucher_id;
        $params['from_date'] = $from_date;
        $params['to_date'] = $to_date;
        return DB::select($query, $params);
    }

    public function stock_out_analysis_query($voucher_id, $godown, $stock_item_id, $from_date, $to_date, $sort_by, $stort_type)
    {
        $params = [];
        // Prepare godown
        if (!empty($godown[0])) {
            $godown_out_id = $godown; // Array of IDs
            $placeholders_out = implode(',', array_map(function ($i) {
                return ':godown_id_' . $i;
            }, array_keys($godown_out_id)));

            $godown_sql = "stock_out.godown_id IN ($placeholders_out) AND";

            foreach ($godown_out_id as $i => $id) {
                $params['godown_id_' . $i] = $id;
            }
        } else {
            $godown_sql = "";
        }

        $sql_sort_by = ''; // Initialize $sort_by
        if ($sort_by == 1) {
            $sql_sort_by = "ORDER BY ledger_head.ledger_name";
        } 
        elseif($sort_by == 2) {
            $sql_sort_by = "ORDER BY stock_out_qty";
        } elseif ($sort_by == 3) {
            $sql_sort_by = "ORDER BY stock_out_total";
        }

        // Initialize $sort_type
        $sort_type = '';

        if ($stort_type == 1) {
            $sort_type = "ASC";
        } elseif ($stort_type == 2) {
            $sort_type = "DESC";
        }

        // Combine the sorting clauses
        if($stort_type==3){
            $final_sort ='';
        }else{
            $final_sort = trim($sql_sort_by . ' ' .($sql_sort_by ? $sort_type:""));
        }

        $groupBy=$voucher_id==21?", transaction_master.voucher_id":"";
        
        $query = "    SELECT      transaction_master.invoice_no,
                                        transaction_master.tran_id,
                                        transaction_master.transaction_date,
                                        voucher_setup.voucher_name,
                                        voucher_setup.voucher_type_id,
                                        ledger_head.ledger_name,
                                        ledger_head.ledger_head_id,
                                        SUM(stock_out.qty) AS  stock_out_qty,
                                        SUM(stock_out.total) AS  stock_out_total

                                FROM       transaction_master
                                INNER JOIN stock_out
                                ON         transaction_master.tran_id=stock_out.tran_id
                                LEFT  JOIN (
                                                SELECT   tran_id,
                                                        ledger_head_id
                                                FROM     debit_credit
                                                GROUP BY tran_id ) AS debit_credit
                                ON         transaction_master.tran_id=debit_credit.tran_id
                                LEFT JOIN ledger_head
                                ON         ledger_head.ledger_head_id=debit_credit.ledger_head_id
                                LEFT JOIN  voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                
                                WHERE     $godown_sql stock_out.stock_item_id=:stock_item_id
                                AND        voucher_setup.voucher_type_id IN(:voucher_id)
                                AND        transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                                GROUP BY   ledger_head.ledger_head_id $groupBy  $final_sort
                              
        ";

        $params['stock_item_id'] = $stock_item_id;
        $params['voucher_id'] = $voucher_id;
        $params['from_date'] = $from_date;
        $params['to_date'] = $to_date;
        return DB::select($query, $params);
    }
}
