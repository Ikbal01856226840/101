<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCommission extends Model
{
    use HasFactory;

    protected $table = 'stock_group_commission';

    protected $graured = ['group_commission_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'group_commission_id';

    public $timestamps = false;
}
