<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerHead extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'ledger_head';

    protected $graured = ['ledger_head_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ledger_head_id';
    public $timestamps = false;

    public function group_chart()
    {
        return $this->belongsTo(GroupChart::class, 'group_id');
    }
}
