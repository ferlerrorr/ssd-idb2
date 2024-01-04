<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Signifly\Shopify\Shopify;
use Illuminate\Support\Facades\Validator;
use App\Models\Orders;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class OrderController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = auth()->user()->permission;
        if ($admin === "tadminuser") {
            $orders = Orders::all();
            $od = json_decode($orders);
            $response = $this->paginate($od);

            // flush();
            return response()->json($response, 200);
        } else {
            $res = [
                "Msg" => "User Unauthorized"
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
    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(array_values($items->forPage($page, $perPage)
            ->toArray()), $items->count(), $perPage, $page, $options);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request, Shopify $shopify)
    {
        //!to order must be an active user of the api

        // * Verify if request is authenticated.
        // * Verify if request is JSON.
        if (!$request->isJson()) {


            return response(['message' => 'Only JSON requests are allowed'], 406);
        }



        $headers = apache_request_headers();
        $beartoken = $headers['Authorization'];
        $actvtoken = auth()->user()->active_token;

        if ($beartoken == "Bearer $actvtoken") {

            // * Request Validation.
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|array|min:2',
                'customer_name.*'  => 'nullable|string',
                'gender' => 'nullable|string|min:1|max:50',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'email' => 'required|email:rfc,dns',
                'contact_number' => 'required|numeric|digits:11',
                'address' => 'required|string|max:255',
                'draft_order' => 'required|array',
                'draft_order.line_items'  => 'required|array',
                'draft_order.line_items.*'  => 'required|array',
                'draft_order.line_items.*.variant_id' => 'numeric',
                'draft_order.line_items.*.quantity' => 'numeric',
                'doctor_details' => 'array|min:2',
                'doctor_details.*'  => 'nullable|string',
                'provider' => 'required|array|min:2',
                'provider.*'  => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            //  * IF OK
            //  * Shopify Api Request.


            // ! Data Filter for Shopify Order Request - Draft Character
            $order = $request->except('customer_name', 'gender', 'date_of_birth', 'email', 'contact_number', 'address', 'doctor_details', 'provider');
            // // // ! Shopify Order Request
            // $data =  $shopify->post('draft_orders.json', $order);
            // // $response = ($request.' '.$data);
            // // $invoice_url = $data['draft_order']['invoice_url'];
            // $draft_order_id =  $data['draft_order']['id'];


            //! Data Filter for Shopify Order Request  - Draft Character


            //!URL String Builder
            $items = $order['draft_order']['line_items'];
            $var_id = array_column($items, 'variant_id');
            $quantity = array_column($items, 'quantity');
            $note = implode('-', $request->provider);
            $providername = $request->provider["provider_name"];
            $providercarddetails = $request->provider["card_details"];

            $note = "$providername=$providercarddetails";

            $col = collect($var_id);
            $zip = $col->zip($quantity);


            $un = str_replace(',', ':', $zip);
            $res_str = array_chunk(explode(":", $un), 2);
            foreach ($res_str as &$val) {
                $val  = implode(":", $val);
            }

            //!URL String Builder
            $str = implode(",", $res_str);
            $strserial1 = str_replace(']', '', $str);
            $strserial2 = str_replace('[', '', $strserial1);

            $url = "https://ssd-api.myshopify.com/cart/" . $strserial2 . "?note=" . $note;



            // ! save to mysql-------------->
            $cust_fn = $request->customer_name['firstname'];
            $cust_mn = $request->customer_name['middlename'];
            $cust_sn = $request->customer_name['surname'];
            $cust_af = $request->customer_name['affix'];
            $gnd     = $request->gender;
            $dob     = $request->date_of_birth;
            $em      = $request->email;
            $cn      = $request->contact_number;
            $add     = $request->address;
            $do      = $request->draft_order['line_items'];
            $dod_fn  = $request->doctor_details['firstname'];
            $dod_mn  = $request->doctor_details['middlename'];
            $dod_sn  = $request->doctor_details['surname'];
            $dod_af  = $request->doctor_details['affix'];
            $dod_prc = $request->doctor_details['prc_number'];
            $prob_na = $request->provider['provider_name'];
            $prob_cd = $request->provider['card_details'];


            $od = [
                'customer_name' => [
                    'firstname' => $cust_fn,
                    'middlename' => $cust_mn,
                    'surname' => $cust_sn,
                    'affix' => $cust_af,
                ],
                'gender' => $gnd,
                'date_of_birth' => $dob,
                'email' => $em,
                'contact_number' => $cn,
                'address' => $add,
                'draft_order' => [
                    'line_items' => $do
                ],
                'doctor_details' => [
                    'firstname' => $dod_fn,
                    'middlename' =>  $dod_mn,
                    'surname' => $dod_sn,
                    'affix' =>  $dod_af,
                    'prc_number' => $dod_prc
                ],
                'provider' => [
                    'provider_name' => $prob_na,
                    'card_details' => $prob_cd
                ]
            ];
            // ! save to mysql-------------->

            $response = [
                'url' => $url

            ];

            $orders = new Orders(
                [
                    'order' => $od
                ]
            );

            $orders->save();

            return response()->json($response, 200);
        } else {

            $mg = [
                "Msg" => "Auth Token Not Valid"
            ];

            return response()->json($mg, 400);
        }
    }
}
