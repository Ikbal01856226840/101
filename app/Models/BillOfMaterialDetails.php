<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfMaterialDetails extends Model
{
    use HasFactory;

    protected $table = 'bom_details';

    protected $graured = ['details_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'details_id';

    public $timestamps = false;
}
