<?php

namespace App\Repositories\Backend;

use Illuminate\Http\Request;

interface AuthInterface
{
    /**
     * checkIfAuthenticated
     *
     * Check if an user is authenticated or not by request
     *
     * @return bool -> true if authenticated, false if not
     */
    public function checkIfAuthenticated(Request $request);

    /**
     * registerUser
     *
     * Register a User By Request Form
     *
     * @return obj $user object
     */
    public function registerUser(Request $request);

    /**
     * findUserByEmailAddress
     *
     * Find an user by email address
     *
     * @param  string  $email
     * @return obj $user object
     */
    public function findUserByUserName($user_name);

    /**
     * logout
     */
    public function logout(Request $request);

    /**
     *change password
     */
    public function changePassword(Request $request);

    /**
     *get user data
     */
    public function findUserGet($id);
}
