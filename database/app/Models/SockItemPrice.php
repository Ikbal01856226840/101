<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SockItemPrice extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'price_setup';

    protected $graured = ['price_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'price_id';

    public $timestamps = false;
}
