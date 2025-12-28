<?php

namespace App\Repositories\Backend;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class BranchRepository implements BranchInterface
{
    public function getBranchOfIndex()
    {
        return Branch::all(['id', 'branch_name', 'alias', 'remark']);
    }

    public function StoreBranch($request)
    {
        $data = new Branch();
        $data->branch_name = $request->branch_name;
        $data->alias = $request->alias;
        $data->remark = $request->remark;
        $data->created_by = Auth::id();
        $data->save();

        return $data;
    }

    public function getBranchId($id)
    {
        return Branch::findOrFail($id);
    }

    public function updateBranch($request, $id)
    {
        $data = Branch::findOrFail($id);
        $data->branch_name = $request->branch_name;
        $data->alias = $request->alias;
        $data->remark = $request->remark;
        $data->created_by = Auth::id();
        $data->save();

        return $data;
    }

    public function deleteBranch($id)
    {
        return Branch::findOrFail($id)->delete();
    }
}
