<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMaster extends Model
{
    use HasFactory;

    protected $table = 'transaction_master';

    protected $graured = ['tran_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'tran_id';

    public $timestamps = false;
}
