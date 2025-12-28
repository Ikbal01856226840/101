<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Services\Voucher_setup\Voucher_setup;
use App\Repositories\Backend\Report\VoucherExchangeRepository;
use App\Repositories\Backend\Voucher\VoucherContraRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\BranchRepository;
use App\Models\Voucher;

class VoucherExchangeController extends Controller
{
    private $voucher_setup;
    private $voucherExchangeRepository;
    private $voucherRepository;
    private $voucherContraRepository;
    private $unit_branch;


    public function __construct(Voucher_setup $voucher_setup,
    VoucherExchangeRepository $voucherExchangeRepository,VoucherRepository $voucherRepository,
    VoucherContraRepository $voucherContraRepository, BranchRepository $branchRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->voucherExchangeRepository = $voucherExchangeRepository;
        $this->voucherRepository = $voucherRepository;
        $this->voucherContraRepository = $voucherContraRepository;
        $this->unit_branch = $branchRepository;

    }

    public function index()
    {
        if (user_privileges_check('report', 'VoucherExchange', 'display_role')) {
            $vouchers = $this->voucherExchangeRepository->AccessVoucherSetup();
            return view('admin.report.general.voucher_exchange', compact('vouchers'));
        } else {
            abort(403);
        }
    }

    public function voucherExchange(Request $request){
        if (user_privileges_check('report', 'VoucherExchange', 'display_role')) {
            try {
                $voucher=$this->voucherRepository->getVoucherId($request->voucher_id);

                $transaction = $this->voucherExchangeRepository->getVouchers($request);
                $data = ['transaction' => $transaction,'voucher'=>$voucher];
                return RespondWithSuccess('Voucher Exchange show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Error', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    public function voucherDuplicate($voucher_id,$tran_id)
    {

        // dd($voucher_id,$tran_id);



        if (user_privileges_check('Voucher', $voucher_id, 'alter_role')) {
            $data = $this->voucherContraRepository->getVoucherContraId($tran_id);
            $voucher=$this->voucherRepository->getVoucherId($voucher_id);
            $unit_branch = $this->unit_branch->getBranchOfIndex();
            $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);
            // $voucher = Voucher::find($data->voucher_id);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            // dd($voucher_invoice);
            if($voucher->voucher_type_id==1){
                return view('admin.report.general.voucherExchange.contra',
                compact('voucher_id','tran_id','branch_setup', 'data', 'voucher','voucher_invoice'));
            }elseif($voucher->voucher_type_id==8){
                return view('admin.report.general.voucherExchange.payment',
                compact('voucher_id','tran_id','branch_setup', 'data', 'voucher','voucher_invoice'));
            }elseif($voucher->voucher_type_id==14){
                return view('admin.report.general.voucherExchange.receipt',
                compact('voucher_id','tran_id','branch_setup', 'data', 'voucher','voucher_invoice'));
            }
            // return view('admin.voucher.contra.create_contra',
            // compact('godowns', 'unit_branch', 'voucher_date',
            // 'branch_setup', 'voucher_invoice', 'voucher', 'debit_setup',
            // 'credit_setup', 'debit_balance_cal', 'credit_balance_cal'));
        } else {
            abort(403);
        }
        // if (user_privileges_check('report', 'VoucherExchange', 'display_role')) {
        //     try {
        //         // $this->voucherNumberModifyRepository->getVoucherNumberModifyStore($request);
        //         return RespondWithSuccess('Voucher Exchange store successfully !! ', null, 201);
        //     } catch (Exception $e) {
        //         return RespondWithError('Error', $e->getMessage(), 400);
        //     }
        // } else {
        //     abort(403);
        // }
    }

}
