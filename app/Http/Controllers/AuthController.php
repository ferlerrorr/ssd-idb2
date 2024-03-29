<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Providers\Auth\Illuminate;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{

    /**
     * ! Auth Middleware.
     * * Middleware for checking the Authentication of user.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['token', 'register', 'verify', 'passwordChangeRequest', 'verifyPasswordChange', 'passwordChange']]);
    }

    /**
     * ! Register Controller.
     * * Register a user.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::Make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6'

        ]);

        if ($validator->fails()) {

            $invalid_fields = array_values($validator->errors()->toArray());

            return response($invalid_fields, 400);
        }


        //$user unused 
        //add the token for input and monitoring and to unsigned
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $email = $request->email;


        Mail::send('emails.welcome', ['email' => $email], function ($m) use ($email) {
            $m->from('ssd.shopify.api@gmail.com', 'SouthStar API');
            $m->to($email, 'admin')->subject('SSD API Email Verification');
        });




        return response()->json([
            ['User Successfully Created'], ['Please Check Your Email For Verifiation']
        ], 200);
    }

    /**
     * ! Login Controller.
     * * Log a user.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function token(Request $request)
    {


        $validator = Validator::Make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);


        if ($validator->fails()) {
            // return response()->json($validator->errors()->toJson(),422);
            $invalid_fields = array_values($validator->errors()->toArray());
            return response($invalid_fields, 400);
            //will return 200 ok if flushed
        }

        if (!$token = auth()->attempt($validator->validated())) {
            $erro =  [["User Unauthorized"]];
            return response()->json($erro, 400);
        }

        $em = $request->email;


        $email = User::where('email', $em)->first();

        if ($email['email_verified_at'] == null) {


            $erro =  [["Please check your email for verification"]];
            return response()->json($erro, 400);
        } else {


            $auth_id = auth()->user()->id;
            $user = User::where('id', $auth_id);


            $data = $this->createNewToken($token);

            $tok = $data['access_token'];

            $user->update([
                'active_token' => $tok
            ]);



            if ($token = auth() == true) {
                auth()->logout();
            }

            $validator = Validator::Make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);


            if ($validator->fails()) {
                // return response()->json($validator->errors()->toJson(),422);
                $invalid_fields = array_values($validator->errors()->toArray());
                return response($invalid_fields, 400);
            }



            if (!$token = auth()->attempt($validator->validated())) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }


            $auth_id = auth()->user()->id;
            $user = User::where('id', $auth_id);


            $data = $this->createNewToken($token);

            $tok = $data['access_token'];

            $user->update([
                'active_token' => $tok
            ]);


            return response()->json($data, 200);
        }
    }

    /**
     * ! Create New Token Function.
     * * Asign a JWT Token and display the specific user details.
     * @param  $token
     * @return \Illuminate\Http\Response
     */
    public function createNewToken($token)
    {
        // auth()->user();
        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'bearer',
        //     'name' => auth()->user()->name,
        //     'email' => auth()->user()->email
        // ]);


        return (["access_token" => $token]);
    }
    /**
     * ! Profile Function.
     * * Display the specific user details.
     * @param  $token
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {

        $headers = apache_request_headers();
        $beartoken = $headers['Authorization'];
        $actvtoken = auth()->user()->active_token;

        if ($beartoken == "Bearer $actvtoken") {

            return response()->json(auth()->user());
        } else {
            return response()->json([
                'message' => "User is not found , Unauthorized"
            ], 401);
        }
    }


    /**
     * ! Logout Function.
     * * Remove a specific Token Details.
     * @param  $token
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $auth_id = auth()->user()->id;
        $user = User::where('id', $auth_id);
        $user->update([
            'active_token' => null
        ]);
        auth()->logout();
        return response()->json([
            'message' => 'User Logout'
        ], 200);
    }


    /**
     * ! Email Verified Function.
     * * Display the specific user details.
     * @param  $token
     * @return \Illuminate\Http\Response
     */
    public function verify($email)
    {


        $em = $email;

        $time = Carbon::now();
        $user = User::where('email', $em);

        $user->update([
            'email_verified_at' => $time
        ]);

        return redirect('/token');
    }

    /**
     * ! Password Reset Function.
     * * Display the specific user details.
     * @return \Illuminate\Http\Response
     */
    public function passwordChangeRequest($email)
    {


        $email = $email;
        Mail::send('emails.resetpasswordrequest', ['email' => $email], function ($m) use ($email) {
            $m->from('ssd.shopify.api@gmail.com', 'SouthStar API');
            $m->to($email, 'admin')->subject('SSD API Password Change Request');
        });

        $email = $email;
        $user = User::where('email', $email);


        $user->update([
            'email_verified_at' => null
        ]);


        return response()->json([
            ['Password change request created'], ['Please Check Your Email For Verifiation']
        ], 200);
    }

    public function verifyPasswordChange($email)
    {

        $em = Str($email);
        $time = Carbon::now();
        $user = User::where('email', $em);

        $user->update([
            'email_verified_at' => $time
        ]);

        // Session::put('ssd_api_email', $email);
        $minutes = 60;
        // //need emaile verified to reset passswod with password confirmation in the view
        $redir = "/reset-password";
        return redirect($redir)->withCookie(cookie('ssd_api_email', $em, $minutes, null, null, false, false));
    }



    public function passwordChange(Request $request, $email)
    {


        //     # Validation
        $validator = Validator::Make($request->all(), [
            'password' => 'required|string|confirmed|min:6'
        ]);

        if ($validator->fails()) {

            $invalid_fields = array_values($validator->errors()->toArray());

            return response($invalid_fields, 400);
        }


        $password = $request->password;

        #Update the new Password
        $user =  User::where('email', $email)->update([
            'password' => Hash::make($password),
            'active_token' => null

        ]);

        $user =  User::where('email', $email)->get();


        return response()->json([
            ['Password changed successfully.'],
            ['Please re - generate your APi key for continuous usage of the service.'],
        ], 200);
    }
}
