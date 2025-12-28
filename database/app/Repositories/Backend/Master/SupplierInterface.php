<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface SupplierInterface
{
    public function getSupplierOfIndex();

    public function StoreSupplier(Request $request);

    public function getSupplierId($id);

    public function updateSupplier(Request $request, $id);

    public function deleteSupplier($id);
}
