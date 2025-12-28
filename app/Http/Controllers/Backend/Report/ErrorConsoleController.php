<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Services\ErrorConsole\ErrorConsole;
use Exception;
use Illuminate\Http\Request;

class ErrorConsoleController extends Controller
{
    private $errorConsole;
    

    public function __construct(ErrorConsole $error_console)
    {
        $this->errorConsole = $error_console;
      
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ErrorConsoleShow()
    {
        if (user_privileges_check('report', 'ErrorConsole', 'display_role')) {
           
            return view('admin.report.general.error_console');
        } else {
            abort(403);
        }

    }

    /**
     * Display a listing of the all data show components.
     *
     * @return \Illuminate\Http\Response
     */
    public function ErrorConsoleGetData(Request $request)
    {
        if (user_privileges_check('report', 'ErrorConsole', 'display_role')) {
            try {
                $data = $this->errorConsole->errorConsoleGetData($request);

                return RespondWithSuccess('ErrorConsole show successfully !! ', $data, 200);
            } catch (Exception $e) {
                return RespondWithError('ErrorConsole not show successfully', $e->getMessage(), 400);
            }
        } else {
            abort(403);
        }
    }

    
}
