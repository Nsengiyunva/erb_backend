<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\USFUser;


class AuthenticateController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 36000,
            'user' => auth()->user()
        ]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email_address' => 'required|email',
            'password' => 'required|string|min:4'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // if (! $token = auth()->attempt($validator->validated())) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        // return $this->createNewToken($token);
        // return response()->json([
        //     "success" => true,
        //     "message" => "User logged in"
        // ], 200 );
        $user = USFUser::where('email_address', $request->email_address)->first();
        return response()->json( [
            "success" => true,
            "user" => $user
        ], 200 );
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUser(Request $request) {
       $record = USFUser::where('email_address', $request->email_address)->first();
       if(!empty($record)){
            $record->position = $request->input('position');
            $record->role = $request->input('role');
            $record->department =  $request->input('department');
            $record->department_no = $request->input('department_no');
            $record->nssf_no = $request->input('nssf_no');
            $record->next_of_kin = $request->input('next_of_kin');
            $record->nok_phone = $request->input('nok_phone');

            $record->save();

            return response()->json([
                "success" => true,
                "data" => $record,
                "message" => "User Updated"
            ]);
           
       }
        $validator = Validator::make($request->all(), [
            'email_address' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        
        if($validator->fails()){
            return response()->json(
                $validator->errors()->toJson(), 
            400);
        }
        $user = USFUser::create(array_merge(
                    $validator->validated(),
                    [
                        'password' => bcrypt($request->password),
                        'email_address' => $request->email_address,
                        'role' => $request->role,
                        'passcode' => $request->password
                    ]
                ));
                return response()->json([
                    'message' => 'User was successfully added',
                    'user' => $user
                ], 201);

        
    }
    public function register(Request $request){
        $record = USFUser::where('email_address', $request->email_address)->first();
       if(!empty($record)){
           return response()->json([
               'message' => 'The Email Address already exists',
               'records' => $record
           ], 400);
       } 
       $user = new USFUser;
       $user->email_address = $request->email_address;
       $user->passcode = $request->passcode;
       $user->first_name = $request->first_name;
       $user->last_name = $request->last_name;
       $user->role = $request->role;
       $user->password = bcrypt($request->passcode);
       $user->username = $request->username;
       $user->institution_type = $request->institution_type;
       $user->institution_name = $request->institution_name;

       $user->save();
       
    //    if( $user->id ){
        return response()->json([
            'message' => 'User was successfully registered successfully.',
            'user' => $user
        ], 201);
    //    }
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
}