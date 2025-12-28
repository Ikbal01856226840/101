<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface CustomerInterface
{
    public function getCustomerOfIndex();

    public function StoreCustomer(Request $request);

    public function getCustomerId($id);

    public function updateCustomer(Request $request, $id);

    public function deleteCustomer($id);
}
