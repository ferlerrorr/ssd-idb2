<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Products;

class JdaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function genericName()
    {
        $sku_numbers = DB::table('products')
            ->pluck('sku_number')
            ->toArray();

        $result = [];

        $data = DB::connection(env('DB2_CONNECTION'))
            ->table('MM770SSL.INVMSA')
            ->whereIn('VNUMBR', $sku_numbers)
            ->get();

        foreach ($data as $item) {
            // Trim 'VCIDSC' field
            $item->vcidsc = trim($item->vcidsc);

            // Append the trimmed data to the result array
            $result[] = $item;

            // Upsert into the 'products' table only if sku_number matches
            if (in_array($item->vnumbr, $sku_numbers)) {
                DB::table('products')->updateOrInsert(
                    ['sku_number' => $item->vnumbr],
                    ['generic_name' => $item->vcidsc]
                );
            }
        }

        return response()->json($result);
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
