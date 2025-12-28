<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\UserRequest;
use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Master\DistributionCenterRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $authRepository;

    private $distribution;

    private $godown;

    private $groupChart;

    public function __construct(AuthRepository $authRepository, DistributionCenterRepository $distributionCenterRepository, GodownRepository $godownRepository, GroupChartRepository $groupChartRepository)
    {
        $this->authRepository = $authRepository;
        $this->distribution = $distributionCenterRepository;
        $this->godown = $godownRepository;
        $this->groupChart = $groupChartRepository;
        $this->middleware('guest')->except('logout', 'show_change_password', 'change_password');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerCreate()
    {
        $distributions = $this->distribution->getDistributionCenterOfIndex();
        $godowns = $this->godown->getGodownOfIndex();
        $groupCharts = $this->groupChart->getTreeSelectOption();

        return view('admin.user.add', compact('distributions', 'godowns', 'groupCharts'));

    }

    public function register(UserRequest $request)
    {
        dd($request->all());
        try {
            $data = $this->authRepository->registerUser($request);

            return RespondWithSuccess('Registered successully !!', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Registration Cannot successfull ! !!', $e->getMessage(), 400);
        }
    }

    public function showLogin()
    {
        return view('auth.adminLogin');
    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'user_name' => 'required|string|max:255|',
            'password' => 'required|string|min:8',
        ]);

        $captcher_number = ($request->firstNumber) + ($request->secondNumber);
        $user_name = $request->user_name;
        $password = $request->password;
        $captchar = $request->captchar;
        if ($captcher_number == $request->captchar) {
            if ($this->authRepository->checkIfAuthenticated($request)) {
                return redirect()->route('main-dashboard');
            } else {
                return redirect()->back()->with('error', 'Invalid email or password')->with('user_name', $user_name)->with('password', $password)->with('captchar', $captchar);
            }
        } else {
            return redirect()->back()->with('error', 'Captche does not match.')->with('user_name', $user_name)->with('password', $password)->with('captchar', $captchar);
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('show-login');

    }

    public function show_change_password()
    {
        $user_name = $this->authRepository->findUserGet(Auth::id());

        return view('auth.change_password', compact('user_name'));
    }

    public function change_password(Request $request)
    {

        try {
            $data = $this->authRepository->changePassword($request);

            return RespondWithSuccess('Password Change in successfully !!', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Password Change Not successfully !!', '', 404);
        }
    }
}
