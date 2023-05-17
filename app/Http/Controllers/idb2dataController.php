<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class idb2dataController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $qry = DB::connection(env('DB2_CONNECTION'))->select('SELECT STSHRT, STADD1, STADD2 ,STADD3 , STCITY , STZIP , STPHON FROM MM770QAL.TBLSTR');


        foreach ($qry as $item) {
            // * $data is a Collection of ApiResources merged together per Page.
            $item->stshrt = rtrim($item->stshrt);
            $item->stadd1 = rtrim(mb_convert_encoding($item->stadd1, 'UTF-8', 'UTF-8'));
            $item->stadd2 = rtrim(mb_convert_encoding($item->stadd2, 'UTF-8', 'UTF-8'));
            $item->stadd3 = rtrim($item->stadd3);
            $item->stcity = rtrim($item->stcity);
            $item->stzip = rtrim($item->stzip);
            $item->stphon = rtrim($item->stphon);
        }

        //  $qry = DB::connection(env('DB2_CONNECTION'))->select('SELECT POSTAT,PONOT1,POVNUM, PONUMB, POEDAT FROM MM770QAL.POMHDR');

        return response()->json($qry);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
