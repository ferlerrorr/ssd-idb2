<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Signifly\Shopify\Shopify;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\type;

class ShopifyController extends Controller
{
    /**
     * ! Auth Middleware.
     * * Middleware for checking the Authentication of user.
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }
    /**
    // ! Show all Shopify Store Products
     * Display a listing of the Shopipy store resource.
     * @param  \Signifly\Shopify\Shopify;  $shopify
     * @return \Illuminate\Http\Response
     */
    public function index(Shopify $shopify)
    {

        $headers = apache_request_headers();
        if ($headers['Authorization'] == null) {
            $res = [
                "User" => "Unauthorized"
            ];


            return response()->json($res);
        } else {
            $beartoken = $headers['Authorization'];
        }

        $actvtoken = auth()->user()->active_token;
        $admin = auth()->user()->permission;

        if ($beartoken == "Bearer $actvtoken") {

            if ($admin === "adminUser") {

                $pages = $shopify->paginateProducts(['limit' => 200]); // * returns Cursor.
                $results = collect();
                // * $page is a Collection of ApiResources
                foreach ($pages as $page) {
                    // * $data is a Collection of ApiResources merged together per Page.
                    $data = $results->merge($page);
                }

                $keysToUnset = [
                    'body_html',
                    'images',
                    'options',
                    'template_suffix',
                    'vendor',
                    'tags',
                    'admin_graphql_api_id',
                    'image',
                    'published_scope',
                    'handle',
                    'created_at',
                    'updated_at',
                    'published_at',
                    'status',
                    'product_type',
                ];

                $dd = $data->map(function ($item) use ($keysToUnset) {
                    foreach ($keysToUnset as $key) {
                        if (isset($item[$key])) {
                            unset($item[$key]);
                        }
                    }
                    return $item;
                });;
            }
        }
        foreach ($dd as $item) {
            $variantId = $item['variants'][0]['id'];

            $productData = [
                'product_id' => $item['id'],
                'variant_id' => $variantId,
                'sku_number' => $item['variants'][0]['sku'],
            ];

            $existingRecord = Products::where('variant_id', $variantId)->first();

            if ($existingRecord) {
                $productData['updated_at'] = now();
                $existingRecord->update($productData);
            } else {
                $productData['created_at'] = now();
                $productData['updated_at'] = now();
                Products::insert($productData);
            }
        }



        $count = count($dd);
        return response()->json(
            $count,
            200
        );
    }

    /**
     ! Pagination Scripts Function For Shopify
     * Paginate the Shopipy store resource result by (n) .
     * @param  $items, $perPage, $page, $options
     * @return \Illuminate\Http\Response
     */
    public function paginate($items, $perPage = 50, $page = null, $options = [])
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

        // $headers = apache_request_headers();
        // $beartoken = $headers['Authorization'];
        // $actvtoken = auth()->user()->active_token;

        // if ($beartoken == "Bearer $actvtoken") {



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
        // } else {
        //     return response()->json([
        //         'message' => "User is not found , Unauthorized"
        //     ], 401);
        // }
    }
}
