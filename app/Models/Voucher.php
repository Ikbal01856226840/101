<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'voucher_setup';

    protected $graured = ['voucher_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'voucher_id';

    public function voucher_type()
    {
        return $this->belongsTo(VoucherType::class, 'voucher_type_id');
    }
}
