<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface GroupChartInterface
{
    public function getGroupChartOfIndex();

    public function StoreGroupChart(Request $request);

    public function getGroupChartId($id);

    public function updateGroupChart(Request $request, $id);

    public function deleteGroupChart($id);

    public function getTree();

    public function getTreeSelectOption();
}
