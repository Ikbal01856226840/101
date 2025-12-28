<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface ComponentsInterface
{
    public function getComponentsOfIndex();

    public function StoreComponents(Request $request);

    public function getComponentsId($id);

    public function updateComponents(Request $request, $id);

    public function deleteComponents($id);
}
