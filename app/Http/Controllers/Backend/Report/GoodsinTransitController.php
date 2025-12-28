<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\GoodsinTransitRepository;
use App\Repositories\Backend\Voucher\VoucherTransferRepository;
use App\Services\User\UserCheck;
use Exception;
use Illuminate\Http\Request;

class GoodsinTransitController extends Controller
{
    private $goodsinTransit;
    private $userCheck;
    private $voucherTransferRepository;

    public function __construct(GoodsinTransitRepository $goodsinTransit,UserCheck $userCheck,VoucherTransferRepository $voucherTransferRepository)
    {
        $this->goodsinTransit = $goodsinTransit;
        $this->userCheck=$userCheck;
        $this->voucherTransferRepository=$voucherTransferRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('report', 'GoodsinTransit', 'display_role')) {
            $distributions= $this->userCheck->AccessDistributionCenter();
            return view('admin.report.approved.goods_in_transit', compact('distributions'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGoodsinTransit(Request $request)
    {
        if (user_privileges_check('report', 'GoodsinTransit', 'display_role')) {
            try {
                $data = $this->goodsinTransit->getGoodsinTransitOfIndex($request);

                return RespondWithSuccess('GoodsinTransit show successfully !! ', $data, 200);
            } catch (Exception $e) {
                return RespondWithError('GoodsinTransit not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getGoodsinTransitReceiveData(Request $request)
    {
        if (user_privileges_check('report', 'GoodsinTransit', 'display_role')) {
            try {
                $data = $this->voucherTransferRepository->receiveApproval($request->id);

                return RespondWithSuccess('GoodsinTransit show successfully !! ', $data, 200);
            } catch (Exception $e) {
                return RespondWithError('GoodsinTransit not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
