<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockGroup extends Model
{
    use HasFactory;

    protected $table = 'stock_group';

    protected $graured = ['stock_group_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'stock_group_id';
}
