<?php

namespace App\Repositories\Backend;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthInterface
{
    public function checkIfAuthenticated(Request $request)
    {
        if (Auth::attempt(['log_in_id' => $request->user_name, 'password' => $request->password])) {
            return true;
        }

        return false;
    }

    public function registerUser($request)
    {
        $user = new User();
        $user->log_in_id = $request->log_in_id;
        $user->user_level = $request->user_level;
        $user->locked = $request->locked;
        $user->activity = $request->activity;
        $user->unit_or_branch = $request->unit_or_branch;
        $user->active_time_start = $request->active_time_start;
        $user->active_time_end = $request->active_time_end;
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->phone_2 = $request->phone_2;
        $user->phone_3 = $request->phone_3;
        $user->address = $request->address;
        $user->insert_date = $request->insert_date;
        $user->godown_id = implode(',', $request->godown_id_array);
        $user->dis_cen_id = $request->dis_cen_id;
        // $user->voucher_upadte=$request->voucher_upadte;
        $user->agar = implode(',', $request->group_id_array);

        $user->password = Hash::make($request->password);
        $user->save();

        return $user;
    }

    public function findUserByUserName($user_name)
    {
        $user = User::where('user_name', $user_name)->first();

        return $user;
    }

    public function findUserGet($id)
    {
        $user = User::where('id', $id)->first();

        return $user;
    }

    public function logout(Request $request)
    {
        return $request->user()->token()->revoke();
    }

    public function changePassword(Request $request)
    {
        $user = $this->findUserGet(Auth::id());
        if (Hash::check($request->old_password, $user->password)) {
            $change_password = User::find(Auth::id());
            $change_password->password = Hash::make($request->new_password);
            $change_password->save();
        }

        return $change_password;
    }
}
