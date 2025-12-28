<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\InvoiceSummaryRepository;
use Exception;
use Illuminate\Http\Request;
use App\Services\Voucher_setup\Voucher_setup;


class InvoiceSummaryController extends Controller
{
    private $invoiceSummaryRepository;

    private $voucher_setup;

    public function __construct(InvoiceSummaryRepository $invoiceSummaryRepository,Voucher_setup $voucher_setup)
    {

        $this->invoiceSummaryRepository = $invoiceSummaryRepository;
        $this->voucher_setup = $voucher_setup;


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function invoiceSummaryShow()
    {
        if (user_privileges_check('report', 'invoiceSummary', 'display_role')) {
            $vouchers = $this->voucher_setup->AccessVoucherSetup();
            return view('admin.report.general.invoice_summary', compact('vouchers'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function invoiceSummaryGetData(Request $request)
    {
        if (user_privileges_check('report', 'invoiceSummary', 'display_role')) {
            try {
                $data = $this->invoiceSummaryRepository->getInvoiceSummaryOfIndex($request);

                return RespondWithSuccess('invoice Summary  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('invoice Summary not  successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }


}
