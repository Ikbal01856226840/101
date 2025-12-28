<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChart extends Model
{
    use HasFactory;

    protected $table = 'group_chart';

    protected $graured = ['group_chart_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'group_chart_id';

    public $timestamps = false;
}
