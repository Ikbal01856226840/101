<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class StoreUniqueInvoiceValidation implements ValidationRule
{

    protected $voucherId;
    protected $invoice;

    /**
     * Constructor to initialize the parameters.
     *
     * @param  int  $voucherId
     * @param  int|null  $invoice
     */
    public function __construct($invoice,$voucherId)
    {
        $this->voucherId = $voucherId;
        $this->invoice = $invoice;

    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $voucher= DB::table('voucher_setup')->where('voucher_id',$this->voucherId)->first();
        $query = DB::table('transaction_master')
                    ->where('invoice_no',$this->invoice);

        if($voucher->auto_reset_invoice==1){
            $query->where('voucher_id',$this->voucherId);
        }elseif($voucher->auto_reset_invoice==2){
            $query->where('voucher_id',$this->voucherId);
            $query->whereRaw('MONTH(entry_date) = ?', [date('m')]);
        }elseif($voucher->auto_reset_invoice==3){
            $query->where('voucher_id',$this->voucherId);
            $query->whereRaw('YEAR(entry_date) = ?', [date('Y')]);
        }
        // If a record exists, the validation fails
        if ($query->exists()) {
            $fail("The must be unique for the specified Invoice Number.");
        }
    }
}
