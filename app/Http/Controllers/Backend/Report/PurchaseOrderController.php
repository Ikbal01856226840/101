<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Report\PurchaseOrderRepository;
use Exception;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    private $purchaseOrderRepository;

    public function __construct(PurchaseOrderRepository $purchaseOrderRepository)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function PurchaseOrderShow()
    {
        if (user_privileges_check('report', 'PurchaseOrder', 'display_role')) {
            return view('admin.report.general.purchase_order');
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function PurchaseOrder(Request $request)
    {
        if (user_privileges_check('report', 'PurchaseOrder', 'display_role')) {
            try {
                $data = $this->purchaseOrderRepository->getPurchaseOrderOfIndex($request);

                return RespondWithSuccess('profit loss successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('profit loss not successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function PurchaseOrderRegisterShow(Request $request)
    {
        $form_date = $request->form_date;
        
        $to_date = $request->to_date;
        if (user_privileges_check('report', 'PurchaseOrder', 'display_role')) {
            return view('admin.report.general.purchase_order_register',compact('form_date','to_date'));
        } else {
            abort(403);
        }
    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function PurchaseOrderRegister(Request $request)
    {
        if (user_privileges_check('report', 'PurchaseOrder', 'display_role')) {
            try {
                $data = $this->purchaseOrderRepository->getPurchaseOrderRegisterOfIndex($request);

                return RespondWithSuccess('purchaese order register successfully !! ', $data, 201);
            } catch (Exception $e) {
                return RespondWithError('purchaese order not register successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }
}
