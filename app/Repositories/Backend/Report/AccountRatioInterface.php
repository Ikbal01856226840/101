<?php

namespace App\Repositories\Backend\Report;

interface AccountRatioInterface
{
    public function currentAsset($request);
    public function currentLiabilities($request);
    public function investment($request);
    public function asset($request);
    public function liabilities($request);
    public function sales($request);
    public function purchase($request);
    public function opening($request);
    public function closing($request);
    public function LoanLiabilities($request);
    public function currentLiabilitiesWithoutShortTermLoan($request);
    public function DirectIncome($request);
    public function IndirectIncome($request);
    public function IndirectExpense($request);
    public function DirectExpenses($request);


}
