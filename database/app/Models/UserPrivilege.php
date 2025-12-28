<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPrivilege extends Model
{
    use HasFactory;

    protected $table = 'user_privileges';

    protected $graured = ['privileges_id'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'privileges_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'table_user_id');
    }

    public $timestamps = false;
}
