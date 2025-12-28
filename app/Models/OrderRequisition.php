<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequisition extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'order_requisition';

    protected $graured = ['id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    public $timestamps = false;
}
