<?php

namespace App\Repositories\Backend\Approve;

use Illuminate\Http\Request;

interface ApproveInterface
{
    public function getApproveOfIndex($request);

    public function StoreApprove(Request $request);

    public function getApproveId($id);

    public function updateApprove(Request $request, $id);

    public function deleteApprove($id);
}
