<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $table = 'stock_item';

    protected $graured = ['stock_item_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'stock_item_id';
    public $timestamps = false;

    public function stock_group()
    {
        return $this->belongsTo(StockGroup::class, 'stock_group_id');
    }
}
