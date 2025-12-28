<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\GroupChart;
use App\Models\LegerHead;
use App\Models\StockGroup;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Repositories\Backend\Master\GroupChartRepository;


class DashboardController extends Controller
{
    private $groupChart;

    public function __construct(GroupChartRepository $groupChart)
    {
        $this->groupChart = $groupChart;
    }

    public function index()
    {
        return view('admin.dashboard');
    }

    public function mainIndex()
    {
        $group_chart = GroupChart::count();
        $ledger_head = LegerHead::count();
        $stock_group = StockGroup::count();
        $stock_item = StockItem::count();
        $users=User::select('user_name','address','created_at')->limit(5)->get();
        $top_sales=DB::select( "    SELECT      Sum(debit_credit.debit)    AS stock_total_debit,
                                                COUNT(debit_credit.debit)    AS stock_total_count,
                                                ledger_head.ledger_name

                                    FROM       transaction_master
                                    INNER JOIN voucher_setup
                                    ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                    INNER JOIN debit_credit
                                    ON         transaction_master.tran_id=debit_credit.tran_id
                                    INNER JOIN ledger_head
                                    ON         debit_credit.ledger_head_id=ledger_head.ledger_head_id
                                    WHERE     voucher_setup.voucher_type_id=19 AND debit_credit.dr_cr='Dr'AND debit_credit.commission IS NULL
                                    GROUP BY  ledger_head.ledger_head_id ORDER BY stock_total_debit DESC limit 5");


        return view('admin.main_dashboard', compact('group_chart', 'ledger_head', 'stock_group', 'stock_item','users','top_sales'));
    }

    public function dayWiseSales(){
    //    $from_date=date('Y-m-d', strtotime('-1 month'));
    //    $to_date=date('Y-m-d');
        $from_date=date('2024-01-01');
        $to_date=date('2024-02-01');
       $data =DB::select( "    SELECT      Sum(stock_out.total)    AS stock_total_sales,
                                           transaction_master.transaction_date
                                FROM       transaction_master
                                INNER JOIN stock_out
                                ON         transaction_master.tran_id=stock_out.tran_id
                                INNER JOIN voucher_setup
                                ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                WHERE      voucher_setup.voucher_type_id=19 AND transaction_master.transaction_date BETWEEN '$from_date' AND        '$to_date'
                                GROUP BY   day(transaction_master.transaction_date)");

        return RespondWithSuccess('Day Wise Sales show successful !! ', $data, 201);

    }
    public function topSalesReturnCustomer(){
              $data=DB::select( "       SELECT     SUM(debit_credit.credit)  AS ratio,
                                                   ledger_head.ledger_name
                                        FROM       transaction_master
                                        INNER JOIN voucher_setup
                                        ON         transaction_master.voucher_id=voucher_setup.voucher_id
                                        INNER JOIN debit_credit
                                        ON         transaction_master.tran_id=debit_credit.tran_id
                                        INNER JOIN ledger_head
                                        ON         debit_credit.ledger_head_id=ledger_head.ledger_head_id
                                        WHERE     voucher_setup.voucher_type_id=25 AND debit_credit.dr_cr='Cr'AND debit_credit.commission IS NULL
                                        GROUP BY  ledger_head.ledger_head_id ORDER BY ratio DESC limit 5");
           return RespondWithSuccess('Day Wise Sales show successful !! ', $data, 201);


    }
}
