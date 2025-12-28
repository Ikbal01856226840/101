<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitCredit extends Model
{
    use HasFactory;

    protected $table = 'debit_credit';

    protected $graured = ['debit_credit_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'debit_credit_id';

    public $timestamps = false;
}
