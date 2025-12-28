<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPageWiseSetting extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'reports';

    protected $graured = ['report_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    public $timestamps = false;
}
