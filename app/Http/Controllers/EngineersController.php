<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ELicence;
use App\Models\ELicenceUser;
use App\Models\EApprover;
use App\Models\ELicenceEducation;
use App\Models\ELicenceSponsor;
use App\Models\ELicenceMembership;
use App\Models\ELicenceEngineer;
use App\Models\ELicencePosition;
use App\Models\ELicencePracticals;
use App\Models\Payment;
use App\Services\ErbPay;
use Illuminate\Support\Facades\DB;

class EngineersController extends Controller
{

    protected $erbPay;
    public function __construct(ErbPay $erbPay)
    {
        $this->erbPay = $erbPay;
    }
    public function makePayment(Request $request)
    {

        $request->validate( [
            "payment_mode" => "required",
            "payment_phone_no" => "required",
            "payment_source_system" => "required"
        ] );
        
        //payment
        $payment = new Payment;
        $payment->mode = "MOBILE";
        $payment->phone_no = $request->input("payment_phone_no");
        $this->erbPay->setPayment($payment);

        //0701234110
        #$this->erbPay->pay( [
        #    "phone_no" => $request->input("payment_phone_no" ),
        #    "source_system" => "FLEXIPAY",
        #    "amount" => $request->input( "payment_amount" ),
        #    "narrative" => "Application fees from the client",
        #    "sent_from" => "Underhill Kawuma"
        #] );
        
        #$payment->elicense_id = $elicence->id;
        #$payment->created_by = $request->applicant_id;
        #$payment->save();

        return response()->json([
            "success" => true,
            "id" => 145345,
            "message" => "Licence Application has been created successfully."
        ]);
    }

    public function fetchRegister( Request $request ) {
        
    }

    public function fetchErbEngineers( $category ) {
        $sql = "SELECT DISTINCT * FROM erb_baseline WHERE type LIKE '%".$category."%' ";
        $results = DB::select( $sql );

        return response()->json( [
            "success" => true,
            "results" => $results
        ] );
    }

    public function updateLicence(Request $request){
        $elicence = ELicence::where("id", $request->id)->first();

        $approver = new EApprover;

        if ($request->status) {
            $elicence->status = $request->status;
        }
        if ($request->progress) {
            $elicence->progress = $request->progress;
        }
        if ($request->tracking_no) {
            $elicence->tracking_no = $request->tracking_no;
        }
        if ($request->stage) {
            $elicence->stage = $request->stage;
        }
        //approver
        $approver->licence_application_id = $request->id;
        $approver->actor_id = $request->actor_id;
        $approver->actor_role = $request->actor_role;
        $approver->comments = $request->comments;
        $approver->action = $request->action;
        $approver->stage = $request->stage;

        $approver->save();
        $elicence->save();

        return response()->json([
            "success" => true,
            "message" => "Remark has been added successfully."
        ]);
    }

    public function getLicences()
    {
        $records = ELicence::all();
        return response()->json( [
            "success" => true,
            "records" => $records
        ] );
    }

    public function getLicenceById($id)
    {
        $main = ELicence::where("id", $id)->first();
        $education = ELicenceEducation::where("parentID", $id)->get();
        $membership = ELicenceMembership::where("parentID", $id)->get();
        $sponsors = ELicenceSponsor::where("parentID", $id)->get();
        $engineering = ELicenceEngineer::where("parentID", $id)->get();
        $practicals = ELicencePracticals::where("parentID", $id)->get();
        $positions = ELicencePosition::where("parentID", $id)->get();
        $remarks = EApprover::where("licence_application_id", $id)->get();

        return response()->json([
            "success" => true,
            "records" => $main,
            "education" => $education,
            "remarks" => $remarks,
            "membership" => $membership,
            "engineering" => $engineering,
            "practicals" => $practicals,
            "positions" => $positions,
            "sponsors" => $sponsors
        ]);
    }

    public function getSponsorsByUser(){
        $sponsors = ELicenceSponsor::all();
        return response()->json([
            "success" => true,
            "sponsors" => $sponsors
        ]);
    }

    public function updateSponsor(Request $request){
        $sponsor = ELicenceSponsor::where("id", $request->id)->first();

        if ($request->status) {
            $sponsor->status = $request->status;
        }
        
        $sponsor->save();

        return response()->json([
            "success" => true,
            "message" => "Sponsor has been updated successfully."
        ]);
    }


    public function fetchEngineers( Request $request ) {
        $users = ELicenceUser::all();
        return response()->json( [
            "success" => true,
            "users" => $users
        ] );
    }

    public function storeUser(Request $request)
    {
        //check if the registered is present 
            if( $request->registered = "No" ) {
                $sql = "SELECT DISTINCT * FROM elicence_user WHERE email LIKE '%".$request->email."%'";
                $record = DB::select( $sql );   

                if( !empty( $record )  ) {
                    return response()->json( [
                        "success" => true,
                        "result" => 1,
                        "message" => "Account has already been created with this Email Address ".$request->email.". Email Address already exists in the database."
                    ] );
                }
                else {
                    $user = new ELicenceUser;
    
                    $user->type = $request->type;
                    $user->name = $request->name;
                    $user->first_name = $request->first_name;
                    $user->surname = $request->surname;
                    $user->other_names = $request->other_names;
                    $user->telephone = $request->telephone;
                    $user->email = $request->email;
                    $user->birth_place = $request->birth_place;
                    $user->dob = $request->birth_date;
                    $user->gender = $request->gender;
                    $user->company_name = $request->company_name;
                    $user->address = $request->address;
                    $user->status = $request->status;
                    $user->user_type = $request->user_type;
                    $user->password = $request->password;
                    $user->country = $request->country;
                    $user->registered = $request->registered;
                    $user->category = $request->category;
                    $user->licence_no = $request->licence_no;
                    $user->address = $request->address;
                    $user->user_picture = $request->user_picture;
    
                    $user->created_at = now();
                    $user->updated_at = now();
                    $user->save();
    
                    return response()->json([
                        "success" => true,
                        "result" => 0,
                        "message" => "Account has been created successfully."
                    ]);
                }


            }
            else {
                $sql = "SELECT DISTINCT * FROM elicence_user WHERE licence_no LIKE '%".$request->licence_no."%'";
                $record = DB::select( $sql );


                if( !empty( $record )  ) {
                    return response()->json( [
                        "success" => true,
                        "result" => 1,
                        "message" => "Account has already been created with this registration number ".$request->licence_no.". Registration Number already exists in the database."
                    ] );
                }
                else {
                    $user = new ELicenceUser;
    
                    $user->type = $request->type;
                    $user->name = $request->name;
                    $user->first_name = $request->first_name;
                    $user->surname = $request->surname;
                    $user->other_names = $request->other_names;
                    $user->telephone = $request->telephone;
                    $user->email = $request->email;
                    $user->birth_place = $request->birth_place;
                    $user->dob = $request->birth_date;
                    $user->gender = $request->gender;
                    $user->company_name = $request->company_name;
                    $user->address = $request->address;
                    $user->status = $request->status;
                    $user->user_type = $request->user_type;
                    $user->password = $request->password;
                    $user->country = $request->country;
                    $user->registered = $request->registered;
                    $user->category = $request->category;
                    $user->licence_no = $request->licence_no;
                    $user->address = $request->address;
                    $user->user_picture = $request->user_picture;
    
                    $user->created_at = now();
                    $user->updated_at = now();
                    $user->save();
    
                    return response()->json([
                        "success" => true,
                        "result" => 0,
                        "message" => "Account has been created successfully."
                    ]);
                }


            }
        
            
            
    }

    public function loginUser(Request $request)
    {
        $sql = "select distinct * from elicence_user where email like '%" . $request->email . "%' and status = 'APPROVED'";
        $record = DB::select($sql);
        
        return response()->json( [
            "success" => true,
            "record" => $record
        ] );
    }

    public function getLicenceRemarks($licenceId)
    {
        $results =  ELicenceApprover::where( "licence_application_id", $licenceId)->get();
        return response()->json([
            "success" => true,
            "record" => $results
        ] );
    }

    public function paymentSuccess( Request $request ) {
        return response()->json( [

        ] );
    }

    public function fetchRegistered() {
        $sql = "SELECT DISTINCT * FROM elicence_user";
        //WHERE type LIKE '%".$category."%' 
        $users = DB::select( $sql );

        return response()->json( [
            "success" => true,
            "results" => $users
        ] );
    }
}
