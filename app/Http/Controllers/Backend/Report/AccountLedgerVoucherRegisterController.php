<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\VoucherRepository;

use App\Repositories\Backend\Report\LedgerVoucherRegisterRepository;
use Illuminate\Http\Request;
use Exception;

class AccountLedgerVoucherRegisterController extends Controller
{
    private $ledgerVoucherRegisterRepository;

    private $voucherRepository;



    public function __construct(LedgerVoucherRegisterRepository $ledgerVoucherRegisterRepository, VoucherRepository $voucherRepository)
    {
        $this->ledgerVoucherRegisterRepository = $ledgerVoucherRegisterRepository;
        $this->voucherRepository = $voucherRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ledgerVoucherRegisterShow()
    {
        if (user_privileges_check('report', 'LedgerVoucherRegister', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();

            return view('admin.report.account_summary.account_ledger_voucher_register', compact('vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function ledgerVoucherRegister(Request $request)
    {
        if (user_privileges_check('report', 'LedgerVoucherRegister', 'display_role')) {
            try {
                $data = $this->ledgerVoucherRegisterRepository->getLedgerVoucherRegisterOfIndex($request);

                return RespondWithSuccess('Ledger Voucher Register show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Ledger Voucher Register Not show successfully', $e->getMessage(), 400);
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
    public function ledgerVoucherWiseRegister(Request $request)
    {
        if (user_privileges_check('report', 'LedgerVoucherRegister', 'display_role')) {
            $vouchers = $this->voucherRepository->voucher_specific_data();
            $form_date = $request->from_date;
            $to_date = $request->to_date;
            $voucher_id=$request->voucher_id;

            return view('admin.report.account_summary.account_ledger_voucher_register', compact('vouchers','form_date','to_date','voucher_id'));
        } else {
            abort(403);
        }
    }
}

