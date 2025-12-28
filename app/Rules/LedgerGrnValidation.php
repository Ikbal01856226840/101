<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LedgerGrnValidation implements ValidationRule
{
    protected $data; // Holds additional request data
    protected $errors = []; // Stores validation errors for each row
    /**
     * Constructor to accept additional request data.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $get_commission=$this->data['get_commission']??[];
        $commision_cal=$this->data['commision_cal']??[];
        $commission_ledger_id=$this->data['commission_ledger_id']??[];
        $debit_ledger_id=$this->data['debit_ledger_id']??[];
        $credit_ledger_id=$this->data['credit_ledger_id']??[];
        $total_amount=$this->data['total_credit']??0;
        $get_without_commission=$this->data['get_without_commission']??0;
        
        $total_debit = 0;
        $total_credit = 0;
        if (!empty($get_commission)) {
            foreach ($get_commission as $i => $commissionAmount) {
                if (!empty($commissionAmount)) {
                        $commission_ledger_ids = $commission_ledger_id[$i] ?? null;
                        $isDebit = in_array($commision_cal[$i], [2, 4]);
                        $total_debit+= $isDebit ? (double)$commissionAmount : 0;
                        $total_credit += $isDebit ? 0 : (double)$commissionAmount;
                        empty($commission_ledger_ids) ? $this->errors[] = "The commission ledger empty  at Row Number " . ($i + 1) . " is required.<br>" : '';
                }
            }
        }

            $total_debit+= (float) $total_amount  ?? 0;
            $total_credit+= (float) $get_without_commission ?? 0;

            if (empty($debit_ledger_id)) {
                 $this->errors[] = "The Purchase Ledger is empty <br>";
            }

          
            if (empty($credit_ledger_id)) {
               $this->errors[] = "The Party's A/C Name is empty <br>";
            }
            
          if (abs(((double)$total_debit)-((double)$total_credit))> 1) {
            $this->errors[] = "Total debit credit calculation does not equal.<br>";
          }
        // Pass errors to the $fail closure
        foreach ($this->errors as $error) {
            $fail($error);
        }
    }


}
