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
        if ($dis_cen_id->dis_cen_id != 0) {
            return DB::table('distribution_center')->where('dis_cen_id', $dis_cen_id->dis_cen_id)->get();
        } else {
            return $this->distributionCenterRepository->getDistributionCenterOfIndex();
        }
    }
}
