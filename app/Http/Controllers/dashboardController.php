<?php

namespace App\Http\Controllers;

use App\Repositories\Backend\Master\GroupChartRepository;
use Exception;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    private $groupChart;

    public function __construct(GroupChartRepository $groupChart)
    {
        $this->groupChart = $groupChart;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    //  {
    //     $group_chart_id = $this->groupChart->getTreeSelectOption();

    //     $test=$this->groupChart->getTree();
    //     $group_chart = $this->groupChart->getGroupChartOfIndex();
    //     return view('admin.group_chart_index', compact('group_chart','test','group_chart_id'));
    // }
    // public function get(Request $request){
    //     if ($request->ajax()) {
    //     $thml= $this->groupChart->getTree();;

    //     return response()->json($thml);
    //     }

    // }
    //     public function create()
    //     {
    //         $group_chart = $this->groupChart->getTreeSelectOption();
    //         return view('admin.group_chart', compact('group_chart'));
    //     }

    //     public function store(Request $request){

    //         try{
    //             $data=$this->groupChart->StoreGroupChart($request);
    //             return  RespondWithSuccess('group chart create successfull !! ',$data,201);
    //         }catch(Exception $e){
    //             return  RespondWithError('group chart not create successfull',$e->getMessage(),400);
    //         }
    //     }

    //     public function edit($id){

    //         try{
    //             $data=$this->groupChart->getGroupChartId($id);
    //             return  RespondWithSuccess('group chart show successfull !! ',$data,201);
    //         }catch(Exception $e){
    //             return  RespondWithError('group chart not show successfull',$e->getMessage(),400);
    //         }
    //     }
    //     public function update(Request $request,$id){

    //         try{
    //             $data=$this->groupChart->updateGroupChart($request ,$id);
    //             return  RespondWithSuccess('group chart update successfull !! ',$data,201);
    //         }catch(Exception $e){
    //             return  RespondWithError('group chart not  update successfull',$e->getMessage(),400);
    //         }

    //     }
    //     public function delete($id){
    //         try{
    //             $data=$this->groupChart->deleteGroupChart($id);
    //             return  RespondWithSuccess('group chart delete successfull !! ',$data,201);
    //         }catch(Exception $e){
    //             return  RespondWithError('group chart not  delete successfull',$e->getMessage(),400);
    //         }
    //     }

    //     public function planView(){
    //         try{
    //             $data=$this->groupChart->getGroupChartOfIndex();
    //             return  RespondWithSuccess('All Group Chart list not show successfull !! ',$data,201);
    //         }catch(Exception $e){
    //             return $this->RespondWithError('All Group Chart list not show successfull !!',$e->getMessage(), 400);
    //         }
    //     }
}
