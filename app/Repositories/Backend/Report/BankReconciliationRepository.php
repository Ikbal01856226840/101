<?php

namespace App\Repositories\Backend\Report;

use App\Models\TransactionMaster;
use Illuminate\Support\Facades\DB;

class BankReconciliationRepository implements BankReconciliationInterface
{

    public function getBankReconciliationOfIndex($request = null)
    {

        $params = [];
        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        }
        if ($request->sort_by == 1) {
            $sort_by = " transaction_master.transaction_date ASC,transaction_master.tran_id ASC";

        } else {
            $sort_by = ($request->sort_by == 2 ? 'debit ASC' : ($request->sort_by == 3 ? 'credit ASC' : ($request->sort_by == 4 ? 'voucher_name ASC' : ($request->sort_by == 5 ? 'ledger_name ASC' : ''))));
        }
        $query="SELECT
                            transaction_master.tran_id,
                            transaction_master.invoice_no,
                            transaction_master.transaction_date,
                            transaction_master.voucher_id,
                            transaction_master.narration,
                            voucher_setup.voucher_type_id,
                            debit_credit.ledger_head_id,
                            ledger_head.ledger_name,
                            voucher_setup.voucher_name,
                            transaction_master.narration,
                            debit_credit.dr_cr,
                            debit_credit.debit,
                            debit_credit.credit,
                            transaction_master.bank_date

                        FROM (transaction_master
                                INNER JOIN voucher_setup
                                ON voucher_setup.voucher_id=transaction_master.voucher_id
                            )
                        LEFT OUTER JOIN
                        (debit_credit INNER JOIN ledger_head
                            ON ledger_head.ledger_head_id=debit_credit.ledger_head_id
                        )
                        ON (debit_credit.tran_id=transaction_master.tran_id)
                        WHERE   ledger_head.ledger_head_id=:ledger_head_id AND transaction_master.transaction_date BETWEEN :from_date AND :to_date
                        Group by transaction_master.tran_id
                        ORDER BY $sort_by
                    ";
                $params['from_date'] = $from_date;
                $params['to_date'] = $to_date;
                $params['ledger_head_id'] = $request->ledger_id;
        return DB::select($query,$params);
    }

    public function bankReconciliationStore($request){
    
       return  TransactionMaster::where('tran_id',$request->tran_id)->update([
            'bank_date' => $request->bank_date,
        ]);
    }
}
