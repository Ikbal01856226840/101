<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequisitionTransactionMaster extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'order_requisition_transaction_master';

    protected $graured = ['id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    public $timestamps = false;
}
