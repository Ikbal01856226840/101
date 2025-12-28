<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\TrialBalanceRepository;
use Exception;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    private $trialBalanceRepository;

    public function __construct(TrialBalanceRepository $trialBalanceRepository)
    {

        $this->trialBalanceRepository = $trialBalanceRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trialBalanceShow()
    {
        if (user_privileges_check('report', 'TrialBalance', 'display_role')) {
            return view('admin.report.account_summary.trial_balance');
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function trialBalance(Request $request)
    {
        if (user_privileges_check('report', 'TrialBalance', 'display_role')) {
            try {
                $data = $this->trialBalanceRepository->getTrialBalanceOfIndex($request);

                return RespondWithSuccess('trial balance  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('trial balance successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
