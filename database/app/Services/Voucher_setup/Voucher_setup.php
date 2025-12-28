<?php

namespace App\Services\Voucher_setup;

use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Services\Tree;
use Illuminate\Support\Facades\DB;

class Voucher_setup
{
    private $authRepository;

    private $godownRepository;

    private $tree;

    public function __construct(AuthRepository $authRepository, GodownRepository $godownRepository, Tree $tree)
    {
        $this->authRepository = $authRepository;
        $this->godownRepository = $godownRepository;
        $this->tree = $tree;
    }

    public function dateSetup($request)
    {
        if ($request->select_date == 'current_date') {
            return date('Y-m-d');
        } elseif ($request->select_date == 'last_insert_date') {
            return DB::table('transaction_master')->max('transaction_date');
        } elseif ($request->select_date == 'fix_date') {
            return $request->fix_date_create;
        }
    }

    public function branchSetup($request)
    {

        if ($request->branch_id == 0) {
            if (Auth()->user()->unit_or_branch == 0) {
                return DB::table('unit_branch_setup')->get();
            } else {
                return DB::table('unit_branch_setup')->where('id', Auth()->user()->unit_or_branch)->get();
            }
        } else {
            return DB::table('unit_branch_setup')->where('id', $request->branch_id)->get();
        }
    }

    public function invoiceSetup($request)
    {

        if (! empty($request->invoice)) {
            $next_invoice = DB::table('track_voucher_setup')->where('invoice', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('id', 'DESC')->first();

            $transaction_voucher = DB::table('transaction_master')->where('invoice_no', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->count();
            if ($next_invoice && $transaction_voucher == 0) {
                return $next_invoice->invoice;
            } else {
                $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();
                if (! empty($transaction_voucher)) {
                    preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);

                    return str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                } else {

                    return $request->invoice;
                }
            }
        } else {
            return $request->invoice;
        }
    }

    public function debit_setup($request)
    {

        if ($request->debit) {
            return $this->balanceDebitCredit($request->debit);
        }
    }

    public function credit_setup($request)
    {
        if ($request->credit) {
            return $this->balanceDebitCredit($request->credit);
        }
    }

    public function group_chart($group, $search)
    {
        return DB::select("with recursive tree as
                (SELECT group_chart.group_chart_id,group_chart.group_chart_name,group_chart.under,1 AS lvl FROM group_chart WHERE FIND_IN_SET(group_chart.group_chart_id, '$group')
                UNION
                SELECT E.group_chart_id,E.group_chart_name,E.under,H.lvl+1 as lvl FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
                )
                SELECT  ledger_head.ledger_head_id ,ledger_head.ledger_name,ledger_head.inventory_value FROM tree Left join ledger_head On ledger_head.group_id=tree.group_chart_id WHERE ledger_head.ledger_name LIKE '%$search%' LIMIT 10");
    }

    public function ledger_head_searching($search)
    {
        $data = DB::select("SELECT l.ledger_head_id ,l.ledger_name,l.inventory_value,l.opening_balance FROM ledger_head  as l WHERE l.ledger_name LIKE '%$search%' LIMIT 10");
        if ($data) {
            return $data;
        } else {
            return DB::select("SELECT l.ledger_head_id , l.ledger_name,l.inventory_value,l.opening_balance FROM ledger_head  as l WHERE l.ledger_name LIKE '%$search%' LIMIT 10");
        }
    }

    public function balanceDebitCredit($ledger_head_id)
    {
        return DB::select("SELECT group_chart.nature_group,ledger_head.ledger_head_id,ledger_head.ledger_name,
            SUM(CASE WHEN group_chart.nature_group=1 OR group_chart.nature_group =3  THEN Ifnull(debit_credit.debit,0) END)+Ifnull(ledger_head.opening_balance,0) AS total_debit1,
            SUM(CASE WHEN group_chart.nature_group=1 OR group_chart.nature_group =3  THEN debit_credit.credit END) AS total_credit1,
            SUM(CASE WHEN group_chart.nature_group=2 OR group_chart.nature_group =4 THEN debit_credit.debit END) AS total_debit2,
            SUM(CASE WHEN group_chart.nature_group=2 OR group_chart.nature_group =4 THEN Ifnull(debit_credit.credit,0) END)+Ifnull(ledger_head.opening_balance,0) AS total_credit2  FROM group_chart LEFT JOIN ledger_head ON group_chart.group_chart_id=ledger_head.group_id
            LEFT JOIN debit_credit ON ledger_head.ledger_head_id=debit_credit.ledger_head_id WHERE ledger_head.ledger_head_id=$ledger_head_id");
    }

    public function balanceDebitCreditCalculation($data)
    {
        if (! empty($data)) {
            if (($data[0]->total_debit1 != null) || ($data[0]->total_credit1 != null)) {
                $debit_balance = (((float) $data[0]->total_debit1) - ((float) $data[0]->total_credit1));

                return number_format((float) abs($debit_balance), company()->amount_decimals, '.', '').(($debit_balance >= 0) ? ' Dr' : ' Cr');
            } elseif (($data[0]->nature_group == 1) || ($data[0]->nature_group == 3)) {
                $debit_null_balance = 0;

                return number_format((float) abs($debit_null_balance), company()->amount_decimals, '.', '').(($debit_null_balance >= 0) ? ' Dr' : ' Cr');
            } elseif (($data[0]->total_debit2!=null) || ($data[0]->total_credit2!= null)) {
                $credit_balance = (((float) $data[0]->total_credit2) - ((float) $data[0]->total_debit2));

                return number_format((float) abs($credit_balance), company()->amount_decimals, '.', '').(($credit_balance >= 0) ? ' Cr' : ' Dr');
            } elseif (($data[0]->nature_group == 2) || ($data[0]->nature_group == 4)) {
                $credit_null_balance = 0;

                return number_format((float) abs($credit_null_balance), company()->amount_decimals, '.', '').(($credit_null_balance >= 0) ? ' Cr' : ' Dr');
            }
        }
    }

    public function godownAccess($voucher_id)
    {

        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        $voucher_godown = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();
        if (array_sum(array_map('intval',explode(' ', $get_user->godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id', [$get_user->godown_id])->get(['godown_id', 'godown_name']);
        } elseif (array_sum(array_map('intval',explode(' ', $voucher_godown->godown_id)))!= 0) {
            return DB::table('godowns')->whereIn('godown_id', [$voucher_godown->godown_id])->get(['godown_id', 'godown_name']);
        } else {
            return $this->godownRepository->getGodownOfIndex();
        }
    }

    public function search_item($search)
    {
        $data = DB::select("SELECT stock_item.stock_item_id ,stock_item.product_name,unitsof_measure.unit_of_measure_id,unitsof_measure.symbol FROM stock_item LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id  WHERE  stock_item.product_name LIKE'$search%' LIMIT 10");
        if ($data) {
            return $data;
        } else {
            return DB::select("SELECT stock_item.stock_item_id ,stock_item.product_name,unitsof_measure.unit_of_measure_id,unitsof_measure.symbol FROM stock_item LEFT JOIN unitsof_measure ON stock_item.unit_of_measure_id=unitsof_measure.unit_of_measure_id  WHERE  stock_item.product_name LIKE'%$search%' LIMIT 10");
        }
    }

    public function godownAccessSearch($search, $voucher_id)
    {
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        $voucher_godown = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();
        if (array_sum(array_map('intval',explode(' ', $get_user->godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id', [$get_user->godown_id])->where('godown_name', 'like', '%'.$search.'%')->get(['godown_id', 'godown_name']);
        } elseif (array_sum(array_map('intval',explode(' ', $voucher_godown->godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id', [$voucher_godown->godown_id])->where('godown_name', 'like', '%'.$search.'%')->get(['godown_id', 'godown_name']);
        } else {
            return DB::select("SELECT godowns.godown_name,godowns.godown_id  FROM godowns WHERE godowns.godown_name LIKE'%$search%' LIMIT 10");
        }
    }

    public function stockItemPrice($item_id, $price_type)
    {
        if ($price_type == 5) {
            return DB::table('stock_in')
                ->select('stock_in.rate')
                ->where('stock_in.stock_item_id', $item_id)
                ->orderBy('stock_in.stock_in_id', 'DESC')
                ->limit(1)
                ->first();
        } elseif ($price_type == 6) {
            $average_rate = DB::table('stock_item')->select(DB::raw('(SUM(stock_in.total)+SUM(stock_out.total))/(SUM(stock_in.qty)+SUM(stock_out.qty)) AS rate'))
                ->leftJoin('stock_in', 'stock_in.stock_item_id', '=', 'stock_item.stock_item_id')
                ->leftJoin('stock_out', 'stock_out.stock_item_id', '=', 'stock_item.stock_item_id')
                ->where('stock_item.stock_item_id', '=', $item_id)
                ->first();

            return $average_rate;

        } else {
            $data = DB::select("select `price_id`, `rate` from `price_setup` where `price_setup`.`price_type` ='".$price_type."' and `price_setup`.`stock_item_id` = '".$item_id."' and `price_setup`.`setup_date` = (SELECT MAX(p.setup_date) as price_date FROM price_setup as p WHERE p.setup_date <=CURRENT_DATE() AND p.price_type='".$price_type."' AND p.stock_item_id='".$item_id."' LIMIT 1) LIMIT 1");
            return $data?$data[0]:0;
        }

    }

    public function stockIn($id)
    {
        return DB::table('stock_in')
            ->select('stock_in.stock_in_id', 'stock_in.tran_id', 'stock_in.stock_item_id', 'stock_in.qty', 'stock_in.rate', 'stock_in.total', 'stock_in.remark', 'stock_item.product_name', 'godowns.godown_id', 'godowns.godown_name', 'unitsof_measure.symbol')
            ->leftJoin('stock_item', 'stock_in.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_in.godown_id', '=', 'godowns.godown_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('stock_in.tran_id', $id)
            ->get();
    }

    // searching Ledger  debit
    public function searchingLedgerDataGet($search_name, $voucher_id)
    {
        $debit_group = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        if (array_sum(array_map('intval',explode(' ', $debit_group->debit_group_id))) != 0) {
            $data = $this->group_chart($debit_group->debit_group_id, $search_name);
        } elseif (array_sum(array_map('intval',explode(' ', $debit_group->debit_group_id))) != 0) {
            $data = $this->group_chart($debit_group->credit_group_id, $search_name);
        } elseif (array_sum(array_map('intval',explode(' ', $get_user->agar))) != 0) {
            $data = $this->group_chart($get_user->agar, $search_name);
        } else {
            $data = $this->ledger_head_searching($search_name);
        }

        return $data;
    }

    // searching Ledger  credit
    public function searchingLedgerDataGetCredit($search_name, $voucher_id)
    {
        $credit_group = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        if (array_sum(array_map('intval',explode(' ',$credit_group->credit_group_id))) != 0) {
            $data = $this->group_chart($credit_group->credit_group_id, $search_name);
        } elseif (array_sum(array_map('intval',explode(' ',$credit_group->credit_group_id))) != 0) {
            $data = $this->group_chart($credit_group->credit_group_id, $search_name);
        } elseif (array_sum(array_map('intval',explode(' ', $get_user->agar))) != 0) {
            $data = $this->group_chart($get_user->agar, $search_name);
        } else {
            $data = $this->ledger_head_searching($search_name);
        }

        return $data;
    }

    // select drop down option
    public function optionLedger($group_id, $under, $voucher_id, $debit_credit = 0)
    {
        $data = '';
        $debit_group = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();

        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        // voucher wise multiple group chart
        if ((array_sum(array_map('intval',explode(' ', $debit_group->debit_group_id))) != 0) && ($debit_credit == 0)) {
            $group_chart_data = $this->tree->group_chart_tree_row_query($debit_group->debit_group_id);
            $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($debit_group->debit_group_id), 'lvl'), 1);
            $new_array = array_map(function ($k) use ($group_chart_data) {
                return $group_chart_data[$k];
            }, $keys);

            for ($i = 0; $i < count($keys); $i++) {
                return $data .= $this->tree->getTreeViewSelectOptionLedgerTree($this->tree->group_chart_tree_row_query($debit_group->debit_group_id), $new_array[$i]['under']);
            }
        }
        // voucher wise multiple group chart
        elseif ((array_sum(array_map('intval',explode(' ',$debit_group->credit_group_id))) != 0) && ($debit_credit == 1)) {
            $group_chart_data = $this->tree->group_chart_tree_row_query($debit_group->credit_group_id);
            $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($debit_group->credit_group_id), 'lvl'), 1);
            $new_array = array_map(function ($k) use ($group_chart_data) {
                return $group_chart_data[$k];
            }, $keys);
            for ($i = 0; $i < count($keys); $i++) {
                return $data .= $this->tree->getTreeViewSelectOptionLedgerTree($this->tree->group_chart_tree_row_query($debit_group->credit_group_id), $new_array[$i]['under']);
            }
        }
        // user wise multiple group chart
        elseif (array_sum(array_map('intval',explode(' ', $get_user->agar))) != 0) {
            $group_chart_data = $this->tree->group_chart_tree_row_query($get_user->agar);
            $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
            $new_array = array_map(function ($k) use ($group_chart_data) {
                return $group_chart_data[$k];
            }, $keys);

            for ($i = 0; $i < count($keys); $i++) {
                return $data .= $this->tree->getTreeViewSelectOptionLedgerTree($this->tree->group_chart_tree_row_query($get_user->agar), $new_array[$i]['under']);
            }
        } else {
            return $this->tree->getTreeViewSelectOptionLedgerTree($this->tree->group_chart_tree_row_query($group_id), $under);
        }
    }

    public function stockOut($id)
    {
        return DB::table('stock_out')
            ->select(
                'stock_out.stock_out_id',
                'stock_out.tran_id',
                'stock_out.stock_item_id',
                'stock_out.qty',
                'stock_out.rate',
                'stock_out.total',
                'stock_out.disc',
                'stock_out.remark',
                'stock_item.product_name',
                'godowns.godown_id',
                'godowns.godown_name',
                'unitsof_measure.symbol',
                DB::raw('(SELECT SUM(st_in.qty)  FROM stock_in as st_in
                              WHERE st_in.stock_item_id=stock_out.stock_item_id AND st_in.godown_id=stock_out.godown_id  GROUP BY st_in.stock_item_id )as stock_in_sum'),
                DB::raw('(SELECT SUM(st_out.qty)  FROM stock_out as st_out

                         WHERE st_out.stock_item_id=stock_out.stock_item_id AND st_out.godown_id=stock_out.godown_id  GROUP BY st_out.stock_item_id )as stock_out_sum')
            )
            ->leftJoin('stock_item', 'stock_out.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_out.godown_id', '=', 'godowns.godown_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('stock_out.tran_id', $id)
            ->get();
    }

    public function stock_in_stock_out_sum_qty($item_id,$godown_id)
    {

        $stock_in_sum = DB::table('stock_in')->where('stock_item_id', '=', $item_id)->where('godown_id', '=', $godown_id)->sum('qty');
        $stock_out_sum = DB::table('stock_out')->where('stock_item_id', '=', $item_id)->where('godown_id', '=',$godown_id)->sum('qty');
        $current_qty = (int) ((int) ($stock_in_sum) - (int) ($stock_out_sum));
        return $current_qty;
    }

    public function stockOut_with_stockIn($id)
    {
        return DB::table('stock_out')
            ->select(
                'stock_out.stock_out_id',
                'stock_out.tran_id',
                'stock_out.stock_item_id',
                'stock_out.qty',
                'stock_out.rate',
                'stock_out.total',
                'stock_out.remark',
                'stock_item.product_name',
                'godowns.godown_id',
                'godowns.godown_name',
                'unitsof_measure.symbol',
                'stock_in.stock_in_id',

                DB::raw('(SELECT SUM(st_in.qty)  FROM stock_in as st_in
                              WHERE st_in.stock_item_id=stock_out.stock_item_id AND st_in.godown_id=stock_out.godown_id   GROUP BY st_in.stock_item_id )as stock_in_sum'),

                DB::raw('(SELECT SUM(st_out.qty)  FROM stock_out as st_out
                         WHERE st_out.stock_item_id=stock_out.stock_item_id AND st_out.godown_id=stock_out.godown_id  GROUP BY st_out.stock_item_id )as stock_out_sum')
            )
            ->leftJoin('stock_in', 'stock_out.tran_id', '=', 'stock_in.tran_id')
            ->leftJoin('stock_item', 'stock_out.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_out.godown_id', '=', 'godowns.godown_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('stock_out.tran_id', $id)
            ->groupBy('stock_out.stock_out_id')
            ->get();
    }

    public function stockIn_with_current_stock($id)
    {
        return DB::table('stock_in')
            ->select(
                'stock_in.stock_in_id',
                'stock_in.tran_id',
                'stock_in.stock_item_id',
                'stock_in.qty',
                'stock_in.rate',
                'stock_in.total',
                'stock_in.remark',
                'stock_item.product_name',
                'godowns.godown_id',
                'godowns.godown_name',
                'unitsof_measure.symbol',
                DB::raw('(SELECT SUM(st_in.qty)  FROM stock_in as st_in
                           WHERE st_in.stock_item_id=stock_in.stock_item_id AND st_in.godown_id=stock_in.godown_id  GROUP BY st_in.stock_item_id )as stock_in_sum'),

                DB::raw('(SELECT SUM(st_out.qty)  FROM stock_out as st_out
                           WHERE st_out.stock_item_id=stock_in.stock_item_id AND st_out.godown_id=stock_in.godown_id  GROUP BY st_out.stock_item_id )as stock_out_sum')
            )

            ->leftJoin('stock_item', 'stock_in.stock_item_id', '=', 'stock_item.stock_item_id')
            ->leftJoin('godowns', 'stock_in.godown_id', '=', 'godowns.godown_id')
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('stock_in.tran_id', $id)
            ->get();
    }

    public function stock_group_commission_with_stock_price($item_id, $price_type)
    {
        return DB::table('stock_item')->leftJoin('price_setup', 'stock_item.stock_item_id', '=', 'price_setup.stock_item_id')
            ->leftJoin('stock_group_commission', 'stock_item.stock_group_id', '=', 'stock_group_commission.stock_group_id')
            ->where('price_setup.price_type', $price_type)
            ->where('stock_item.stock_item_id', $item_id)
            ->orderBy('price_setup.price_id', 'DESC', 'stock_group_commission.group_commission_id', 'DESC')
            ->first(['price_id', 'rate', 'commission']);
    }

    public function stock_item_commission_with_stock_price($item_id, $price_type)
    {
        // return DB::table('stock_item')
        //     ->leftJoin('price_setup', 'stock_item.stock_item_id', '=', 'price_setup.stock_item_id')
        //     ->leftJoin('stock_item_commission_setup', 'stock_item.stock_item_id', '=', 'stock_item_commission_setup.stock_item_id')
        //     ->where('price_setup.price_type', $price_type)
        //     ->where('price_setup.setup_date',DB::raw("(SELECT MAX(p.setup_date) as price_date FROM price_setup as p WHERE p.setup_date <=CURRENT_DATE() AND p.price_type='".$price_type."' AND p.stock_item_id='".$item_id."' LIMIT 1)"))
        //     ->where('stock_item.stock_item_id', $item_id)
        //     ->orderBy('price_setup.price_id', 'DESC', 'stock_item_commission_setup.item_commission_id', 'DESC')
        //     ->first(['price_id', 'rate', 'commission']);
            return DB::table('stock_item')
                    ->select( DB::raw("(select rate from  price_setup
                                           where price_setup.price_type ='".$price_type."' and `price_setup`.`stock_item_id` = '".$item_id."' 
                                           and `price_setup`.`setup_date` = (SELECT MAX(p.setup_date) as price_date FROM price_setup as p 
                                           WHERE p.setup_date <=CURRENT_DATE() AND p.price_type='".$price_type."' AND p.stock_item_id='".$item_id."' LIMIT 1) LIMIT 1 )as rate"),
                              DB::raw("(select commission from  stock_item_commission_setup
                                           where stock_item_commission_setup.stock_item_id= '".$item_id."' 
                                           and `stock_item_commission_setup`.`setup_date` = (SELECT MAX(p.setup_date) as setup_date FROM stock_item_commission_setup as p 
                                            WHERE p.setup_date <=CURRENT_DATE()  AND p.stock_item_id='".$item_id."' LIMIT 1) LIMIT 1 )as commission"),
                                       )
                    ->where('stock_item.stock_item_id', $item_id)
                    ->first();
    }

    public function DebitCreditWithSum($id)
    {
        return DB::table('debit_credit')
            ->select(
                'debit_credit.debit_credit_id',
                'debit_credit.ledger_head_id',
                'ledger_head.ledger_name',
                'ledger_head.opening_balance',
                'debit_credit.debit',
                'debit_credit.credit',
                'debit_credit.comm_level',
                'debit_credit.dr_cr',
                'debit_credit.remark',
                'debit_credit.commission',
                'debit_credit.commission_type',
                'group_chart.nature_group',
                DB::raw('IF(group_chart.nature_group=1 OR group_chart.nature_group=3,(SELECT SUM(debit_credit_sum_in.debit)  FROM debit_credit as debit_credit_sum_in WHERE debit_credit.ledger_head_id=debit_credit_sum_in.ledger_head_id Group by debit_credit_sum_in.ledger_head_id  ),0) as debit_sum_1'),
                DB::raw('IF(group_chart.nature_group=1 OR group_chart.nature_group=3,(SELECT SUM(credit_sum_in.credit)  FROM debit_credit as credit_sum_in WHERE debit_credit.ledger_head_id=credit_sum_in.ledger_head_id Group by credit_sum_in.ledger_head_id  ),0) as credit_sum_1'),
                DB::raw('IF(group_chart.nature_group=2 OR group_chart.nature_group=4,(SELECT SUM(debit_credit_sum_in_2.debit)  FROM debit_credit as debit_credit_sum_in_2 WHERE debit_credit.ledger_head_id=debit_credit_sum_in_2.ledger_head_id Group by debit_credit_sum_in_2.ledger_head_id  ),0) as debit_sum_2'),
                DB::raw('IF(group_chart.nature_group=2 OR group_chart.nature_group=4,(SELECT SUM(credit_sum_in_2.credit)  FROM debit_credit as credit_sum_in_2 WHERE debit_credit.ledger_head_id=credit_sum_in_2.ledger_head_id Group by credit_sum_in_2.ledger_head_id  ),0) as credit_sum_2')
            )
            ->leftJoin('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
            ->leftJoin('group_chart', 'ledger_head.group_id', '=', 'group_chart.group_chart_id')
            ->where('debit_credit.tran_id', $id)

            ->get();
    }

    public function id_wise_debit_credit_data($tran_id, $dr_cr, $commission)
    {
        return DB::table('debit_credit')
            ->Join('ledger_head', 'debit_credit.ledger_head_id', '=', 'ledger_head.ledger_head_id')
            ->where('tran_id', $tran_id)->where('dr_cr', $dr_cr)->where('commission', $commission)->first(['debit_credit.debit_credit_id', 'debit_credit.ledger_head_id', 'ledger_head.ledger_name', 'debit_credit.debit', 'debit_credit.credit']);
    }

    public function posPrice($item_id,$shop_id,$price_type){
        return DB::table('stock_item')
                    ->select( DB::raw("(select rate from  price_setup
                                       where price_setup.price_type ='".$price_type."' and `price_setup`.`stock_item_id` = '".$item_id."' and `price_setup`.`setup_date` = (SELECT MAX(p.setup_date) as price_date FROM price_setup as p WHERE p.setup_date <=CURRENT_DATE() AND p.price_type='".$price_type."' AND p.stock_item_id='".$item_id."' LIMIT 1) LIMIT 1 )as rate"),
                              DB::raw("(select discount from  offer_setup
                                       where offer_setup.stock_group_id=stock_item.stock_group_id
                                       and  offer_setup.dis_cen_id='".$shop_id."'
                                       and offer_setup.date_from<='".date('Y-m-d')."' and offer_setup.date_to>='".date('Y-m-d')."' )as discount"),
                                       )
                    ->where('stock_item.stock_item_id', $item_id)
                    ->first();
    }
}
