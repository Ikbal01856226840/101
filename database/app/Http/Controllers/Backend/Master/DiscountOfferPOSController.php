<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\DiscountOfferPOSRepository;
use App\Repositories\Backend\Master\DistributionCenterRepository;
use App\Repositories\Backend\Master\StockGroupRepository;
use Illuminate\Http\Request;
use Exception;

class DiscountOfferPOSController extends Controller
{
    private $discountOfferPOSRepository;

    private $distributionCenterRepository;

    private $stockGroupRepository;

    public function __construct(DiscountOfferPOSRepository $discountOfferPOSRepository,DistributionCenterRepository $distributionCenterRepository,StockGroupRepository $stockGroupRepository)
    {
        $this->discountOfferPOSRepository = $discountOfferPOSRepository;
        $this->distributionCenterRepository=$distributionCenterRepository;
        $this->stockGroupRepository=$stockGroupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $select_option_tree = $this->distributionCenterRepository->getTreeSelectOption();
        $select_option_stock_group_tree = $this->stockGroupRepository->getTreeSelectOption();


        if (user_privileges_check('master', 'POS Discount', 'display_role')) {
            return view('admin.master.discount_offer_pos.index', compact('select_option_tree','select_option_stock_group_tree'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show POS discount.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDiscountOfferPOS()
    {
        if (user_privileges_check('master', 'POS Discount', 'display_role')) {
            try {
                $data = $this->discountOfferPOSRepository->getDiscountOfferPOSOfIndex();

                return RespondWithSuccess('All discount offer POS list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All discount offer POS list show successfully !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (user_privileges_check('master', 'POS Discount', 'create_role')) {
            try {
                $data = $this->discountOfferPOSRepository->StoreDiscountOfferPOS($request);

                return RespondWithSuccess('discount offer POS create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('discount offer POS not create successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (user_privileges_check('master', 'POS Discount', 'alter_role')) {
            try {
                $data = $this->discountOfferPOSRepository->getDiscountOfferPOSId($id);

                return RespondWithSuccess('discount offer POS show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('discount offer POS not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (user_privileges_check('master', 'POS Discount', 'alter_role')) {
            try {
                $data = $this->discountOfferPOSRepository->updateDiscountOfferPOS($request, $id);

                return RespondWithSuccess('discount offer POS update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('discount offer POS not  update successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (user_privileges_check('master', 'POS Discount', 'delete_role')) {
            try {
                $data = $this->discountOfferPOSRepository->deleteDiscountOfferPOS($id);

                return RespondWithSuccess('discount offer POS delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('discount offer POS not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }

    }
}
