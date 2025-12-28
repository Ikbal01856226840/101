<?php

namespace App\Services\User;

use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Master\DistributionCenterRepository;
use Illuminate\Support\Facades\DB;

class UserCheck
{
    private $authRepository;

    private $distributionCenterRepository;

    public function __construct(AuthRepository $authRepository, DistributionCenterRepository $distributionCenterRepository)
    {
        $this->authRepository = $authRepository;
        $this->distributionCenterRepository = $distributionCenterRepository;
    }

    public function AccessDistributionCenter()
    {
        $dis_cen_id = $this->authRepository->findUserGet(Auth()->user()->id);
        if (array_sum(array_map('intval', explode(' ', $dis_cen_id->dis_cen_id))) != 0){
            $ids = explode(',', $dis_cen_id->dis_cen_id);
            return  DB::table('distribution_center')->whereIn('dis_cen_id', $ids)->orderByRaw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dis_cen_name, '-', -1), ' ', 1) AS UNSIGNED)")->get();
        } else {
            return $this->distributionCenterRepository->getDistributionCenterOfIndex();
        }
    }
    public function DistributionCenterWiseUserId()
    {
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        $users = DB::table('admin_user_info')->select('id')
        ->where(function($query) use ($get_user) {
            $ids = explode(',', $get_user->dis_cen_id);
            foreach ($ids as $id) {
                $query->orWhereRaw('FIND_IN_SET(?, dis_cen_id)', [$id]);
            }
        })
        ->get();
       return implode(", ",array_column(json_decode(json_encode($users, true), true),'id'));
    }
   
}
