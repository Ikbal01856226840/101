<?php

namespace App\Http\Controllers\Backend\Approve;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Approve\ApproveRepository;
use App\Repositories\Backend\BranchRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use App\Repositories\Backend\Voucher\VoucherSalesRepository;
use App\Services\Voucher_setup\Voucher_setup;
use App\Models\GoodsInTransit;
use Exception;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    private $approveRepository;

    private $voucherRepository;

    private $voucherSalesRepository;

    private $branchRepository;

    private $voucher_setup;

    public function __construct(ApproveRepository $approveRepository, VoucherRepository $voucherRepository, VoucherSalesRepository $voucherSalesRepository,BranchRepository $branchRepository,Voucher_setup $voucher_setup)
    {
        $this->approveRepository = $approveRepository;
        $this->voucherRepository = $voucherRepository;
        $this->voucherSalesRepository = $voucherSalesRepository;
        $this->branchRepository=$branchRepository;
        $this->voucher_setup=$voucher_setup;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showApprove()
    {
        $vouchers = $this->voucher_setup->AccessVoucherSetup();

        return view('admin.approve.approve', compact('vouchers'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $this->approveRepository->getApproveOfIndex($request);

            return RespondWithSuccess('approve show successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('approve not show successfully', $e->getMessage(), 404);
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

        $voucher_type=$this->approveRepository->voucherType($id);
        if($voucher_type->voucher_type_id == 21){
            $tran_ledger= $this->approveRepository->tranmasterAndLedgerWithOutDebitCredit($id);
        }else{
            $tran_ledger= $this->approveRepository->tranmasterAndLedger($id);
        }
        if ($tran_ledger->voucher_type_id == 14 || $tran_ledger->voucher_type_id == 8 || $tran_ledger->voucher_type_id == 1|| $tran_ledger->voucher_type_id == 6) {
            $debit_credit = $this->approveRepository->getdebitCredit($id);
            $stock = 0;
            $debit_credit_commission = 0;
            $stock_in = 0;
            $stock_out = 0;
            $destination_godown=0;
        } elseif ($tran_ledger->voucher_type_id == 10 || $tran_ledger->voucher_type_id == 24 || $tran_ledger->voucher_type_id == 29) {
            $stock = $this->approveRepository->getStockIn($id);
            $debit_credit_commission = $this->approveRepository->getdebitCreditCommission($id);
            $debit_credit = 0;
            $stock_in = 0;
            $stock_out = 0;
            $destination_godown=0;
        } elseif ($tran_ledger->voucher_type_id == 19 || $tran_ledger->voucher_type_id == 23 || $tran_ledger->voucher_type_id == 25 || $tran_ledger->voucher_type_id == 22) {
            if($tran_ledger->voucher_type_id == 22){
                if($voucher_type->st_approval){
                    $destination_godown= GoodsInTransit::where('goods_in_transits.tran_id', $id)
                    ->leftJoin('godowns', 'godowns.godown_id', '=', 'goods_in_transits.to_godown')
                    ->first(['godowns.godown_name']);
                }else{
                    $destination_godown=$this->approveRepository->DestinationGodown($id);
                }
            }else{
                $destination_godown=0;
            }

            if ($tran_ledger->commission_is == 1) {
                $stock = $this->voucherSalesRepository->product_wise_commission_sales($id);
                $debit_credit_commission = $this->approveRepository->getdebitCreditCommissionProductWise($id);

            } else {
                $stock = $this->approveRepository->getStockOut($id);
                if ($tran_ledger->voucher_type_id != 22) {
                    $debit_credit_commission = $this->approveRepository->getdebitCreditCommission($id);
                } else {
                    $debit_credit_commission = 0;
                }

            }
            $stock_in = 0;
            $stock_out = 0;
            $debit_credit = 0;

        } elseif ($tran_ledger->voucher_type_id == 21) {
            $stock_in = $this->approveRepository->getStockIn($id);
            $stock_out = $this->approveRepository->getStockOut($id);
            $stock = 0;
            $debit_credit_commission = 0;
            $debit_credit = 0;
            $destination_godown=0;

        }
       $access_report= $this->branchRepository->getBranchLimitOne();


       if($access_report->id_report==1){
            // hcl report
            if($tran_ledger->voucher_type_id==14){
                $debit_credit = $this->approveRepository->getCreditReceived($id);
                return view('admin.approve.approve_received_report', compact('debit_credit','tran_ledger','access_report','voucher_type'));
            }else if($tran_ledger->voucher_type_id==8){
                return view('admin.approve.approve_payment_report', compact('debit_credit','tran_ledger','access_report','voucher_type'));
            }else if($tran_ledger->voucher_type_id == 28){
                $tran_ledger= $this->approveRepository->tranmasterAndLedger($id);
                return view('admin.approve.commission_report',compact('tran_ledger','access_report'));
            }else{
                return view('admin.approve.approve_report', compact('stock_in', 'stock_out', 'debit_credit', 'tran_ledger', 'stock', 'debit_credit_commission','destination_godown','access_report','voucher_type'));
            }
       }elseif($access_report->id_report==101){
            // glogo report
            if($tran_ledger->voucher_type_id==14){
                $debit_credit = $this->approveRepository->getCreditReceived($id);
                return view('admin.approve.approve_received_report', compact('debit_credit','tran_ledger','access_report','voucher_type'));
            }else if($tran_ledger->voucher_type_id==8){
                return view('admin.approve.approve_payment_report', compact('debit_credit','tran_ledger','access_report','voucher_type'));
            }else if($tran_ledger->voucher_type_id == 28){
                $tran_ledger= $this->approveRepository->tranmasterAndLedger($id);
                return view('admin.approve.commission_report',compact('tran_ledger','access_report'));
            }else{
                $ledger_blance=0;
                if($tran_ledger->voucher_type_id!=21){
                    $blanceDebitCredit=$this->voucher_setup->balanceDebitCredit($tran_ledger->ledger_head_id);
                    $ledger_blance=$this->voucher_setup->balanceDebitCreditCalculation($blanceDebitCredit);

                }

                return view('admin.approve.glogo_approve_report', compact('stock_in', 'stock_out', 'debit_credit', 'tran_ledger', 'stock', 'debit_credit_commission','destination_godown','ledger_blance','access_report','voucher_type'));
            }
       }else{
            // other report
            if($tran_ledger->voucher_type_id==14){
                $debit_credit = $this->approveRepository->getCreditReceived($id);
                return view('admin.approve.approve_received_report', compact('debit_credit','tran_ledger','access_report','voucher_type'));
            }else if($tran_ledger->voucher_type_id==8){
                return view('admin.approve.approve_payment_report', compact('debit_credit','tran_ledger','access_report','voucher_type'));
            }else if($tran_ledger->voucher_type_id == 28){
                $tran_ledger= $this->approveRepository->tranmasterAndLedger($id);
                return view('admin.approve.commission_report',compact('tran_ledger','access_report'));
            }else{
                return view('admin.approve.approve_report', compact('stock_in', 'stock_out', 'debit_credit', 'tran_ledger', 'stock', 'debit_credit_commission','destination_godown','access_report','voucher_type'));
            }
       }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deliveryApproved($id)
    {
        try {
            $data = $this->approveRepository->deliveryApproved($id);

            return RespondWithSuccess('approve successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('approve not  successfully', $e->getMessage(), 404);
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showChallanApprove($id)
    {
            $voucher_type=$this->approveRepository->voucherType($id);

            if($voucher_type->voucher_type_id == 21){
                $tran_ledger= $this->approveRepository->tranmasterAndLedgerWithOutDebitCredit($id);
            }else{
                $tran_ledger= $this->approveRepository->tranmasterAndLedger($id);
            }
            if($tran_ledger->voucher_type_id == 22){
                $stock_transfer=$this->approveRepository->StockTransferAddress($id);
                if($voucher_type->st_approval){
                    $destination_godown= GoodsInTransit::where('goods_in_transits.tran_id', $id)
                    ->leftJoin('godowns', 'godowns.godown_id', '=', 'goods_in_transits.to_godown')
                    ->first(['godowns.godown_name']);
                }else{
                    $destination_godown=$this->approveRepository->DestinationGodown($id);
                }
            }else{
                $destination_godown=0;
                $stock_transfer=0;
            }
            $stock = $this->approveRepository->getStockOut($id);
            $access_report= $this->branchRepository->getBranchLimitOne();
            if($access_report->id_report==1){
                return view('admin.approve.challan_report', compact( 'stock','tran_ledger','destination_godown','voucher_type','access_report','stock_transfer'));
            }elseif($access_report->id_report==101){
                $ledger_blance=0;
                if($tran_ledger->voucher_type_id!=21){
                    $blanceDebitCredit=$this->voucher_setup->balanceDebitCredit($tran_ledger->ledger_head_id);
                    $ledger_blance=$this->voucher_setup->balanceDebitCreditCalculation($blanceDebitCredit);

                }
                return view('admin.approve.glogo_challan_report', compact( 'stock','tran_ledger','destination_godown','ledger_blance','voucher_type','access_report','stock_transfer'));
            }else{
                return view('admin.approve.challan_report', compact( 'stock','tran_ledger','destination_godown','voucher_type','access_report','stock_transfer'));
            }


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showSalesBillOrder($id)
    {
            $tran_ledger = $this->approveRepository->tranmasterAndLedgerSalesOrder($id);

            $stock = $this->approveRepository->getSalesOrder($id);
        return view('admin.approve.sales_order_report', compact( 'stock', 'tran_ledger'));

    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPosBill($id)
    {
            $tran_ledger = $this->approveRepository->tranmasterAndLedger($id);

            $stock_out = $this->approveRepository->getStockOut($id);

        return view('admin.approve.pos_bill_report', compact('stock_out','tran_ledger'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseOrder($id)
    {
            $stock = $this->approveRepository->purchaseOrder($id);

            $purchase_order_transaction= $this->approveRepository->purchaseOrdertransaction($id);
            $voucher_type=$this->approveRepository->voucherType($id);
        return view('admin.approve.purchase_order', compact('stock','purchase_order_transaction','voucher_type'));
    }

    public function stockItemCommission(Request $request,$id){
       
        try {
            $data=$this->approveRepository->stockItemCommission($id,$request);
            return RespondWithSuccess('stock item commission show successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('stock item commission show not show successfully', $e->getMessage(), 400);
        }
       
    }
}
