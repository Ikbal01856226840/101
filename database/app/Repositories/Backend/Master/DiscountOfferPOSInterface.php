<?php

namespace App\Repositories\Backend\Master;

use Illuminate\Http\Request;

interface DiscountOfferPOSInterface
{
    public function getDiscountOfferPOSOfIndex();

    public function StoreDiscountOfferPOS(Request $request);

    public function getDiscountOfferPOSId($id);

    public function updateDiscountOfferPOS(Request $request, $id);

    public function deleteDiscountOfferPOS($id);
}
