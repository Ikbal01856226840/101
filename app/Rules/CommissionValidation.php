<?php

namespace App\Rules;

use App\Services\Voucher_setup\Voucher_setup;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CommissionValidation implements ValidationRule
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
        
        if (!is_array($value)) {
            $fail("The $attribute field must be an array.");
            return;
        }

        $commission_ledger_id = $this->data['commission_ledger_id'] ?? [];
        $party_ledger_id = $this->data['party_ledger_id'] ?? [];
        $commission_amount = $this->data['commission_amount'] ?? [];
        $total_commission_per = $this->data['total_commission_per'] ?? 0;
         $total_credit = 0;

        foreach ($commission_amount as $commissionAmount) {
               $total_credit += (double)$commissionAmount;
        }
        if (empty($commission_ledger_id)) {
                 $this->errors[] = "The Commission Ledger is empty <br>";
        }
        if (empty($party_ledger_id)) {
            $this->errors[] = "The Party's A/C Name is empty <br>";
        }
            
        if (abs(((double)$total_commission_per)-((double)$total_credit))>3) {
            $this->errors[] = "Total debit credit calculation does not equal.<br>";
        }
         if (empty(((double)$total_credit))) {
            $this->errors[] = "Total amount is not Zero.<br>";
        }
        // Pass errors to the $fail closure
        foreach ($this->errors as $error) {
            $fail($error);
        }
    }


}
