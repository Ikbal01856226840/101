<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface MeasureInterface
{
    public function getMeasureOfIndex();

    public function StoreMeasure(Request $request);

    public function getMeasureId($id);

    public function updateMeasure(Request $request, $id);

    public function deleteMeasure($id);
}
