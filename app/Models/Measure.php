<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'unitsof_measure';

    protected $graured = ['unit_of_measure_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'unit_of_measure_id';

    public $timestamps = false;
}
