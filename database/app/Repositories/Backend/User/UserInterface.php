<?php

namespace App\Repositories\Backend\User;

use Illuminate\Http\Request;

interface UserInterface
{
    public function getUserOfIndex();

    public function StoreUser(Request $request);

    public function getUserId($id);

    public function updateUser(Request $request, $id);

    public function deleteUser($id);
}
