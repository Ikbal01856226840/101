<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountOfferPOS extends Model
{
    use HasFactory;

    protected $table = 'offer_setup';

    protected $graured = ['offer_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'offer_id';

    public $timestamps = false;
}
