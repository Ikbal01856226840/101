<?php

namespace App\Repositories\Backend\User;

use App\Models\User;

class UserDashboardRepository implements UserDashboardInterface
{
    public function getUserOfIndex()
    {
        return User::all();
    }
}
