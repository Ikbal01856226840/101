<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerStoreRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Repositories\Backend\Master\CustomerRepository;
use App\Repositories\Backend\Master\GroupChartRepository;
use App\Services\Tree;
use Exception;

class CustomerController extends Controller
{
    private $customer;

    private $groupChartRepository;

    private $tree;

    public function __construct(CustomerRepository $customerRepository, GroupChartRepository $groupChartRepository, Tree $tree)
    {
        $this->customer = $customerRepository;
        $this->groupChartRepository = $groupChartRepository;
        $this->tree = $tree;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ledger_tree = $this->tree->getTreeViewSelectOptionLedgerTree(json_decode(json_encode($this->groupChartRepository->getGroupChartOfIndex(), true), true), 0);

        if (user_privileges_check('master', 'Customer', 'display_role')) {
            return view('admin.master.customer.index', compact('ledger_tree'));
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomer()
    {
        if (user_privileges_check('master', 'Customer', 'display_role')) {
            try {
                $data = $this->customer->getCustomerOfIndex();

                return RespondWithSuccess('All customer list not show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return $this->RespondWithError('All customer list not show successfully !!', $e->getMessage(), 404);
            }
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
    public function store(CustomerStoreRequest $request)
    {
        if (user_privileges_check('master', 'Customer', 'create_role')) {
            try {
                $data = $this->customer->storeCustomer($request);

                return RespondWithSuccess('customer create successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('customer not create successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Customer', 'alter_role')) {
            try {
                $data = $this->customer->getCustomerId($id);

                return RespondWithSuccess('customer show successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('customer not show successfully', $e->getMessage(), 400);
            }
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
    public function update(CustomerUpdateRequest $request, $id)
    {
        if (user_privileges_check('master', 'Customer', 'alter_role')) {
            try {
                $data = $this->customer->updateCustomer($request, $id);

                return RespondWithSuccess('customer update successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('customer not  update successfully', $e->getMessage(), 404);
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
        if (user_privileges_check('master', 'Customer', 'delete_role')) {
            try {
                $data = $this->customer->deleteCustomer($id);

                return RespondWithSuccess('customer delete successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('customer not  delete successfully', $e->getMessage(), 404);
            }
        } else {
            abort(403);
        }
    }
}
