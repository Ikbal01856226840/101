<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\AccountRatioRepository;
use Exception;
use Illuminate\Http\Request;

class AccountRatioController extends Controller
{
    private $accountRatioRepository;

    public function __construct(AccountRatioRepository $accountRatioRepository)
    {

        $this->accountRatioRepository = $accountRatioRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountRatio()
    {
        if (user_privileges_check('report', 'AccountRatio', 'display_role')) {
            return view('admin.report.account_summary.account_ratio');
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountRatioData(Request $request)
    {
        if (user_privileges_check('report', 'AccountRatio', 'display_role')) {
            try {
                $currentAsset=$this->accountRatioRepository->currentAsset($request);
                $currentLiabilities=$this->accountRatioRepository->currentLiabilities($request);
                $investment=$this->accountRatioRepository->investment($request);
                $asset=$this->accountRatioRepository->asset($request);
                $liabilities=$this->accountRatioRepository->liabilities($request);
                $sales=$this->accountRatioRepository->sales($request);
                $purchase=$this->accountRatioRepository->purchase($request);
                $opening=$this->accountRatioRepository->opening($request);
                $closing=$this->accountRatioRepository->closing($request);
                $loanLiabilities=$this->accountRatioRepository->LoanLiabilities($request);
                $DirectIncome=$this->accountRatioRepository->DirectIncome($request);
                $IndirectIncome=$this->accountRatioRepository->IndirectIncome($request);
                $DirectExpenses=$this->accountRatioRepository->DirectExpenses($request);
                $IndirectExpense=$this->accountRatioRepository->IndirectExpense($request);


                $currentLiabilitiesWithoutShortTermLoan=$this->accountRatioRepository->currentLiabilitiesWithoutShortTermLoan($request);
                $data['workingCapital']=($currentAsset)-$currentLiabilities;
                $data['currentRatio']=($currentAsset)/$currentLiabilities;
                $data['quickRatio']=($currentAsset-$closing)/$currentLiabilities;
                $totalEquity=$asset-$liabilities;

                $data['lequageRatio']=($asset)/($liabilities);
                $cogs=($opening+$purchase)-$closing;
                $NetIncome=($sales+$DirectIncome+$IndirectIncome)-($cogs+$DirectExpenses+$IndirectExpense);
                $data['cogs']=$cogs;
                $data['gpm']=(($sales-$cogs)/$sales)*100;
                $data['der']=($loanLiabilities+$currentLiabilitiesWithoutShortTermLoan)/$totalEquity;
                $data['npm']=($NetIncome/$sales)*100;
                $data['sales']=$sales;
                $data['DirectIncome']=$DirectIncome;
                $data['IndirectIncome']=$IndirectIncome;
                $data['DirectExpenses']=$DirectExpenses;
                $data['IndirectExpense']=$IndirectExpense;
                $data['itr']=($cogs/(($opening+$closing)/2));
                // $debtRatio=;
                // $assetRatio=;
                // $cashRatio=;

                return RespondWithSuccess('Account Ratio successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('Account Ratio successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

}
