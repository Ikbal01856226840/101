<?php

namespace App\Repositories\Backend;

use Illuminate\Http\Request;

interface CompanyInterface
{
    public function getCompanyOfIndex();

    public function updateCompany(Request $request, $id);
}
