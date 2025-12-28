<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\BranchRepository;
use App\Repositories\Backend\CompanyRepository;
use App\Repositories\Backend\Master\CustomerRepository;
use Exception;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private $companyRepository;

    private $branch;

    private $customerRepository;

    public function __construct(CompanyRepository $companyRepository, BranchRepository $branchRepository, CustomerRepository $customerRepository)
    {
        $this->companyRepository = $companyRepository;
        $this->branch = $branchRepository;
        $this->customerRepository = $customerRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = $this->companyRepository->getCompanyOfIndex();
        $branchs = $this->branch->getBranchOfIndex();
        $customers = $this->customerRepository->getCustomerOfIndex();

        // user privileges checking
        if (user_privileges_check('menu', 'tab_company', 'create_role')) {
            return view('admin.company.company', compact('data', 'branchs', 'customers'));
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

        try {
            $data = $this->companyRepository->updateCompany($request, $id);

            return RespondWithSuccess('company update successfully !! ', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('company  update successfully', $e->getMessage(), 400);
        }
    }

    public function showCompany()
    {
        return view('admin.company.company_show');
    }

    public function create()
    {

        return view('admin.company.create_branch');

    }

    public function view()
    {

    }
}
