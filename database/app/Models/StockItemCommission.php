<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItemCommission extends Model
{
    use HasFactory;

    protected $table = 'stock_item_commission_setup';

    protected $graured = ['item_commission_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'item_commission_id';

    public $timestamps = false;
}
