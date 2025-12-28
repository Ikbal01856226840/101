<?php

namespace App\Repositories\Backend\Report;

use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\AuthRepository;

class POSRepository implements POSInterface
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository=$authRepository;
    }

    public function salesList($request){
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);

        if (isset($request)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d');
        }
            $query=DB::table('transaction_master')
                        ->select('transaction_master.tran_id','transaction_master.invoice_no','transaction_master.narration','transaction_master.other_details','voucher_setup.voucher_name','voucher_setup.voucher_type_id','ledger_head.ledger_name','transaction_master.transaction_date','stock_out.stock_out_qty')
                        ->leftJoin('debit_credit', 'transaction_master.tran_id', '=', 'debit_credit.tran_id')
                        ->leftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
                        ->leftJoin('voucher_setup', 'transaction_master.voucher_id', '=', 'voucher_setup.voucher_id')
                        ->leftJoin(DB::raw('(SELECT Sum(stock_out.qty) AS stock_out_qty,stock_out.tran_id,stock_out.godown_id FROM `stock_out` GROUP BY stock_out.tran_id) stock_out'),
                                        function($join)
                                        {
                                            $join->on('transaction_master.tran_id', '=', 'stock_out.tran_id');
                                        })
                        ->whereIn('voucher_setup.voucher_type_id', [30,27]);
                        if (array_sum(array_map('intval',explode(' ', $get_user->godown_id))) != 0) {
                            $query->whereIn('stock_out.godown_id', [$get_user->godown_id]);
                        }
                  return  $query->whereBetween('transaction_master.transaction_date',[$from_date,$to_date])
                        ->groupBy('transaction_master.tran_id')
                        ->get();

    }

}
