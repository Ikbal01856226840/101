<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockGroupPrice extends Model
{
    use HasFactory;

    protected $table = 'stock_group_price';

    protected $graured = ['group_price_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'group_price_id';

    public $timestamps = false;
}
