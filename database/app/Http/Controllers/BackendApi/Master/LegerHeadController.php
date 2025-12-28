<?php

namespace App\Http\Controllers\BackendApi\Master;

use App\Http\Controllers\Controller;
use App\Repositories\Backend\Master\LegerHeadRepository;
use Exception;
use Illuminate\Http\Request;

class LegerHeadController extends Controller
{
    private $legerHeadRepository;

    public function __construct(LegerHeadRepository $legerHeadRepository)
    {
        $this->legerHeadRepository = $legerHeadRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->legerHeadRepository->getLegerHeadOfIndex();

            return RespondWithSuccess('All leger head list show successfull', $data, 201);
        } catch (Exception $e) {
            return RespondWithError('All leger head list not show successfull !!', $e->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
