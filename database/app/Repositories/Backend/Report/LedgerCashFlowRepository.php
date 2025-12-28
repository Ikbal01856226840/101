<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class LedgerCashFlowRepository implements LedgerCashFlowInterface
{
    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getLedgerCashFlowOfIndex($request = null)
    {

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $ledger_id = $request->ledger_id;

        return DB::select(
                          "SELECT transaction_master.tran_id,
                                    transaction_master.invoice_no,
                                    transaction_master.transaction_date,
                                    transaction_master.user_id,
                                    transaction_master.voucher_id,
                                    voucher_setup.voucher_type_id,
                                    debit_credit.debit_credit_id,
                                    debit_credit.ledger_head_id,
                                    ledger_head.ledger_name,
                                    voucher_setup.voucher_name,
                                    Sum(debit_credit.debit)  AS sum_debit,
                                    Sum(debit_credit.credit) AS sum_credit
                            FROM   transaction_master
                                    LEFT JOIN voucher_setup
                                        ON voucher_setup.voucher_id = transaction_master.voucher_id
                                    LEFT JOIN debit_credit
                                        ON debit_credit.tran_id = transaction_master.tran_id
                                    LEFT JOIN ledger_head
                                        ON ledger_head.ledger_head_id = debit_credit.ledger_head_id
                            WHERE   ledger_head.ledger_head_id = '$ledger_id'
                                    AND transaction_master.transaction_date BETWEEN
                                        '$from_date' AND '$to_date'
                            GROUP  BY transaction_master.tran_id
                            ORDER  BY transaction_master.tran_id DESC
                        "
        );

    }
}
