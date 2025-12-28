<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\BalanceSheetRepository;
use Exception;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    private $balanceSheetRepository;

    public function __construct(BalanceSheetRepository $balanceSheetRepository)
    {

        $this->balanceSheetRepository = $balanceSheetRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function BalanceSheetShow()
    {
        if (user_privileges_check('report', 'BalanceSheet', 'display_role')) {
            return view('admin.report.account_summary.balance_sheet');
        } else {
            abort(403,'Access Denied — You don’t have permission to view this page.');
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function BalanceSheet(Request $request)
    {
        if (user_privileges_check('report', 'BalanceSheet', 'display_role')) {
            try {
                $data = $this->balanceSheetRepository->getBalanceSheetOfIndex($request);

                return RespondWithSuccess(' balance sheet successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError(' balance sheet successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403,'Access Denied — You don’t have permission to view this page.');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bankBookShow()
    {
        if (user_privileges_check('report', 'BankBook', 'display_role')) {
            return view('admin.report.account_summary.bank_book');
        } else {
            abort(403,'Access Denied — You don’t have permission to view this page.');
        }
    }

     /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function bankBook(Request $request)
    {
        if (user_privileges_check('report', 'BankBook', 'display_role')) {
            try {
                $data = $this->balanceSheetRepository->getCashBankBookOfIndex($request);

                return RespondWithSuccess('cash bank book  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('cash bank book not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403,'Access Denied — You don’t have permission to view this page.');
        }
    }

}
