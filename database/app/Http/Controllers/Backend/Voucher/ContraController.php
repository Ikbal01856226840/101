<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\BranchRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Voucher\VoucherContraRepository;
use App\Services\Voucher_setup\Voucher_setup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContraController extends Controller
{
    private $godown;

    private $unit_branch;

    private $voucher_setup;

    private $authRepository;

    private $voucherContraRepository;

    public function __construct(GodownRepository $godownRepository, BranchRepository $branchRepository, Voucher_setup $voucher_setup, AuthRepository $authRepository, VoucherContraRepository $voucherContraRepository)
    {
        $this->godown = $godownRepository;
        $this->voucherContraRepository = $voucherContraRepository;
        $this->unit_branch = $branchRepository;
        $this->voucher_setup = $voucher_setup;
        $this->authRepository = $authRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.master.components.index');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!empty(array_filter($request->ledger_id))) {
            if (user_privileges_check('Voucher', $request->voucher_id, 'create_role')) {
                if ($request->ch_4_dup_vou_no == 0) {
                    $voucher_invoice = '';
                    $validator = Validator::make($request->all(), [
                        'invoice_no' => 'required|unique:transaction_master,invoice_no,'.$request->invoice_no.',tran_id,voucher_id,'.$request->voucher_id,
                    ]);

                    if (empty($request->invoice)) {
                        if ($validator->fails()) {
                            return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                        }
                       
                    } else {
                        if ($validator->fails()) {
                            $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();

                            preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
                            $voucher_invoice = str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                        }
                    }
                }
                try {
                    $data = $this->voucherContraRepository->storeVoucherContra($request,$voucher_invoice);

                    return RespondWithSuccess('Voucher Contra successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Contra Not successful !!', $e->getMessage(), 404);
                }
            } else {
                abort(403);
            }
        } else {
            return RespondWithError('Voucher  Contra Not successful !!', 'Ledger not Empty', 404);
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
        if (user_privileges_check('Voucher', $id, 'create_role')) {
            $godowns = $this->godown->getGodownOfIndex();
            $unit_branch = $this->unit_branch->getBranchOfIndex();
            $voucher = Voucher::find($id);
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);
            $debit_setup = $this->voucher_setup->debit_setup($voucher);
            $credit_setup = $this->voucher_setup->credit_setup($voucher);
            $debit_balance_cal = $this->voucher_setup->balanceDebitCreditCalculation($debit_setup);
            $credit_balance_cal = $this->voucher_setup->balanceDebitCreditCalculation($credit_setup);

            return view('admin.voucher.contra.create_contra', compact('godowns', 'unit_branch', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'debit_setup', 'credit_setup', 'debit_balance_cal', 'credit_balance_cal'));
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
    public function edit($id)
    {
        $data = $this->voucherContraRepository->getVoucherContraId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'alter_role')) {
            $unit_branch = $this->unit_branch->getBranchOfIndex();
            $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);

            return view('admin.voucher.contra.edit_contra', compact('branch_setup', 'data', 'voucher'));
        } else {
            abort(403);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (user_privileges_check('Voucher', $request->voucher_id, 'alter_role')) {
            if ($request->ch_4_dup_vou_no == 0) {
                $validator = Validator::make($request->all(), [
                    'invoice_no' => 'required|unique:transaction_master,invoice_no,'.$id.',tran_id,voucher_id,'.$request->voucher_id,
                ]);
                if (empty($request->invoice)) {
                    if ($validator->fails()) {
                        return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                    $voucher_invoice = 0;
                } else {
                    if ($validator->fails()) {
                        dd($request->invoice);
                        $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();

                        preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
                        $voucher_invoice = str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                    } else {
                        $voucher_invoice = 0;
                    }
                }
            } else {
                $voucher_invoice = 0;
            }
            try {
                $data = $this->voucherContraRepository->updateVoucherContra($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher Contra  Update Successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Contra  Update Not Successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherContraRepository->getVoucherContraId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherContraRepository->deleteVoucherContra($id);

                return RespondWithSuccess('Voucher  Contra delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher  Contra delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function searchingLedgerDataGet()
    {
        $debit_group = Voucher::find($_GET['voucher_id']);
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        $search = $_GET['name'];
        if (array_sum(explode(' ', $debit_group->debit_group_id)) != 0) {
            if ($_GET['DrCr'] == 'Dr') {
                $data = $this->voucher_setup->group_chart($debit_group->debit_group_id, $search);

            } elseif ($_GET['DrCr'] == 'Cr') {

                $data = $this->voucher_setup->group_chart($debit_group->credit_group_id, $search);

            }
        } elseif (array_sum(explode(' ', $get_user->agar)) != 0) {
            $data = $this->voucher_setup->group_chart($get_user->agar, $search);
            $data = $this->voucher_setup->group_chart($get_user->agar, $search);
        } else {
            $data = $this->voucher_setup->ledger_head_searching($search);
        }
        echo json_encode($data);
        exit();
    }

    public function editDebitCredit()
    {

        try {
            $data = $this->voucherContraRepository->editDebitCredit($_GET['tran_id']);

            return RespondWithSuccess('debit credit show successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('debit credit show not successful !!', $e->getMessage(), 404);
        }
    }
}
