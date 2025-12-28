<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UpdateUniqueInvoiceValidation implements ValidationRule
{

    protected $voucherId;
    protected $invoice;
    protected $tran_id;
    protected $transaction_date;

    /**
     * Constructor to initialize the parameters.
     *
     * @param  int  $voucherId
     * @param  int|null  $invoice
     */
    public function __construct($tran_id,$invoice,$voucherId,$transaction_date)
    {
        $this->tran_id = $tran_id;
        $this->voucherId = $voucherId;
        $this->invoice = $invoice;
        $this->transaction_date=$transaction_date;

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
            $query->whereRaw('MONTH(transaction_date) = ?', [date('m', strtotime($this->transaction_date))]);
        }elseif($voucher->auto_reset_invoice==3){
            $query->where('voucher_id',$this->voucherId);
            $query->whereRaw('YEAR(transaction_date) = ?', [date('Y', strtotime($this->transaction_date))]);
        }
        if ($this->tran_id) {
            $query->where('tran_id', '!=', $this->tran_id);
        }
       
        if ($query->exists()) {
            $fail("The must be unique for the specified Invoice Number.");
        }
    }
}
