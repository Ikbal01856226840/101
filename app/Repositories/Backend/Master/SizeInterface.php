<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface SizeInterface
{
    public function getSizeOfIndex();

    public function StoreSize(Request $request);

    public function getSizeId($id);

    public function updateSize(Request $request, $id);

    public function deleteSize($id);
}
