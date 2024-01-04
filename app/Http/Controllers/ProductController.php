<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Products;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class ProductController extends Controller
{
    /**
     * ! Auth Middleware.
     * * Middleware for checking the Authentication of user.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['show', 'generics', 'generics']]);
        // $this->middleware('auth:api');
    }

    /**
     * ! DISPLAY ALL PRODUCTS CONTROLLER.
     * * Display a listing of the Product resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $headers = apache_request_headers();
        $beartoken = $headers['Authorization'];
        $actvtoken = auth()->user()->active_token;
        $admin = auth()->user()->permission;

        if ($beartoken == "Bearer $actvtoken") {

            if ($admin === "adminUser") {


                /*   ! For Development Purpose.
                *todo This controller will replace the Shopify Display all Product(index) in ShopifyContoller.
                *todo Must be Paginated by 10.
                *   ? Does the controller needs a limit in number or not
                *   ? is this The right Controller for this Fucntion
                */
                // * Search Query Search ALL Products in Products Table
                $products = Products::orderBy('level', 'DESC')->paginate(100);


                return response()->json($products, 200);
            } else {
                $res = [
                    "error" => "User Unauthorized"
                ];
                return response()->json($res, 400);
            }
        }
    }

    /**
     ! Pagination Scripts Function For Shopify
     * Paginate the Shopipy store resource result by (n) .
     * @param  $items, $perPage, $page, $options
     * @return \Illuminate\Http\Response
     */
    public function paginate($items, $perPage = 22, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(array_values($items->forPage($page, $perPage)
            ->toArray()), $items->count(), $perPage, $page, $options);
    }

    /**
     * ! SEARCH ENGINE CONTROLLER.
     * * Display the specified resource by Search Term.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // $headers = apache_request_headers();
        // $beartoken = $headers['Authorization'];
        // $actvtoken = auth()->user()->active_token;


        // if ($beartoken == "Bearer $actvtoken") {
        // * Search Query Search Mulltiple Table For Pattern
        $products = Products::where('generic_name', 'LIKE', "%$slug%")
            ->orWhere('keywords', 'LIKE', "%$slug%")
            ->orWhere('category', 'LIKE', "%$slug%")
            ->orWhere('grams', 'LIKE', "%$slug%")
            ->orWhere('product_name', 'LIKE', "%$slug%")
            ->orWhere('product_name', 'LIKE', "%$slug%")
            ->orderBy('level', 'DESC')->limit(3)
            ->get();


        // * Error Handler If Empty
        if ($products->isEmpty()) {

            $response =  [

                ['error' => 'Product Not Found']

            ];


            return response()->json($response, 400);
        }



        $obj = json_decode(json_encode($products), FALSE);


        return response($obj, 200);
    }


    /**
     * ! INSERT NEW PRODUCT CONTROLLER.
     * * Store a newly created Product in storage/database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $admin = auth()->user()->permission;
        if ($admin === "tadminuser") {
            // * Input Parameters = Asigned Variable
            $generic_name = $request->input('generic_name');
            $grams = $request->input('grams');
            $product_name = $request->input('product_name');
            $category = $request->input('category');
            $product_id = $request->input('product_id');
            $variant_id = $request->input('variant_id');
            $price = $request->input('price');
            $compare_at_price = $request->input('compare_at_price');
            $level = $request->input('level');
            $keyword = $request->input('keyword');

            // * Success Response
            $product = new Products([
                'generic_name' => $generic_name,
                'grams' => $grams,
                'product_name' => $product_name,
                'category' => $category,
                'product_id' => $product_id,
                'variant_id' => $variant_id,
                'price' => $price,
                'compare_at_price' => $compare_at_price,
                'level' => $level,
                'keyword' => $keyword
            ]);

            // * if OK save
            $product->save();


            return response()->json($product, 200);
        }

        $res = [
            "error" => "User Unauthorized"
        ];


        return response()->json($res, 400);
    }



    public function search($generic_name, $product)
    {
        // $headers = apache_request_headers();
        // $beartoken = $headers['Authorization'];
        // $actvtoken = auth()->user()->active_token;


        // if ($beartoken == "Bearer $actvtoken") {
        // * Search Query Search Mulltiple Table For Pattern
        $products = Products::where('generic_name', 'LIKE', "%$generic_name%")
            ->where('keywords', 'LIKE', "%$product%")->limit(10)
            ->orWhere('product_name', 'LIKE', "%$product%")
            ->get();


        // * Error Handler If Empty
        if ($products->isEmpty()) {

            $response =  [

                ['error' => 'Product Not Found']

            ];


            return response()->json($response, 400);
        }


        return response()->json($products, 200);
    }

    public function generics()
    {

        $generics = Products::select('generic_name')->distinct()->get();

        if ($generics->isEmpty()) {

            $response =  [

                ['error' => 'Product Not Found']

            ];


            return response()->json($response, 400);
        }



        foreach ($generics as $generic) {

            $dd[] =  $generic->generic_name;
        }

        return response()->json($dd, 200);
    }
}
