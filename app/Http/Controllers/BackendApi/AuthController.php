<?php

namespace App\Http\Controllers\BackendApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authenticatin\UserLoginRequest;
use App\Http\Requests\Authenticatin\UserRequest;
use App\Models\User;
use App\Repositories\Backend\AuthRepository;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function createToken()
    {
        $user = User::first();
        $accessToken = $user->createToken('Token Name')->accessToken;

        return $accessToken;
    }

    public function register(UserRequest $request)
    {
        try {
            $data = $this->authRepository->registerUser($request);

            return RespondWithSuccess('Registered successully !!', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Registration Cannot successfull ! !!', $e->getMessage(), 404);
        }
    }

    public function login(UserLoginRequest $request)
    {

        if ($this->authRepository->checkIfAuthenticated($request)) {
            $data['user'] = $user = $this->authRepository->findUserByUserName($request->user_name);
            $data['accessToken'] = $user->createToken('authToken')->accessToken;

            return RespondWithSuccess('Logged in successully !!', $data, 201);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Sorry Invalid UserName and Password',
            ], 404);
        }
    }

    public function logout(Request $request)
    {
        $logout = $this->authRepository->logout($request);
        if ($logout) {
            return RespondWithSuccess('Logout in successully !!', $logout, 201);
        } else {
            return RespondWithError('Logout in successully !!', $logout, 404);
        }
    }

    public function change_password(Request $request)
    {

        try {
            $data = $this->authRepository->changePassword($request);

            return RespondWithSuccess('Password Change in successully !!', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Password Change Not successully !!', '', 404);
        }
    }
}
