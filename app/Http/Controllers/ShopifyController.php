<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Signifly\Shopify\Shopify;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpParser\Node\Stmt\Return_;

use function PHPSTORM_META\type;

class ShopifyController extends Controller
{
    /**
     * ! Auth Middleware.
     * * Middleware for checking the Authentication of user.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
    // ! Show all Shopify Store Products
     * Display a listing of the Shopipy store resource.
     * @param  \Signifly\Shopify\Shopify;  $shopify
     * @return \Illuminate\Http\Response
     */
    public function index(Shopify $shopify)
    {
        $admin = auth()->user()->permission;

        if ($admin === "tadminuser") {

            // * Query All Products Limit by 250 - which is the maximum product shopify can produce.
            $pages = $shopify->paginateProducts(['limit' => 250]); // * returns Cursor.
            $results = collect();

            // * $page is a Collection of ApiResources
            foreach ($pages as $page) {
                // * $data is a Collection of ApiResources merged together per Page.
                $data = $results->merge($page);
            }

            // * $result is a Collection Calling Paginate Script.
            $results = $this->paginate($data);

            //flush();
            return response()->json($results, 200);
        } else {
            $res = [
                "User" => "Unauthorized"
            ];


            return response()->json($res, 400);
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
     * ! Search Product From Shopify Shop.
     * Display the specified Product From Shopify.
     *
     * @param  int  $pid
     * @param  \Signifly\Shopify\Shopify;  $shopify
     * @return \Illuminate\Http\Response
     */
    public function show($pid, Shopify $shopify)
    {
        //  * Get Product in Shopify w/ Product_id = $pid.
        $product = $shopify->getProduct($pid);

        //  * Get Product Variant First key[0]. 
        $variant = $product->variants[0];

        $images = $product->images;

        $imgs = array_map(function ($o) {
            return $o["src"];
        }, $images);


        //  * Get Product Variant id. 
        $variant_id = $variant['id'];

        //  * Get Product Variant Quantity. 
        $inventory_quantity = $variant['inventory_quantity'];

        $price = $variant['price'];

        $comprice = $variant['compare_at_price'];

        $product_name = $product["title"];

        if ($comprice <= $price) {

            $cprice = 0;
        } else {

            $cprice = $comprice;
        };


        // Response Object. 
        $response = [
            'images' => $imgs,
            'product_name' => $product_name,
            'variant_id' => $variant_id,
            'inventory_quantity' => $inventory_quantity,
            'price' => $price,
            'compare_at_price' => $cprice
        ];

        //	flush();

        //  * Return Response Object -> Json. 
        return response()->json($response, 200);
    }
}
