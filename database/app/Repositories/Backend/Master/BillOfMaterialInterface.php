<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface BillOfMaterialInterface
{
    public function getBillOfMaterialOfIndex();

    public function StoreBillOfMaterial(Request $request);

    public function getBillOfMaterialId($id);

    public function updateBillOfMaterial(Request $request, $id);

    public function deleteBillOfMaterial($id);
}
