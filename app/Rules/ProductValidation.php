<?php

namespace App\Rules;

use App\Services\Voucher_setup\Voucher_setup;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductValidation implements ValidationRule
{
    protected $data; // Holds additional request data
    protected $errors = []; // Stores validation errors for each row
    protected $voucher_setup;
    /**
     * Constructor to accept additional request data.
     *
     * @param array $data
     */
    public function __construct(array $data,Voucher_setup $voucher_setup)
    {
        $this->data = $data;
        $this->voucher_setup=$voucher_setup;
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

        $productNames = $this->data['product_name'] ?? [];
        $godownIds = $this->data['godown_id'] ?? [];
        $productIds = $this->data['product_id'] ?? [];
        $qty = $this->data['qty'] ?? [];
        $rate = $this->data['rate'] ?? [];
        $total = $this->data['amount'] ?? [];
        $stock=$this->data['stock'] ?? [];
        $check_current_stock=$this->data['check_current_stock']??'';
        $actual_qtys=$this->data['actual_qty']??[];
        $row_wise_qty_is=$this->data['row_wise_qty_is']??1;

        $consumeStock=[];
        foreach ($productNames as $index => $productName) {

            $row = $index + 1;

            $godownId = $godownIds[$index] ?? null;
            $productId = $productIds[$index] ?? null;
            $qtys = $qty[$index] ?? null;
            $rates = $rate[$index] ?? null;
            $totals = $total[$index] ?? null;
            $stocks=$stock[$index] ?? null;
            $actual_qty=$actual_qtys[$index] ?? null;

            if (!empty($productName)) {
                if (empty($godownId)) {
                    $this->errors[] = "The godown name at Row Number $row is required.<br>";
                }
                if (empty($productId)) {
                    $this->errors[] = "The product name at Row Number $row is required.<br>";
                }
                if (abs(((double)$qtys * $rates) - ((double)$totals)) > 1) {
                    $this->errors[] = "Row Number $row calculation does not match.<br>";
                }
                if (empty($qtys)&&$row_wise_qty_is==0) {
                    $this->errors[] = "Row Number $row  quantity 0 is not allowed .<br>";
                }
                if (!empty($stocks)&&$check_current_stock==0) {
                    if(!array_key_exists($productId, $consumeStock)){
                        $consumeStock[$productId]=0;
                    }
                    $consumeStock[$productId]=$consumeStock[$productId]+$qtys;
                    $currentQty = $this->voucher_setup->stock_in_stock_out_sum_qty($productId,$godownId);

                    if($consumeStock[$productId]>($currentQty+($actual_qty??0))){
                        $this->errors[] = "Row Number $row And product name $productName quantity is not available.<br>";
                    }
                }
            } else {
                if (((double)$totals) > 0 && empty($productId)) {
                    $this->errors[] = "The product name at Row Number $row is required.<br>";
                }
            }

        }

        // Pass errors to the $fail closure
        foreach ($this->errors as $error) {
            $fail($error);
        }
    }


}
