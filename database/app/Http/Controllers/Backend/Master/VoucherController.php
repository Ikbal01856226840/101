<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\VoucherStoreRequest;
use App\Http\Requests\Voucher\VoucherUpdateRequest;
use App\Models\VoucherType;
use App\Repositories\Backend\BranchRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Repositories\Backend\Master\LegerHeadRepository;
use App\Repositories\Backend\Master\VoucherRepository;
use Exception;

class VoucherController extends Controller
{
    private $voucher;

    private $branch;

    private $godown;

    private $ledger;

    private $groupChart;

    public function __construct(VoucherRepository $voucherRepository, BranchRepository $branchRepository, GodownRepository $godownRepository, LegerHeadRepository $legerHeadRepository, GroupChartRepository $group_chart)
    {
        $this->voucher = $voucherRepository;
        $this->branch = $branchRepository;
        $this->godown = $godownRepository;
        $this->ledger = $legerHeadRepository;
        $this->groupChart = $group_chart;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (user_privileges_check('master', 'Voucher Type', 'display_role')) {
            return view('admin.master.voucher.index');
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show getVoucher.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVoucher()
    {
        if (user_privileges_check('master', 'Voucher Type', 'display_role')) {
            try {
                $data = $this->voucher->getVoucherOfIndex();

                return RespondWithSuccess('All voucher list  successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All voucher list not show successfully !!', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in create.
     */
    public function create()
    {
        $voucher_types = VoucherType::all(['voucher_type_id', 'voucher_type']);
        $branch = $this->branch->getBranchOfIndex();
        $godown = $this->godown->getGodownOfIndex();
        $debitLedger = $this->ledger->debitLedgerTreeSelectOption();
        $creditLedger = $this->ledger->creditLedgerTreeSelectOption();
        $group_chart = $this->groupChart->getTreeSelectOption();

        if (user_privileges_check('master', 'Voucher Type', 'create_role')) {
            return view('admin.master.voucher.create', compact('voucher_types', 'branch', 'godown', 'debitLedger', 'creditLedger', 'group_chart'));
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VoucherStoreRequest $request)
    {
        if (user_privileges_check('master', 'Voucher Type', 'create_role')) {
            try {
                $data = $this->voucher->StoreVoucher($request);

                return RespondWithSuccess('voucher create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('voucher not create successfully !!', $e->getMessage(), 400);
            }
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
    public function show($id)
    {
        $voucher = $this->voucher->getVoucherId($id);
        $voucher_types = VoucherType::all(['voucher_type_id', 'voucher_type']);
        $branch = $this->branch->getBranchOfIndex();
        $godown = $this->godown->getGodownOfIndex();
        $debitLedger = $this->ledger->debitLedgerTreeSelectOption();
        $creditLedger = $this->ledger->creditLedgerTreeSelectOption();
        $group_chart = $this->groupChart->getTreeSelectOption();
        $voucher_count = $this->voucher->current_invoice($id);
        $next_invoice = $this->voucher->next_invoice($id);

        if (user_privileges_check('master', 'Voucher Type', 'alter_role')) {
            return view('admin.master.voucher.edit', compact('voucher_types', 'branch', 'godown', 'debitLedger', 'creditLedger', 'voucher', 'group_chart', 'voucher_count', 'next_invoice'));
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VoucherUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Voucher Type', 'alter_role')) {
            try {
                $data = $this->voucher->updateVoucher($request, $id);

                return RespondWithSuccess('voucher update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('voucher update not successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Voucher Type', 'delete_role')) {
            try {
                $data = $this->voucher->deleteVoucher($id);

                return RespondWithSuccess('voucher delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('voucher not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
