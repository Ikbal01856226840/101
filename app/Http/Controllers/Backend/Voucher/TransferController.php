<?php

namespace App\Http\Controllers\Backend\Voucher;

use App\Http\Controllers\Controller;
use App\Models\GoodsInTransit;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Voucher\{VoucherTransferRepository};
use App\Services\Voucher_setup\Voucher_setup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\User\UserCheck;
use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\Voucher;
use Exception;
use App\Rules\ProductValidation;
use App\Rules\StoreUniqueInvoiceValidation;
use App\Rules\UpdateUniqueInvoiceValidation;

class TransferController extends Controller
{

    private $voucher_setup;

    private $customerRepository;

    private $userCheck;

    private $voucherTransferRepository;

    public function __construct( Voucher_setup $voucher_setup, CustomerRepository $customerRepository, UserCheck $userCheck, VoucherTransferRepository $voucherTransferRepository)
    {
        $this->voucher_setup = $voucher_setup;
        $this->customerRepository = $customerRepository;
        $this->userCheck = $userCheck;
        $this->voucherTransferRepository = $voucherTransferRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!empty(array_filter($request->product_id))) {
            if (user_privileges_check('Voucher', $request->voucher_id, 'create_role')) {

                $voucher_invoice = '';
                $validator = Validator::make($request->all(), [
                    'invoice_no' => [
                        'required',
                        new StoreUniqueInvoiceValidation($request->invoice_no, $request->voucher_id),
                    ],
                    'product_name' => [
                        new ProductValidation($request->all(),$this->voucher_setup),
                    ],
                ]);


                if($validator->fails()){
                    if($validator->errors()->get('invoice_no')){
                        if (!empty($request->invoice)) {
                            $voucher_invoice= $this->voucher_setup->duplicateVoucherCheckValidation($request->voucher_id,$request->invoice_no);
                        }else{
                            return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                        }
                    }
                    if($validator->errors()->get('product_name')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }
                try {
                    $data = $this->voucherTransferRepository->storeTransfer($request, $voucher_invoice);

                    return RespondWithSuccess('Voucher Transfer successful  !! ', $data, 201);
                } catch (Exception $e) {
                    return RespondWithError('Voucher Transfer Not successful !!', $e->getMessage(), 404);
                }
            } else {
                abort(403);
            }
        } else {
            return RespondWithError('Product not Empty !!', 'Product not Empty', 404);
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
        if (user_privileges_check('Voucher', $id, 'display_role')) {
            $voucher = Voucher::find($id);
            $voucher_date = $this->voucher_setup->dateSetup($voucher);
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $voucher_invoice = $this->voucher_setup->invoiceSetup($voucher);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->voucher_setup->AccessVoucherDistributionCenter($voucher->distribution_center_id);;
            $destination_godowns = $this->voucher_setup->destinationGodownAccess($voucher->voucher_id);
            return view('admin.voucher.transfer.create_transfer', compact('godowns', 'voucher_date', 'branch_setup', 'voucher_invoice', 'voucher', 'customers', 'distributionCenter','destination_godowns'));
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
        $data = $this->voucherTransferRepository->getTransferId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'display_role')) {
            $voucher = Voucher::find($data->voucher_id);
            $godown_id_in = StockIn::where('tran_id', $id)->first(['godown_id']);
            if (!$godown_id_in) {
                $godown_id_in = GoodsInTransit::where('tran_id', $id)->first(['to_godown AS godown_id']);
            }
            $branch_setup = $this->voucher_setup->branchSetup($voucher);
            $godowns = $this->voucher_setup->godownAccess($voucher->voucher_id);
            $customers = $this->customerRepository->getCustomerOfIndex();
            $distributionCenter = $this->voucher_setup->AccessVoucherDistributionCenter($voucher->distribution_center_id);;
            $destination_godowns = $this->voucher_setup->destinationGodownAccess($voucher->voucher_id);
            return view('admin.voucher.transfer.edit_transfer', compact('branch_setup', 'data', 'voucher', 'godowns', 'customers', 'distributionCenter', 'voucher', 'godown_id_in','destination_godowns'));
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

            $voucher_invoice = '';
            if ($request->ch_4_dup_vou_no == 0) {

                $validator = Validator::make($request->all(), [
                    'invoice_no' => [
                        'required',
                        new  UpdateUniqueInvoiceValidation($id,$request->invoice_no,$request->voucher_id,$request->invoice_date),
                    ],
                    'product_name' => [
                        new ProductValidation($request->all(),$this->voucher_setup),
                    ],
                ]);

                if($validator->fails()){
                    if($validator->errors()->get('invoice_no')){
                     return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                    if($validator->errors()->get('product_name')){
                         return RespondWithError('validation Voucher error ', $validator->errors(), 422);
                    }
                }
            }
            try {
                $data = $this->voucherTransferRepository->updateTransfer($request, $id, $voucher_invoice);

                return RespondWithSuccess('Voucher  Transfer  Update  successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher  Transfer Update Not successful !!', $e->getMessage(), 404);
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
        $data = $this->voucherTransferRepository->getTransferId($id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucherTransferRepository->deleteTransfer($id);

                return RespondWithSuccess('Voucher  Transfer delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Transfer delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    public function stockOut_with_stockIn(Request $request)
    {
        try {
            $data = $this->voucher_setup->stockOut_with_stockIn($request->tran_id);

            return RespondWithSuccess('Voucher  Stock In And out successful  !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('Voucher Stock In And out Not successful !!', $e->getMessage(), 404);
        }
    }

    public function Cancel(Request $request){
        $data = $this->voucherTransferRepository->getTransferId($request->id);
        if (user_privileges_check('Voucher', $data->voucher_id, 'delete_role')) {
            try {
                $data = $this->voucher_setup->cancelStockInStockOut($request->id);
                $this->voucher_setup->transactionMasterNarrationUpdate($request);
                return RespondWithSuccess('Voucher Transfer delete successful  !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Voucher Transfer delete Not successful !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
