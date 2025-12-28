<?php

namespace App\Services\Voucher_setup;

use App\Repositories\Backend\AuthRepository;
use App\Repositories\Backend\Master\GodownRepository;
use App\Services\Tree;
use Illuminate\Support\Facades\DB;
use App\Services\Voucher_setup\Voucher_setup_trait;

class Voucher_setup
{
    use Voucher_setup_trait;

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
            return DB::table('transaction_master')->where('voucher_id',$request->voucher_id)->orderBy('tran_id', 'DESC')->first()->transaction_date??date('Y-m-d');
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
        if($request->auto_reset_invoice==1){
            if (!empty($request->invoice)) {
                $next_invoice = DB::table('track_voucher_setup')->where('invoice', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('id', 'DESC')->first();
                $transaction_voucher = DB::table('transaction_master')->where('invoice_no', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->count();
                if ($next_invoice && $transaction_voucher == 0) {
                    return $next_invoice->invoice;
                } else {

                    $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();
                    preg_match_all("/\d+/",$transaction_voucher->invoice_no, $number_check);

                    if($request->increment_invoice_number_satatus==2&&end($number_check[0])<>$request->increment_invoice_number){
                        preg_match_all("/\d+/", $request->invoice, $number);
                        return str_replace(end($number[0]),$request->increment_invoice_number??1, $request->invoice);

                    }else if (!empty($transaction_voucher)) {

                        if($request->increment_invoice_number_satatus<>1){
                            DB::table('voucher_setup')
                            ->where('voucher_id', $request->voucher_id) // Specify the condition to find the record(s) to update
                            ->update([
                                'increment_invoice_number_satatus' =>1,
                            ]);
                        }

                       return  $this->voucher_increment($transaction_voucher);
                    } else {

                        preg_match_all("/\d+/", $request->invoice, $number);
                        return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);;
                    }
                }
            } else {
                preg_match_all("/\d+/", $request->invoice, $number);
                return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);;
            }
        }elseif($request->auto_reset_invoice==2){

                $month1 = date('m', strtotime($request->invoice_date)); // Extract month from $date1
                $month2 = date('m', strtotime(date('Y-m-d'))); // Extract month from $date2
                if ($month1 == $month2) {

                    if (!empty($request->invoice)) {

                        $second_id= DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->skip(1)->first();
                        $next_invoice = DB::table('track_voucher_setup')->where('invoice', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('id', 'DESC')->first();
                        $transaction_voucher = DB::table('transaction_master')->where('invoice_no', $request->invoice)->where('voucher_id', $request->voucher_id)->whereRaw('MONTH(entry_date) = ?', [date('m')])->orderBy('tran_id', 'DESC')->count();
                        if ($second_id?->invoice_no!=$next_invoice?->invoice && $transaction_voucher == 0) {
                            return $next_invoice->invoice;
                        } else {
                            $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();
                            preg_match_all("/\d+/",$transaction_voucher->invoice_no, $number_check);

                            if($request->increment_invoice_number_satatus==2&&end($number_check[0])<>$request->increment_invoice_number){
                                preg_match_all("/\d+/", $request->invoice, $number);
                                return str_replace(end($number[0]),$request->increment_invoice_number??1, $request->invoice);;

                            }else if (!empty($transaction_voucher)) {

                                if($request->increment_invoice_number_satatus<>1){
                                    DB::table('voucher_setup')
                                    ->where('voucher_id', $request->voucher_id) // Specify the condition to find the record(s) to update
                                    ->update([
                                        'increment_invoice_number_satatus' =>1,
                                    ]);
                                }

                                return $this->voucher_increment($transaction_voucher);

                            } else {

                                preg_match_all("/\d+/", $request->invoice, $number);
                                return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);;
                            }
                        }
                    } else {
                        preg_match_all("/\d+/", $request->invoice, $number);
                        return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);;
                    }
                } else {
                    preg_match_all("/\d+/", $request->invoice, $number);
                    $new_invoice_month= str_replace(end($number[0]),$request->starting_number??1, $request->invoice);

                    $transaction_voucher = DB::table('transaction_master')
                                        ->where('voucher_id',$request->voucher_id)
                                        ->where('invoice_no',$new_invoice_month)
                                        ->whereRaw('MONTH(entry_date) = ?', [date('m')]) // Extract the month from entry_date
                                        ->orderBy('tran_id', 'DESC')
                                        ->first();

                    if (!empty($transaction_voucher)) {
                        DB::table('voucher_setup')
                        ->where('voucher_id', $request->voucher_id) // Specify the condition to find the record(s) to update
                        ->update([
                            'invoice_date' =>date('Y-m-d'),
                            'invoice'=>$transaction_voucher->invoice_no??1
                        ]);
                        return $this->voucher_increment($transaction_voucher);

                    }

                    preg_match_all("/\d+/", $request->invoice, $number);
                    return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);
                }

        }elseif($request->auto_reset_invoice==3){

                $yaer1 = date('Y', strtotime($request->invoice_date)); // Extract month from $date1
                $yaer2 = date('Y', strtotime(date('Y-m-d')));

                if ($yaer1 ==  $yaer2) {
                    if (!empty($request->invoice)) {

                        $next_invoice = DB::table('track_voucher_setup')->where('invoice', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('id', 'DESC')->first();
                        $transaction_voucher = DB::table('transaction_master')->where('invoice_no', $request->invoice)->where('voucher_id', $request->voucher_id)->whereRaw('YEAR(entry_date) = ?', [date('Y')])->orderBy('tran_id', 'DESC')->count();

                        if ($next_invoice && $transaction_voucher == 0) {
                            return $next_invoice->invoice;
                        } else {
                            $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('tran_id', 'DESC')->first();
                            preg_match_all("/\d+/",$transaction_voucher->invoice_no, $number_check);

                            if($request->increment_invoice_number_satatus==2&&end($number_check[0])<>$request->increment_invoice_number){
                                preg_match_all("/\d+/", $request->invoice, $number);
                                return str_replace(end($number[0]),$request->increment_invoice_number??1, $request->invoice);;
                            }
                            else if (!empty($transaction_voucher)) {
                                return  $this->voucher_increment($transaction_voucher);
                            } else {

                                preg_match_all("/\d+/", $request->invoice, $number);
                                return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);;
                            }
                        }
                    } else {
                        preg_match_all("/\d+/", $request->invoice, $number);
                        return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);;
                    }
                }else{
                        preg_match_all("/\d+/", $request->invoice, $number);
                        $new_invoice_year= str_replace(end($number[0]),$request->starting_number??1, $request->invoice);
                        $transaction_voucher = DB::table('transaction_master')
                                        ->where('voucher_id', $request->voucher_id)
                                        ->where('invoice_no',$new_invoice_year)
                                        ->whereRaw('YEAR(entry_date) = ?', [date('Y')]) // Extract the month from entry_date
                                        ->orderBy('tran_id', 'DESC')
                                        ->first();
                    if (!empty($transaction_voucher)) {
                        DB::table('voucher_setup')
                        ->where('voucher_id', $request->voucher_id) // Specify the condition to find the record(s) to update
                        ->update([
                            'invoice_date' =>date('Y-m-d'),
                            'invoice'=>$transaction_voucher->invoice_no??1
                        ]);
                        return  $this->voucher_increment($transaction_voucher);


                    }
                        preg_match_all("/\d+/", $request->invoice, $number);
                        return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);;
                }
        }

    }
    public function voucher_increment($transaction_voucher){
        preg_match_all("/\d+/", $transaction_voucher->invoice_no, $numbers);
        $all_numbers = $numbers[0];
        $last_number = array_pop($all_numbers);
        $incremented_last_number = $last_number + 1;
        $invoice_no = preg_replace('/' . $last_number . '$/', $incremented_last_number, $transaction_voucher->invoice_no);
        return $invoice_no;
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
        $searchTerm = '%' . $search . '%';
        return DB::select("with recursive tree as
                (SELECT group_chart.group_chart_id,group_chart.group_chart_name,group_chart.under,1 AS lvl FROM group_chart WHERE FIND_IN_SET(group_chart.group_chart_id,:group)
                UNION
                SELECT E.group_chart_id,E.group_chart_name,E.under,H.lvl+1 as lvl FROM tree H JOIN group_chart E ON H.group_chart_id=E.under
                )
                SELECT  ledger_head.ledger_head_id ,ledger_head.ledger_name,ledger_head.inventory_value FROM tree Left join ledger_head On ledger_head.group_id=tree.group_chart_id WHERE ledger_head.ledger_name LIKE :search LIMIT 10",['search' =>$searchTerm,'group' => $group]);
    }

    // public function ledger_head_searching($search)
    // {
    //     $keywords = explode(' ', $search);

    //     $query = DB::table('ledger_head as l')
    //         ->select(
    //             'l.ledger_head_id',
    //             'l.ledger_name',
    //             'l.inventory_value',
    //             'l.opening_balance',
    //             'group_chart.nature_group'
    //         )
    //         ->join('group_chart', 'l.group_id', '=', 'group_chart.group_chart_id');

    //     foreach ($keywords as $keyword) {
    //         $query->where('l.ledger_name', 'LIKE', "%$keyword%");
    //     }

    //     $data = $query->limit(20)->get();
    //     return $data;
    // }

    public function ledger_head_searching($search)
    { 
        if(unit_branch_first()==151 || unit_branch_first()==1){
            $keywords = explode(' ', $search);
            $search = implode("%", $keywords); 
        }else{
            $str = preg_replace('/([a-zA-Z])(\d)/', '$1 $2', $search);
            $str = str_replace("-", " ", $str);
            $keywords = explode(' ', $str);
            $search = implode("%", $keywords); 
        }
        $query = DB::table('ledger_head as l')
                ->select(
                    'l.ledger_head_id',
                    'l.ledger_name',
                    'l.inventory_value',
                    'l.opening_balance',
                    'group_chart.nature_group'
                )
                ->join('group_chart', 'l.group_id', '=', 'group_chart.group_chart_id')
                ->where('l.ledger_name', 'LIKE', "$search%")
                ->orWhere('l.alias', 'LIKE', "$search%");

            $data = $query->limit(20)->get();

            if ($data->count() < 20) {
                $limit = 20 - $data->count();

                // Get an array of existing ledger_head_ids from the first set of results
                $existingledgerheadids = $data->pluck('ledger_head_id')->toArray();
                // Adjust the query to search with more flexibility (matching any of the keywords)
                $queryNext = DB::table('ledger_head as l')
                ->select(
                    'l.ledger_head_id',
                    'l.ledger_name',
                    'l.inventory_value',
                    'l.opening_balance',
                    'group_chart.nature_group'
                )
                ->join('group_chart', 'l.group_id', '=', 'group_chart.group_chart_id')
                ->whereNotIn('l.ledger_head_id', $existingledgerheadids)
                ->where(function($query) use ($search) {
                    $query->where('l.ledger_name', 'LIKE', "%{$search}%")
                        ->orWhere('l.alias', 'LIKE', "%{$search}%");
                });
                // Get the remaining needed results
                $dataNext = $queryNext->limit($limit)->get();

                // Merge the results from the second query
                $data = $data->merge($dataNext);
            }

            return $data;
    }


    public function balanceDebitCredit($ledger_head_id)
    {
        return DB::select("SELECT group_chart.nature_group,ledger_head.ledger_head_id,ledger_head.ledger_name,
        SUM(CASE WHEN group_chart.nature_group=1 OR group_chart.nature_group =3  THEN Ifnull(debit_credit.debit,0) END)+(CASE  WHEN nature_group IN (1, 3) AND ledger_head.DrCr = 'Cr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END) AS total_debit1,
        SUM(CASE WHEN group_chart.nature_group=1 OR group_chart.nature_group =3  THEN debit_credit.credit END) AS total_credit1,
        SUM(CASE WHEN group_chart.nature_group=2 OR group_chart.nature_group =4 THEN debit_credit.debit END) AS total_debit2,
        SUM(CASE WHEN group_chart.nature_group=2 OR group_chart.nature_group =4 THEN Ifnull(debit_credit.credit,0) END)+(CASE WHEN nature_group IN (2, 4) AND ledger_head.DrCr = 'Dr' THEN -Ifnull(ledger_head.opening_balance, 0) ELSE Ifnull(ledger_head.opening_balance, 0) END) AS total_credit2  FROM group_chart LEFT JOIN ledger_head ON group_chart.group_chart_id=ledger_head.group_id
        LEFT JOIN debit_credit ON ledger_head.ledger_head_id=debit_credit.ledger_head_id WHERE ledger_head.ledger_head_id=:ledger_head_id",['ledger_head_id'=>$ledger_head_id]);
    }

    public function balanceDebitCreditCalculation($data)
    {
        if (!empty($data)) {
            if (($data[0]->total_debit1 != null) || ($data[0]->total_credit1 != null)) {
                $debit_balance = (((float) $data[0]->total_debit1) - ((float) $data[0]->total_credit1));

                return makeCurrency(number_format((float) abs($debit_balance), company()->amount_decimals, '.', '')) . (($debit_balance >= 0) ? ' Dr' : ' Cr');
            } elseif (($data[0]->nature_group == 1) || ($data[0]->nature_group == 3)) {
                $debit_null_balance = 0;

                return makeCurrency(number_format((float) abs($debit_null_balance), company()->amount_decimals, '.', '')) . (($debit_null_balance >= 0) ? ' Dr' : ' Cr');
            } elseif (($data[0]->total_debit2 != null) || ($data[0]->total_credit2 != null)) {
                $credit_balance = (((float) $data[0]->total_credit2) - ((float) $data[0]->total_debit2));

                return makeCurrency(number_format((float) abs($credit_balance), company()->amount_decimals, '.', '')) . (($credit_balance >= 0) ? ' Cr' : ' Dr');
            } elseif (($data[0]->nature_group == 2) || ($data[0]->nature_group == 4)) {
                $credit_null_balance = 0;

                return makeCurrency(number_format((float) abs($credit_null_balance), company()->amount_decimals, '.', '')) . (($credit_null_balance >= 0) ? ' Cr' : ' Dr');
            }
        }
    }

    public function godownAccess($voucher_id)
    {

        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        $voucher_godown = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();
        if (array_sum(array_map('intval', explode(' ', $get_user->godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id',explode(",",$get_user->godown_id))->get(['godown_id', 'godown_name']);
        } elseif (array_sum(array_map('intval', explode(' ', $voucher_godown->godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id',explode(",",$voucher_godown->godown_id))->get(['godown_id', 'godown_name']);
        } else {
            return $this->godownRepository->getGodownOfIndex();
        }
    }

    // public function search_item($search)
    // {
    //     $keywords = explode(' ', $search);

    //     $query = DB::table('stock_item')
    //         ->select(
    //             'stock_item.stock_item_id',
    //             'stock_item.product_name',
    //             'unitsof_measure.unit_of_measure_id',
    //             'unitsof_measure.symbol'
    //         )
    //         ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id');

    //     // Add grouped conditions for product_name and alias columns
    //     $query->where(function ($subQuery) use ($keywords) {
    //         $subQuery->where(function ($innerQuery) use ($keywords) {
    //             foreach ($keywords as $keyword) {
    //                 $innerQuery->where('stock_item.product_name', 'LIKE', "%$keyword%");
    //             }
    //         })->orWhere(function ($innerQuery) use ($keywords) {
    //             foreach ($keywords as $keyword) {
    //                 $innerQuery->where('stock_item.alias', 'LIKE', "%$keyword%");
    //             }
    //         });
    //     });

    //     $data = $query->limit(20)->get();

    //     return $data;
    // }

    public function search_item($search)
    {
        if(unit_branch_first()==151  || unit_branch_first()==1){
            $keywords = explode(' ', $search);
            $search = implode("%", $keywords); 
        }else{
            $str = preg_replace('/([a-zA-Z])(\d)/', '$1 $2', $search);
            $str = str_replace("-", " ", $str);
            $keywords = explode(' ', $str);
            $search = implode("%", $keywords); 
        }
        // Start the initial query
        $query = DB::table('stock_item')
            ->select(
                'stock_item.stock_item_id',
                'stock_item.product_name',
                'unitsof_measure.unit_of_measure_id',
                'unitsof_measure.symbol'
            )
            ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
            ->where('stock_item.product_name', 'LIKE', "$search%")
            ->orWhere('stock_item.alias', 'LIKE', "$search%");

        // Get the first batch of data (limit to 20)
        $data = $query->limit(20)->get();

        // If there are fewer than 20 results, run a secondary query with more flexible conditions
        if ($data->count() < 20) {
            $limit = 20 - $data->count();

            // Get an array of existing stock_item_ids from the first set of results
            $existingStockItemIds = $data->pluck('stock_item_id')->toArray();
            // Adjust the query to search with more flexibility (matching any of the keywords)
            $queryNext = DB::table('stock_item')
                ->select(
                    'stock_item.stock_item_id',
                    'stock_item.product_name',
                    'unitsof_measure.unit_of_measure_id',
                    'unitsof_measure.symbol'
                )
                ->leftJoin('unitsof_measure', 'stock_item.unit_of_measure_id', '=', 'unitsof_measure.unit_of_measure_id')
                ->whereNotIn('stock_item.stock_item_id', $existingStockItemIds)
                    ->where(function($query) use ($search) {
                        $query->where('stock_item.product_name', 'LIKE', "%$search%")
                        ->orWhere('stock_item.alias', 'LIKE', "%$search%");
                });
            // Get the remaining needed results
            $dataNext = $queryNext->limit($limit)->get();

            // Merge the results from the second query
            $data = $data->merge($dataNext);
        }

        return $data;
    }




    public function godownAccessSearch($search, $voucher_id)
    {
        $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        $voucher_godown = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();
        if (array_sum(array_map('intval', explode(' ', $get_user->godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id',explode(",",$get_user->godown_id))->where('godown_name', 'like', '%' . $search . '%')->get(['godown_id', 'godown_name']);
        } elseif (array_sum(array_map('intval', explode(' ', $voucher_godown->godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id',explode(",",$voucher_godown->godown_id))->where('godown_name', 'like', '%' . $search . '%')->get(['godown_id', 'godown_name']);
        } else {
            return DB::select("SELECT godowns.godown_name,godowns.godown_id  FROM godowns WHERE godowns.godown_name LIKE'%$search%' LIMIT 10");
        }
    }

    public function destinationGodownAccess($voucher_id)
    {
        $voucher_godown = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->first();
        if (array_sum(array_map('intval', explode(' ', $voucher_godown->destination_godown_id))) != 0) {
            return DB::table('godowns')->whereIn('godown_id',explode(",",$voucher_godown->destination_godown_id))->get(['godown_id', 'godown_name']);
        } else {
            return $this->godownRepository->getGodownOfIndex();
        }
    }

    public function stockItemPrice($item_id, $price_type,$voucher_type_id=1,$godown_id=1,$tran_date=null)
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
        }elseif ($price_type == 7) {
            if($voucher_type_id==22){
                $result = DB::select("SELECT GodownWiseRateCal(?, ?) AS rate", [$item_id, $godown_id]);


                return  $result?$result[0]:0;
            }else{
                return DB::table('stock')
                ->select('stock.current_rate AS rate')
                ->where('stock.stock_item_id', $item_id)
                ->orderBy('stock.id', 'DESC')
                ->limit(1)
                ->first();
            }

        } else {
            $params = [];
            $query="select `price_id`, `rate` from `price_setup` where `price_setup`.`price_type` =:price_type_1 and `price_setup`.`stock_item_id` =:item_id_1 and `price_setup`.`setup_date` = (SELECT MAX(p.setup_date) as price_date FROM price_setup as p WHERE p.setup_date <=:tran_date AND p.price_type=:price_type_2 AND p.stock_item_id=:item_id_2 LIMIT 1) LIMIT 1";
            $params['price_type_1'] = $price_type;
            $params['item_id_1'] = $item_id;
            $params['price_type_2'] = $price_type;
            $params['item_id_2'] = $item_id;
            $params['tran_date'] = $tran_date;
            $data = DB::select( $query,$params);
            return $data ? $data[0] : 0;
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
        if (array_sum(array_map('intval', explode(' ', $debit_group->debit_group_id))) != 0) {
            $data = $this->group_chart($debit_group->debit_group_id, $search_name);
        } elseif (array_sum(array_map('intval', explode(' ', $debit_group->debit_group_id))) != 0) {
            $data = $this->group_chart($debit_group->credit_group_id, $search_name);
        } elseif (array_sum(array_map('intval', explode(' ', $get_user->agar))) != 0) {
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
        if (array_sum(array_map('intval', explode(' ', $credit_group->credit_group_id))) != 0) {
            $data = $this->group_chart($credit_group->credit_group_id, $search_name);
        } elseif (array_sum(array_map('intval', explode(' ', $credit_group->credit_group_id))) != 0) {
            $data = $this->group_chart($credit_group->credit_group_id, $search_name);
        } elseif (array_sum(array_map('intval', explode(' ', $get_user->agar))) != 0) {
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

        // $get_user = $this->authRepository->findUserGet(Auth()->user()->id);
        // voucher wise multiple group chart
        if ((array_sum(array_map('intval', explode(' ', $debit_group->debit_group_id))) != 0) && ($debit_credit == 0)) {
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
        elseif ((array_sum(array_map('intval', explode(' ', $debit_group->credit_group_id))) != 0) && ($debit_credit == 1)) {
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
        // elseif (array_sum(array_map('intval', explode(' ', $get_user->agar))) != 0) {
        //     $group_chart_data = $this->tree->group_chart_tree_row_query($get_user->agar);
        //     $keys = array_keys(array_column($this->tree->group_chart_tree_row_query($get_user->agar), 'lvl'), 1);
        //     $new_array = array_map(function ($k) use ($group_chart_data) {
        //         return $group_chart_data[$k];
        //     }, $keys);

        //     for ($i = 0; $i < count($keys); $i++) {
        //         return $data .= $this->tree->getTreeViewSelectOptionLedgerTree($this->tree->group_chart_tree_row_query($get_user->agar), $new_array[$i]['under']);
        //     }
        // } 
        else {
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

    public function stock_in_stock_out_sum_qty($item_id, $godown_id,$allowAllStock=0)
    {
        if(!$godown_id && $allowAllStock){
            $stock_in_sum = DB::table('stock_in')->where('stock_item_id', '=', $item_id)->sum('qty');
            $stock_out_sum = DB::table('stock_out')->where('stock_item_id', '=', $item_id)->sum('qty');
            $current_qty = (double) ((double) ($stock_in_sum) - (double) ($stock_out_sum));
            return $current_qty;
        }

        $stock_in_sum = DB::table('stock_in')->where('stock_item_id', '=', $item_id)->where('godown_id', '=', $godown_id)->sum('qty');
        $stock_out_sum = DB::table('stock_out')->where('stock_item_id', '=', $item_id)->where('godown_id', '=', $godown_id)->sum('qty');
        $current_qty = (double) ((double) ($stock_in_sum) - (double) ($stock_out_sum));
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
                DB::raw('(SELECT st_in_1.stock_in_id  FROM stock_in as st_in_1
                              WHERE st_in_1.stock_item_id=stock_out.stock_item_id  AND  st_in_1.tran_id=stock_out.tran_id GROUP BY st_in_1.stock_item_id)as stock_in_id'),
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
            ->orderBy('price_setup.price_id', 'DESC', 'stock_group_commission.setup_date', 'DESC', 'stock_group_commission.group_commission_id', 'DESC')
            ->first(['price_id', 'rate', 'commission']);
    }

    public function stock_item_commission_with_stock_price($item_id, $price_type,$tran_date=null)
    {

       $data= DB::table('stock_item')
            ->select(
                DB::raw("(select rate from  price_setup
                                           where price_setup.price_type ='" . $price_type . "' and `price_setup`.`stock_item_id` = '" . $item_id . "'
                                           and `price_setup`.`setup_date` = (SELECT MAX(p.setup_date) as price_date FROM price_setup as p
                                           WHERE p.setup_date <='" . $tran_date . "' AND p.price_type='" . $price_type . "' AND p.stock_item_id='" . $item_id . "' LIMIT 1) LIMIT 1 )as rate"),
                DB::raw("(select commission from  stock_item_commission_setup
                                           where stock_item_commission_setup.stock_item_id= '" . $item_id . "'
                                           and `stock_item_commission_setup`.`setup_date` = (SELECT MAX(p.setup_date) as setup_date FROM stock_item_commission_setup as p
                                            WHERE p.setup_date <='" . $tran_date . "'  AND p.stock_item_id='" . $item_id . "' LIMIT 1) LIMIT 1 )as commission"),
                'stock_group.sales_ledger'
            )
            ->leftJoin('stock_group', 'stock_group.stock_group_id', '=', 'stock_item.stock_group_id')
            ->where('stock_item.stock_item_id', $item_id)
            ->first();

       if($data && $data->commission){
            return $data;
       }else{

        return   DB::table('stock_item')
        ->select(
            DB::raw("(select rate from  price_setup
                                       where price_setup.price_type ='" . $price_type . "' and `price_setup`.`stock_item_id` = '" . $item_id . "'
                                       and `price_setup`.`setup_date` = (SELECT MAX(p.setup_date) as price_date FROM price_setup as p
                                       WHERE p.setup_date <='" . $tran_date . "' AND p.price_type='" . $price_type . "' AND p.stock_item_id='" . $item_id . "' LIMIT 1) LIMIT 1 )as rate"),
            DB::raw("(select commission from  stock_group_commission
                                       where  stock_group_commission.stock_group_id=stock_item.stock_group_id
                                       and  stock_group_commission.setup_date= (SELECT MAX(p.setup_date) as setup_date FROM  stock_group_commission as p
                                       WHERE p.setup_date <='" . $tran_date . "' AND p.stock_group_id=stock_item.stock_group_id LIMIT 1) LIMIT 1 )as commission"),
             DB::raw("(select commission_type from  stock_group_commission
                                        where  stock_group_commission.stock_group_id=stock_item.stock_group_id
                                        and  stock_group_commission.setup_date= (SELECT MAX(p.setup_date) as setup_date FROM  stock_group_commission as p
                                        WHERE p.setup_date <='" .$tran_date . "'  AND p.stock_group_id=stock_item.stock_group_id LIMIT 1) LIMIT 1 )as commission_type"),
            'stock_group.sales_ledger'
        )
        ->leftJoin('stock_group', 'stock_group.stock_group_id', '=', 'stock_item.stock_group_id')
        ->where('stock_item.stock_item_id', $item_id)
        ->first();
       }
    }

    public function DebitCreditWithSum($id)
    {
        return DB::table('debit_credit')
            ->select(
                'debit_credit.debit_credit_id',
                'debit_credit.ledger_head_id',
                'ledger_head.ledger_name',
                'ledger_head.opening_balance',
                'ledger_head.DrCr',
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

    public function posPrice($item_id, $shop_id, $price_type)
    {
        return DB::table('stock_item')
            ->select(
                DB::raw("(select rate from  price_setup
                                       where price_setup.price_type ='" . $price_type . "' and `price_setup`.`stock_item_id` = '" . $item_id . "' and `price_setup`.`setup_date` = (SELECT MAX(p.setup_date) as price_date FROM price_setup as p WHERE p.setup_date <=CURRENT_DATE() AND p.price_type='" . $price_type . "' AND p.stock_item_id='" . $item_id . "' LIMIT 1) LIMIT 1 )as rate"),
                DB::raw("(select discount from  offer_setup
                                       where offer_setup.stock_group_id=stock_item.stock_group_id
                                       and  offer_setup.dis_cen_id='" . $shop_id . "'
                                       and offer_setup.date_from<='" . date('Y-m-d') . "' and offer_setup.date_to>='" . date('Y-m-d') . "' )as discount"),
            )
            ->where('stock_item.stock_item_id', $item_id)
            ->first();
    }
    public function cancelDebitCredit($id){
        return DB::table('debit_credit')->where('tran_id',$id)->delete();
    }

    public function cancelStockInStockOut($id){
        try {
            DB::beginTransaction();

            DB::table('transaction_master')->where('tran_id',$id)->update(
                ['delivery_status'=> 0]
            );
            DB::table('order_approver')->where('tran_id', $id)->delete();

            // Perform the delete operations
            DB::table('stock_in')->where('tran_id', $id)->delete();
            DB::table('stock_out')->where('tran_id', $id)->delete();

            // Commit the transaction
            DB::commit();

            return true;
        } catch (\PDOException $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Optionally, handle the exception (log it, rethrow it, etc.)
            return false;
        }
    }

    public function cancelStockInDebitCredit($id){
        try {
            DB::beginTransaction();
            $debit_credits=DB::table('debit_credit')->where('tran_id',$id)->get();
            foreach($debit_credits as $debit_credit ){
                if($debit_credits[0]->debit_credit_id==$debit_credit->debit_credit_id){
                    DB::table('debit_credit')->where('debit_credit_id',$debit_credit->debit_credit_id)->update(
                        ['debit' => 0, 'credit' => 0]
                    );
                }else{
                    DB::table('debit_credit')->where('debit_credit_id',$debit_credit->debit_credit_id)->delete();
                }

            }
            // Perform the delete operations
            DB::table('stock_in')->where('tran_id', $id)->delete();
            // Commit the transaction
            DB::commit();

            return true;
        } catch (\PDOException $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Optionally, handle the exception (log it, rethrow it, etc.)
            return false;
        }
    }
    public function cancelStockOutDebitCredit($id){
        try {
            DB::beginTransaction();

            DB::table('transaction_master')->where('tran_id',$id)->update(
                ['delivery_status'=> 0]
            );
            DB::table('order_approver')->where('tran_id', $id)->delete();

             $debitCreditIds = DB::table('debit_credit')
                                ->where('tran_id', $id)
                                ->pluck('debit_credit_id');

            if ($debitCreditIds->isNotEmpty()) {
                $firstId = $debitCreditIds->first();
               
               DB::table('debit_credit')
                    ->where('tran_id', $id)
                    ->where('debit_credit_id', $firstId)
                    ->update(['debit' => 0, 'credit' => 0]);
                
                 DB::table('debit_credit')
                    ->where('tran_id', $id)
                    ->where('debit_credit_id', '!=', $firstId)
                    ->delete();
            }
             
             DB::table('debit_credit')
                     ->where('tran_id', $id)
                    ->update(['debit' => 0, 'credit' => 0]);
                    
            // Perform the delete operations
            DB::table('stock_out')->where('tran_id', $id)->delete();
            // Commit the transaction
            DB::commit();

            return true;
        } catch (\PDOException $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Optionally, handle the exception (log it, rethrow it, etc.)
            return false;
        }
    }
    
    public function cancelStockinAndStockOutDebitCredit($id){
        try {
            DB::beginTransaction();
            DB::table('debit_credit')->where('tran_id',$id)->delete();
            // Perform the delete operations
            DB::table('stock_in')->where('tran_id', $id)->delete();
            DB::table('stock_out')->where('tran_id', $id)->delete();
            // Commit the transaction
            DB::commit();

            return true;
        } catch (\PDOException $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Optionally, handle the exception (log it, rethrow it, etc.)
            return false;
        }
    }
    public function cancelStockOutDebitCreditPOS($id){
        try {
            DB::beginTransaction();
                DB::table('transaction_master')->where('tran_id',$id)->update(
                    ['received_amount' => 0]
                );
                $debit_credits=DB::table('debit_credit')->where('tran_id',$id)->get();
                $credit=DB::table('debit_credit')->where('tran_id',$id)->where('dr_cr','Dr')->first();
                foreach($debit_credits as $debit_credit ){
                    if($credit->debit_credit_id==$debit_credit->debit_credit_id){
                        DB::table('debit_credit')->where('debit_credit_id',$debit_credit->debit_credit_id)->update(
                            ['debit' => 0, 'credit' => 0]
                        );
                    }else{
                        DB::table('debit_credit')->where('debit_credit_id',$debit_credit->debit_credit_id)->delete();
                    }

                }
                // Perform the delete operations
                DB::table('stock_out')->where('tran_id', $id)->delete();
                // Commit the transaction
            DB::commit();

            return true;
        } catch (\PDOException $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Optionally, handle the exception (log it, rethrow it, etc.)
            return false;
        }
    }

    public function cancelCommissionVoucher($id){
        try {
            DB::beginTransaction();

            DB::table('transaction_master')->where('tran_id',$id)->update(
                ['voucher_status'=> 1]
            );

            DB::table('debit_credit')->where('tran_id',$id)->update(
                ['debit' => 0, 'credit' => 0]
            );
            // Perform the delete operations
            DB::table('stock_item_commission')->where('tran_id', $id)->delete();
            // Commit the transaction
            DB::commit();

            return true;
        } catch (\PDOException $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Optionally, handle the exception (log it, rethrow it, etc.)
            return false;
        }
    }
    
    public function duplicateVoucherCheck($request){
        $count=DB::table('transaction_master')->where('voucher_id',$request->voucher_id)->where('invoice_no',$request->invoice_no)->count();
        if(!empty($request->invoice_no)){
            $duplicates = DB::table('transaction_master')
            ->select('invoice_no')
            ->where('voucher_id', $request->voucher_id)
            ->where('invoice_no', 'like',  $request->invoice_no . '%')
            ->orderBy('transaction_master.transaction_date', 'DESC')
            ->orderBy('transaction_master.tran_id', 'DESC')
            ->orderBy('transaction_master.invoice_no', 'ASC')
            ->limit(5)
            ->get();
            if(count($duplicates)<=0){
                $duplicates = DB::table('transaction_master')
                ->select('invoice_no')
                ->where('voucher_id', $request->voucher_id)
                ->where('invoice_no', 'like', '%' . $request->invoice_no . '%')
                ->orderBy('transaction_master.transaction_date', 'DESC')
                ->orderBy('transaction_master.tran_id', 'DESC')
                ->orderBy('transaction_master.invoice_no', 'ASC')
                ->limit(5)
                ->get();
            }
        }else{
            $duplicates = [];
        }
       

        return ['count'=>$count,'duplicates'=>$duplicates];
    }

    public function transactionMasterNarrationUpdate($request){
        DB::table('transaction_master')->where('tran_id',$request->id)->update(
            ['narration' => $request->narration??'']
        );
    }

    public function duplicateVoucherCheckValidation($voucher_id,$invoice){
        $voucher= DB::table('voucher_setup')->where('voucher_id',$voucher_id)->first();
         if($voucher->auto_reset_invoice==1){
             $transaction_voucher = DB::table('transaction_master')->where('voucher_id',$voucher_id)->orderBy('tran_id', 'DESC')->first();
             preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
             return  str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);

         }elseif($voucher->auto_reset_invoice==2){
             $month1 = date('m', strtotime($voucher->invoice_date)); // Extract month from $date1
             $month2 = date('m', strtotime(date('Y-m-d'))); // Extract month from $date2
             if ($month1 == $month2) {
                 $transaction_voucher_check = DB::table('transaction_master')
                 ->where('voucher_id',$voucher_id)
                 ->where('invoice_no', $invoice)
                 ->whereRaw('MONTH(entry_date) = ?', [date('m')]) // Extract the month from entry_date
                 ->orderBy('tran_id', 'DESC')
                 ->first();
                 if(!empty($transaction_voucher_check)){
                     $transaction_voucher = DB::table('transaction_master')->where('voucher_id',$voucher_id)->orderBy('tran_id', 'DESC')->first();
                     preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
                     return str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                 }else{
                     return $invoice;
                 }

             } else {
                 return $invoice;
             }
         }elseif($voucher->auto_reset_invoice==3){
             $yaer1 = date('Y', strtotime($voucher->invoice_date)); // Extract month from $date1
             $yaer2 = date('Y', strtotime(date('Y-m-d')));
                     if ($yaer1 ==  $yaer2) {
                         $transaction_voucher_check = DB::table('transaction_master')
                         ->where('voucher_id',$voucher_id)
                         ->where('invoice_no', $invoice)
                         ->whereRaw('YEAR(entry_date) = ?', [date('Y')]) // Extract the month from entry_date
                         ->orderBy('tran_id', 'DESC')
                         ->first();

                         if(!empty($transaction_voucher_check)){
                             $transaction_voucher = DB::table('transaction_master')->where('voucher_id',$voucher_id)->orderBy('tran_id', 'DESC')->first();
                                 preg_match_all("/\d+/", $transaction_voucher->invoice_no, $number);
                                 return str_replace(end($number[0]), end($number[0]) + 1, $transaction_voucher->invoice_no);
                           }
                     } else {
                         return $invoice;
                     }
          }
    }

    public function bill_of_material_searching($search)
    {
        $searchTerm = '%' . $search . '%';
        $data = DB::select("SELECT  bom.bom_id, bom.bom_name FROM bom WHERE bom.bom_name LIKE :search LIMIT 25",['search' =>$searchTerm]);
        return $data;

    }

    public function bill_of_material_qty($id,$voucher_id,$godown_id,$godown_out)
    {
        $bom_in = DB::table('bom_details')
        ->select(
            DB::raw('(SELECT SUM(st_in.qty)
                    FROM stock_in as st_in
                    WHERE st_in.stock_item_id=bom_details.stock_item_id
                    AND st_in.godown_id=' . $godown_id . '
                    GROUP BY st_in.stock_item_id) as stock_in_sum'),

            DB::raw('(SELECT SUM(st_out.qty)
                    FROM stock_out as st_out
                    WHERE st_out.stock_item_id=bom_details.stock_item_id
                    AND st_out.godown_id=' . $godown_id . '
                    GROUP BY st_out.stock_item_id) as stock_out_sum'),

            'bom_details.details_id',
            'bom_details.stock_item_id',
            'bom_details.qty',
            'stock_item.product_name'
        )
        ->join('stock_item', 'bom_details.stock_item_id', '=', 'stock_item.stock_item_id')
        ->where('details_copy', 1)
        ->where('bom_id', $id)
        ->get()
        ->map(function ($item) use ($voucher_id) {
            $priceTypeId = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->value('price_type_id');
            $item->rate = $this->stockItemPrice($item->stock_item_id, $priceTypeId);
            return $item;
        });

        $bom_out= DB::table('bom_details')
        ->select(
            DB::raw('(SELECT SUM(st_in.qty)
                    FROM stock_in as st_in
                    WHERE st_in.stock_item_id=bom_details.stock_item_id
                    AND st_in.godown_id=' . $godown_out . '
                    GROUP BY st_in.stock_item_id) as stock_in_sum'),

            DB::raw('(SELECT SUM(st_out.qty)
                    FROM stock_out as st_out
                    WHERE st_out.stock_item_id=bom_details.stock_item_id
                    AND st_out.godown_id=' . $godown_out . '
                    GROUP BY st_out.stock_item_id) as stock_out_sum'),

            'bom_details.details_id',
            'bom_details.stock_item_id',
            'bom_details.qty',
            'stock_item.product_name'
        )
        ->join('stock_item', 'bom_details.stock_item_id', '=', 'stock_item.stock_item_id')
        ->where('details_copy', 2)
        ->where('bom_id', $id)
        ->get()
        ->map(function ($item) use ($voucher_id) {
            $priceTypeId = DB::table('voucher_setup')->where('voucher_id', $voucher_id)->value('price_type_id');
            $item->rate = $this->stockItemPrice($item->stock_item_id, $priceTypeId);
            return $item;
        });
        return ['bom_in'=>$bom_in,'bom_out'=>$bom_out];

    }

    public function AccessVoucherDistributionCenter($id)
    {
       if(!empty($id)){
         return  DB::table('distribution_center')->where('dis_cen_id',$id)->get();
       }else{
         return  DB::table('distribution_center')->get();
       }

    }
    public function PurchaseOrderInvoice($request){
        if (!empty($request->invoice)) {
            $next_invoice = DB::table('track_voucher_setup')->where('invoice', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('id', 'DESC')->first();
            $transaction_voucher = DB::table('order_requisition_transaction_master')->where('invoice_no', $request->invoice)->where('voucher_id', $request->voucher_id)->orderBy('id', 'DESC')->count();
            if ($next_invoice && $transaction_voucher == 0) {
                return $next_invoice->invoice;
            } else {

                $transaction_voucher = DB::table('order_requisition_transaction_master')->where('voucher_id', $request->voucher_id)->orderBy('id', 'DESC')->first();
                preg_match_all("/\d+/",$transaction_voucher->invoice_no, $number_check);

                if($request->increment_invoice_number_satatus==2&&end($number_check[0])<>$request->increment_invoice_number){
                    preg_match_all("/\d+/", $request->invoice, $number);
                    return str_replace(end($number[0]),$request->increment_invoice_number??1, $request->invoice);

                }else if (!empty($transaction_voucher)) {

                    if($request->increment_invoice_number_satatus<>1){
                        DB::table('voucher_setup')
                        ->where('voucher_id', $request->voucher_id) // Specify the condition to find the record(s) to update
                        ->update([
                            'increment_invoice_number_satatus' =>1,
                        ]);
                    }

                   return  $this->voucher_increment($transaction_voucher);
                } else {

                    preg_match_all("/\d+/", $request->invoice, $number);
                    return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);
                }
            }
        } else {
            preg_match_all("/\d+/", $request->invoice, $number);
            return str_replace(end($number[0]),$request->starting_number??1, $request->invoice);
        }
    }
    public function AccessVoucherSetup()
    {

        if(Auth()->user()->user_level==1){
            return DB::table('voucher_setup')->select('voucher_setup.voucher_type_id', 'voucher_setup.voucher_name', 'voucher_setup.voucher_id', 'voucher_type.voucher_type')->leftJoin('voucher_type', 'voucher_type.voucher_type_id', '=', 'voucher_setup.voucher_type_id')->orderBy('voucher_setup.voucher_type_id', 'ASC')->get();
        }else{
            // Fetch user access titles as an array
            $user_access = DB::table('user_privileges')
            ->where('table_user_id', Auth()->user()->id)
            ->where('status_type', 'Voucher')
            ->where('display_role', 1)
            ->pluck('title_details'); // Pluck the 'title_details' column as a collection
        // Convert collection to an array for `whereIn` usage
        $user_access_array=0;
        if($user_access->isNotEmpty()){
            $user_access_array = implode(',', array_map('intval', $user_access->toArray()));
        }

        // Fetch voucher setup data
        return DB::table('voucher_setup')
            ->select(
                'voucher_setup.voucher_type_id',
                'voucher_setup.voucher_name',
                'voucher_setup.voucher_id',
                'voucher_type.voucher_type',
                DB::raw('(SELECT COUNT(s.voucher_id)
                          FROM voucher_setup as s
                          WHERE s.voucher_type_id = voucher_setup.voucher_type_id) as total_count'), // Total count per type
                DB::raw('(SELECT COUNT(s.voucher_id)
                          FROM voucher_setup as s
                          WHERE s.voucher_type_id = voucher_setup.voucher_type_id
                          AND s.voucher_id IN (' . $user_access_array . ')) as filtered_count') // Count of matching vouchers
            )
            ->leftJoin('voucher_type', 'voucher_type.voucher_type_id', '=', 'voucher_setup.voucher_type_id')
            ->whereIn('voucher_setup.voucher_id', $user_access) // Use the array of title_details
            ->orderBy('voucher_setup.voucher_type_id', 'ASC')
            ->get();


        }

    }
}
