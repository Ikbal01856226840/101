<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\ItemVoucherAnalysisRepository;
use Exception;
use Illuminate\Http\Request;

class ItemVoucherAnalysisController extends Controller
{
    private $itemVoucherAnalysisRepository;

    public function __construct(ItemVoucherAnalysisRepository $itemVoucherAnalysisRepository)
    {
        $this->itemVoucherAnalysisRepository = $itemVoucherAnalysisRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemVoucherAnalysisShow()
    {
        if (user_privileges_check('report', 'ItemVoucherAnalysisLedger', 'display_role')) {
            return view('admin.report.movement_analysis_1.item_voucher_analysis');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function itemVoucherAnalysis(Request $request)
    {
        if (user_privileges_check('report', 'ItemVoucherAnalysisLedger', 'display_role')) {
            try {
                $data = $this->itemVoucherAnalysisRepository->getItemVoucherAnalyisOfIndex($request);

                return RespondWithSuccess('item voucher analysis successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('item voucher analysis not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
