<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\UserRequest;
use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\BranchRepository;
use App\Repositories\Backend\Master\DistributionCenterRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\User\UserRepository;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $distribution;

    private $godown;

    private $groupChart;

    private $auth;

    private $userRepository;

    private $voucher;

    private $branch;

    public function __construct(DistributionCenterRepository $distributionCenterRepository, GodownRepository $godownRepository, GroupChartRepository $groupChartRepository, AuthRepository $authRepository, UserRepository $userRepository, VoucherRepository $voucher, BranchRepository $branch)
    {
        $this->distribution = $distributionCenterRepository;
        $this->godown = $godownRepository;
        $this->groupChart = $groupChartRepository;
        $this->auth = $authRepository;
        $this->userRepository = $userRepository;
        $this->voucher = $voucher;
        $this->branch = $branch;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('master', 'User', 'display_role')) {
            try {
                $data = $this->userRepository->getUserOfIndex();

                return RespondWithSuccess('All User list  show successful !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('All User list not show successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function userListShow()
    {
        if (user_privileges_check('master', 'User', 'display_role')) {
            return view('admin.user.index');
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $distributions = $this->distribution->getDistributionCenterOfIndex();
        $godowns = $this->godown->getGodownOfIndex();
        $groupCharts = $this->groupChart->getTreeSelectOption();
        $branchs = $this->branch->getBranchOfIndex();

        if (user_privileges_check('master', 'User', 'create_role')) {
            return view('admin.user.add', compact('distributions', 'godowns', 'groupCharts', 'branchs'));
        } else {
            abort(403);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        if (user_privileges_check('master', 'User', 'create_role')) {
            try {
                $data = $this->auth->registerUser($request);

                return RespondWithSuccess('Registered Successfully !!', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Registration Cannot Successfully ! !!', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $distributions = $this->distribution->getDistributionCenterOfIndex();
        $godowns = $this->godown->getGodownOfIndex();
        $groupCharts = $this->groupChart->getTreeSelectOption();
        $get_user_data = $this->auth->findUserGet($id);
      
        $branchs = $this->branch->getBranchOfIndex();

        if (user_privileges_check('master', 'User', 'alter_role')) {
            return view('admin.user.edit', compact('distributions', 'godowns', 'groupCharts', 'get_user_data', 'branchs'));
        } else {
            abort(403);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (user_privileges_check('master', 'User', 'alter_role')) {
            try {
                $data = $this->userRepository->updateUser($request, $id);

                return RespondWithSuccess('user update successful !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('user not  update successful', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (user_privileges_check('master', 'User', 'delete_role')) {
            try {
                $data = $this->userRepository->deleteUser($id);

                return RespondWithSuccess('user  delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('user not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function userPrivilegeShow($id)
    {
        $get_user_data = $this->auth->findUserGet($id);
        $userPrivilegeSet = $this->userRepository->userPrivilegeSet($id);
        $vouchers_type = $this->voucher->getVoucherOfIndex();

        if (user_privileges_check('master', 'User', 'alter_role')) {
            return view('admin.user.user_privilege', compact('get_user_data', 'userPrivilegeSet', 'vouchers_type'));
        } else {
            abort(403);
        }
    }

    public function userPrivilegeStore(Request $request)
    {
        if (Auth()->user()->user_level==1) {
            try {
                $data = $this->userRepository->userPrivilegeStore($request);

                return RespondWithSuccess('user  delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('user not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
?>

