<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItemCommissionVoucher extends Model
{
    use HasFactory;

    protected $table = 'stock_item_commission';

    protected $graured = ['stock_comm_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'stock_comm_id';

    public $timestamps = false;
}
