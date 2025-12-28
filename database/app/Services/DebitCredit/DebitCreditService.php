<?php

namespace App\Services\DebitCredit;

use App\Models\DebitCredit;

class DebitCreditService
{
    public function debitCreditStore($tran_id, $ledger_head_id, $debit, $credit, $dr_cr, $commission = null, $commission_type = null)
    {
        $debit_credit_data = new DebitCredit();
        $debit_credit_data->tran_id = $tran_id;
        $debit_credit_data->ledger_head_id = $ledger_head_id;
        $debit_credit_data->debit = (float) $debit ?? 0;
        $debit_credit_data->credit = (float) $credit ?? 0;
        $debit_credit_data->dr_cr = $dr_cr;
        $debit_credit_data->commission = (float) $commission ?? 0;
        $debit_credit_data->commission_type = $commission_type ?? 0;
        $debit_credit_data->save();

        return $debit_credit_data;
    }

    public function debitCreditUpdate($debit_credit_id, $tran_id, $ledger_head_id, $debit, $credit, $dr_cr, $commission = null, $commission_type = null)
    {
        $debit_credit_update = DebitCredit::find($debit_credit_id);
        $debit_credit_update->tran_id = $tran_id;
        $debit_credit_update->ledger_head_id = $ledger_head_id;
        $debit_credit_update->debit = (float) $debit ?? 0;
        $debit_credit_update->credit = (float) $credit ?? 0;
        $debit_credit_update->dr_cr = $dr_cr;
        $debit_credit_update->commission = (float) $commission ?? 0;
        $debit_credit_update->commission_type = $commission_type ?? 0;
        $debit_credit_update->save();

        return $debit_credit_update;
    }

    public function debit_sum($tran_id)
    {
        return DebitCredit::where('tran_id', $tran_id)->sum('debit');
    }
}
