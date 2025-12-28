<?php

namespace App\Repositories\Backend;

use Illuminate\Http\Request;

interface BranchInterface
{
    public function getBranchOfIndex();

    public function StoreBranch(Request $request);

    public function getBranchId($id);

    public function updateBranch(Request $request, $id);

    public function deleteBranch($id);
}
