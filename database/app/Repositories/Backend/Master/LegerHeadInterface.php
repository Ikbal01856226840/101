<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface LegerHeadInterface
{
    public function getLegerHeadOfIndex();

    public function StoreLegerHead(Request $request);

    public function getLegerHeadId($id);

    public function updateLegerHead(Request $request, $id);

    public function deleteLegerHead($id);
}
