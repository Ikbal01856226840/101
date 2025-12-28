<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface DistributionCenterInterface
{
    public function getDistributionCenterOfIndex();

    public function StoreDistributionCenter(Request $request);

    public function getDistributionCenterId($id);

    public function updateDistributionCenter(Request $request, $id);

    public function deleteDistributionCenter($id);
}
