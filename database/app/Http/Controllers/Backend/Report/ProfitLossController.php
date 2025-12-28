<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\ProfitLossRepository;
use Exception;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    private $profitLossRepository;

    public function __construct(ProfitLossRepository $profitLossRepository)
    {

        $this->profitLossRepository = $profitLossRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profitLossShow()
    {
        if (user_privileges_check('report', 'ProfitLoss', 'display_role')) {
            return view('admin.report.account_summary.profit_loss');
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function profitLoss(Request $request)
    {
        if (user_privileges_check('report', 'ProfitLoss', 'display_role')) {
            try {
                $data = $this->profitLossRepository->getProfitLossOfIndex($request);

                return RespondWithSuccess('profit loss successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('profit loss not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
