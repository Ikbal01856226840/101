<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface GodownInterface
{
    public function getGodownOfIndex();

    public function StoreGodown(Request $request);

    public function getGodownId($id);

    public function updateGodown(Request $request, $id);

    public function deleteGodown($id);
}
