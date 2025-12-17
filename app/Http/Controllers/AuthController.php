<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Payments;


class AuthController extends Controller {

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
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login1(Request $request){
    	$validator = Validator::make($request->all(), [
            'email_address' => 'required|email',
            'password' => 'required|string|min:4'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUser(Request $request) {
       $record = User::where('email_address', $request->email_address)->first();
       if(!empty($record)){
            $record->position = $request->input('position');
            $record->role = $request->input('role');
            $record->organisation = $request->input('organisation');
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
        $user = User::create(array_merge(
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
        $record = User::where('email_address', $request->email_address)->first();
       if(!empty($record)){
           return response()->json([
               'message' => 'The Email Address already exists',
               'records' => $record
           ], 400);
       } 
       $user = new User;
       $user->email_address = $request->email_address;
       $user->role = $request->role;
       $user->gender = $request->gender;
       $user->passcode = $request->passcode;
       $user->department = $request->department;
       $user->department_no = $request->department_no;
       $user->position = $request->position;
       $user->next_of_kin = $request->next_of_kin;
       $user->nok_phone = $request->nok_phone;
       $user->nssf_no = $request->nssf_no;
       $user->organisation = $request->organisation;
       $user->password = bcrypt($request->passcode );

       $user->account_no = $request->account_no;
       $user->bank_name = $request->bank_name;
       $user->designation = $request->designation;
       $user->dob = $request->dob;
       $user->employment_terms = $request->employment_terms;
       $user->first_name = $request->first_name;
       $user->last_name = $request->last_name;
       $user->gender = $request->gender;
       $user->national_id = $request->national_id;
       $user->nationality = $request->nationality;
       $user->position = $request->position;
       $user->salary_scale = $request->salary_scale;
       $user->primary_contact = $request->primary_contact;
       $user->secondary_contact = $request->secondary_contact;
       $user->spouse_1_name = $request->spouse_1_name;
       $user->spouse_2_name = $request->spouse_2_name;
       $user->spouse_1_phone = $request->spouse_1_phone;
       $user->spouse_2_phone = $request->spouse_2_phone;
       $user->staff_id = $request->staff_id;
       $user->terms_of_employment = $request->terms_of_employment;
       $user->start_date = $request->start_date;
       $user->end_date = $request->end_date;
       $user->title = $request->title;
       $user->type = "NEW_STAFF";

       $user->save();
       
       if( $user->id ){
        return response()->json([
            'message' => 'User was successfully added',
            'user' => $user
        ], 201);
       }
    }

    public function login( Request $request ) {
        $record = User::where('email_address', $request->email_address )->first();
        return response()->json( $record );
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