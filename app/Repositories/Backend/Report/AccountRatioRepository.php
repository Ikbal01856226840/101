<?php

namespace App\Repositories\Backend\Report;

use App\Services\Tree;
use Illuminate\Support\Facades\DB;
use App\Services\RateCalculation\ClosingBalance;
use App\Services\RateCalculation\OpeningBalance;
class AccountRatioRepository implements AccountRatioInterface
{

    private $closingBalance;
    private $openingBalance;
    public function __construct(ClosingBalance $closingBalance,OpeningBalance $openingBalance)
    {
        $this->closingBalance=$closingBalance;
        $this->openingBalance=$openingBalance;
    }

    function currentAsset($request){
        $currentAsset=$this->accountRatio($request,7);
        $totalCurrentAsset=(($currentAsset->debit+$currentAsset->ledger_head_opening_balance)-$currentAsset->credit);
        $closing=$this->closing($request);
        return $totalCurrentAsset+$closing;
    }

    function currentLiabilities($request){
        $currentLiability=$this->accountRatio($request,17);
        $totalCurrentLiability=(($currentLiability->credit+$currentLiability->ledger_head_opening_balance)-$currentLiability->debit);
        
        $AlArafaIslamiBankShortTermLoan=$this->accountRatio($request,842);
        $totalAlArafaIslamiBankShortTermLoan=(($AlArafaIslamiBankShortTermLoan->credit+$AlArafaIslamiBankShortTermLoan->ledger_head_opening_balance)-$AlArafaIslamiBankShortTermLoan->debit);
        
        $BasicShortTermLoan=$this->accountRatio($request,270);
        $totalBasicShortTermLoan=(($BasicShortTermLoan->credit+$BasicShortTermLoan->ledger_head_opening_balance)-$BasicShortTermLoan->debit);
        
        $IBBLShortTermLoan=$this->accountRatio($request,272);
        $totalIBBLShortTermLoan=(($IBBLShortTermLoan->credit+$IBBLShortTermLoan->ledger_head_opening_balance)-$IBBLShortTermLoan->debit);
        
        
        return $totalCurrentLiability+$totalAlArafaIslamiBankShortTermLoan+$totalBasicShortTermLoan+$totalIBBLShortTermLoan;
    }



    function investment($request){
        $investment=$this->accountRatio($request,15);
        $totalInvestment=(($investment->debit+$investment->ledger_head_opening_balance)-$investment->credit);
        return $totalInvestment;
    }


    function asset($request){
        $asset=$this->accountRatio($request,1);
        $totalAsset=(($asset->debit+$asset->ledger_head_opening_balance)-$asset->credit);
        return $totalAsset;
    }

    function liabilities($request){
        $branchDivision=$this->accountRatio($request,27);
        $totalbranchDivision=(($branchDivision->credit+$branchDivision->ledger_head_opening_balance)-$branchDivision->debit);
        
        $currentLiability=$this->accountRatio($request,17);
        $totalCurrentLiability=(($currentLiability->credit+$currentLiability->ledger_head_opening_balance)-$currentLiability->debit);
        

        $LoanLiabilities=$this->accountRatio($request,21);
        $totalLoanLiabilities=(($LoanLiabilities->credit+$LoanLiabilities->ledger_head_opening_balance)-$LoanLiabilities->debit);
        
        $SuspenseAccounts=$this->accountRatio($request,29);
        $totalSuspenseAccounts=(($SuspenseAccounts->credit+$SuspenseAccounts->ledger_head_opening_balance)-$SuspenseAccounts->debit);
        
        return $totalbranchDivision+$totalCurrentLiability+$totalLoanLiabilities+$totalSuspenseAccounts;
    }

    function sales($request){
        $sales=$this->accountRatioWithOutOpening($request,35);
        return $sales->credit-$sales->debit;
    }

    function purchase($request){
        $purchase=$this->accountRatioWithOutOpening($request,32);
        return $purchase->debit-$purchase->credit;
    }
    function opening($request){
        return $this->openingBalance->OpeningRate($request->from_date,$request->to_date)[0]->total_stock_total_out_opening;

    }

    function closing($request){
        return $this->closingBalance->ClosingRate($request->from_date,$request->to_date)[0]->total_val??0;
    }

    function LoanLiabilities($request){
        $LoanLiabilities=$this->accountRatio($request,21);
        $totalLoanLiabilities=(($LoanLiabilities->credit+$LoanLiabilities->ledger_head_opening_balance)-$LoanLiabilities->debit);
        return $totalLoanLiabilities;
    }

    function currentLiabilitiesWithoutShortTermLoan($request){
        $currentLiability=$this->accountRatio($request,17);
        $totalCurrentLiability=(($currentLiability->credit+$currentLiability->ledger_head_opening_balance)-$currentLiability->debit);        
        return $totalCurrentLiability;
    }

    function DirectIncome($request){
        $DirectIncome=$this->accountRatioWithOutOpening($request,33);
        return $DirectIncome->credit-$DirectIncome->debit;
    }

    function IndirectIncome($request){
        $IndirectIncome=$this->accountRatioWithOutOpening($request,34);
        return $IndirectIncome->credit-$IndirectIncome->debit;
    }

    function IndirectExpense($request){
        $IndirectExpense=$this->accountRatioWithOutOpening($request,31);
        return $IndirectExpense->debit-$IndirectExpense->credit;
    }

    function DirectExpenses($request){
        $DirectExpenses=$this->accountRatioWithOutOpening($request,30);
        return $DirectExpenses->debit-$DirectExpenses->credit;
    }

    


    public function accountRatio($request,$group_id){
        $query="WITH RECURSIVE tree AS (
                SELECT
                    group_chart.group_chart_id,
                    group_chart.nature_group
                FROM
                    group_chart
                WHERE
                    FIND_IN_SET(group_chart.group_chart_id, $group_id)
                UNION
                SELECT
                    e.group_chart_id,
                    e.nature_group
                FROM
                    tree h
                JOIN
                    group_chart e ON h.group_chart_id = e.under
            ),
            debit_summary AS (
                SELECT
                    ledger_head_id,
                    SUM(debit) AS total_debit,
                    SUM(credit) AS total_credit
                FROM
                    debit_credit
                LEFT JOIN transaction_master
                ON transaction_master.tran_id = debit_credit.tran_id
                WHERE  transaction_master.transaction_date <=  :to_date
                GROUP BY
                    ledger_head_id
            )

            SELECT
                group_chart.group_chart_id,
                ledger_head.ledger_name,
                group_chart.nature_group,
                SUM(COALESCE(debit_summary.total_debit, 0)) AS debit,
                SUM(COALESCE(debit_summary.total_credit, 0)) AS credit,
                SUM(CASE
                    WHEN group_chart.nature_group IN (1, 3) AND ledger_head.DrCr = 'Cr' THEN -IFNULL(ledger_head.opening_balance, 0)
                    WHEN group_chart.nature_group IN (2, 4) AND ledger_head.DrCr = 'Dr' THEN -IFNULL(ledger_head.opening_balance, 0)
                    ELSE IFNULL(ledger_head.opening_balance, 0)
                END) AS ledger_head_opening_balance

            FROM
                tree AS group_chart
            LEFT JOIN
                ledger_head ON ledger_head.group_id = group_chart.group_chart_id
            LEFT JOIN
                debit_summary ON debit_summary.ledger_head_id = ledger_head.ledger_head_id";
        $to_date = $request->to_date;
        $params = [];
        $params['to_date'] = $to_date;
       return DB::select($query,$params)[0];

    }

    public function accountRatioWithOutOpening($request,$group_id){

        $query="WITH RECURSIVE tree AS (
                SELECT
                    group_chart.group_chart_id,
                    group_chart.nature_group
                FROM
                    group_chart
                WHERE
                    FIND_IN_SET(group_chart.group_chart_id, $group_id)
                UNION
                SELECT
                    e.group_chart_id,
                    e.nature_group
                FROM
                    tree h
                JOIN
                    group_chart e ON h.group_chart_id = e.under
            ),
            debit_summary AS (
                SELECT
                    ledger_head_id,
                    SUM(debit) AS total_debit,
                    SUM(credit) AS total_credit
                FROM
                    debit_credit
                LEFT JOIN transaction_master
                ON transaction_master.tran_id = debit_credit.tran_id
                WHERE  transaction_master.transaction_date BETWEEN :from_date AND        :to_date
                GROUP BY
                    ledger_head_id
            )

            SELECT
                group_chart.group_chart_id,
                ledger_head.ledger_name,
                group_chart.nature_group,
                SUM(COALESCE(debit_summary.total_debit, 0)) AS debit,
                SUM(COALESCE(debit_summary.total_credit, 0)) AS credit


            FROM
                tree AS group_chart
            LEFT JOIN
                ledger_head ON ledger_head.group_id = group_chart.group_chart_id
            LEFT JOIN
                debit_summary ON debit_summary.ledger_head_id = ledger_head.ledger_head_id";
        $to_date = $request->to_date;
        $params = [];
        $params['from_date'] = $request->from_date;
        $params['to_date'] = $to_date;

       return DB::select($query,$params)[0];

    }
    //  public function liabilities($request,$group_id){
    //     $query="WITH RECURSIVE tree AS (
    //                     SELECT
    //                         group_chart.group_chart_id,
    //                         group_chart.nature_group
    //                     FROM
    //                         group_chart
    //                     WHERE
    //                         FIND_IN_SET(group_chart.group_chart_id,$group_id )
    //                     UNION
    //                     SELECT
    //                         e.group_chart_id,
    //                         e.nature_group
    //                     FROM
    //                         tree h
    //                     JOIN
    //                         group_chart e ON h.group_chart_id = e.under
    //                 ),
    //                 debit_summary AS (
    //                     SELECT
    //                         ledger_head_id,
    //                         SUM(debit) AS total_debit,
    //                         SUM(credit) AS total_credit
    //                     FROM
    //                         debit_credit
    //                     GROUP BY
    //                         ledger_head_id
    //                 )

    //                 SELECT
    //                     group_chart.group_chart_id,
    //                     ledger_head.ledger_name,
    //                     group_chart.nature_group,
    //                     SUM(COALESCE(debit_summary.total_debit, 0)) AS debit,
    //                     SUM(COALESCE(debit_summary.total_credit, 0)) AS credit,
    //                     SUM(CASE
    //                         WHEN group_chart.nature_group IN (2, 4) AND ledger_head.DrCr = 'Dr'
    //                         THEN -IFNULL(ledger_head.opening_balance, 0)
    //                         ELSE IFNULL(ledger_head.opening_balance, 0)
    //                     END) AS ledger_opening_balance

    //                 FROM
    //                     tree AS group_chart
    //                 LEFT JOIN
    //                     ledger_head ON ledger_head.group_id = group_chart.group_chart_id
    //                 LEFT JOIN
    //                     debit_summary ON debit_summary.ledger_head_id = ledger_head.ledger_head_id
    //                 LEFT JOIN
    //                     transaction_master ON transaction_master.tran_id = transaction_master.tran_id
    //                 WHERE
    //                     transaction.tran_date <= :to_date
    //     ";
    //     $to_date = $request->to_date;
    //     $params = [];
    //     $params['to_date'] = $to_date;
    //    return DB::select($query,$params);

    // }
}
