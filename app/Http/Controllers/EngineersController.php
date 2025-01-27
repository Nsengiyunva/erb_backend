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
    public function storeLicence(Request $request)
    {

        $request->validate( [
            "payment_mode" => "required",
            "payment_phone_no" => "required",
            "payment_source_system" => "required"
        ] );

        $elicence = new ELicence;
        $education = new ELicenceEducation;

        $elicence->type = $request->type;
        $elicence->category = $request->category;

        $elicence->firstname = $request->firstname;
        $elicence->surname = $request->surname;
        $elicence->other_names = $request->other_names;
        $elicence->address = $request->address;
        $elicence->dob = $request->birth_date;
        $elicence->nationality = $request->nationality;
        $elicence->pob = $request->pob;
        $elicence->nin = $request->nin;
        $elicence->telephone = $request->telephone;
        $elicence->applicant_id = $request->applicant_id;

        $elicence->status = $request->status;
        $elicence->progress = $request->progress;
        $elicence->tracking_no = $request->tracking_no;
        $elicence->stage = $request->stage;
        $elicence->applicant_id = $request->applicant_id;

        $elicence->name = $request->name;
        $elicence->email_address = $request->email_address;
        $elicence->birth_place = $request->birth_place;
        $elicence->application_type = $request->application_type;

        $elicence->user_picture = $request->user_picture;
        $elicence->document_type = $request->document_type;
        $elicence->document_id = $request->document_id;

        $elicence->created_at = now();
        $elicence->updated_at = now();

        $elicence->save();

        foreach ($request->education as $child) {
            $sql = DB::table('elicence_education')->insert(
                [
                    'parentID' => $elicence->id,
                    'start_date' => $child['start_date'],
                    'end_date' => $child['end_date'],
                    'qualification' => $child['qualification'],
                    'institution' => $child['institution'],
                    'file' => $child['attach_file'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }

        foreach ($request->sponsors as $child) {
            $sql = DB::table('elicence_sponsors')->insert(
                [
                    'parentID' => $elicence->id,
                    'sponsor_name' => $child['sponsor_name'],
                    'registered' => $child['registered'],
                    'registration_number' => $child['registration_number'],
                    'discipline' => $child['discipline'],
                    'progress' => $child['progress'],
                    'status' => $child['status'],
                    'email_address' => $child['email_address'],
                    'user_id' => $child['user_id'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }


        foreach ($request->membership as $child) {
            $sql = DB::table('elicence_membership')->insert(
                [
                    'parentID' => $elicence->id,
                    'membership_name' => $child['membership_name'],
                    'attach_file' => $child['attach_file'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }

        foreach ($request->engineering as $child) {
            $sql = DB::table('elicence_engineering')->insert(
                [
                    'parentID' => $elicence->id,
                    'start_date' => $child['start_date'],
                    'institution' => $child['institution'],
                    'file' => $child['attach_file'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }

        foreach ($request->positions as $child) {
            $sql = DB::table('elicence_positions')->insert(
                [
                    'parentID' => $elicence->id,
                    'start_date' => $child['start_date'],
                    'end_date' => $child['end_date'],
                    'organisation' => $child['organisation'],
                    'cadre' => $child['cadre'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }


        foreach ($request->practicals as $child) {
            $sql = DB::table('elicence_practicals')->insert(
                [
                    'parentID' => $elicence->id,
                    'start_date' => $child['start_date'],
                    'end_date' => $child['end_date'],
                    'organisation' => $child['organisation'],
                    'cadre' => $child['cadre'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }

        $payment = new Payment;
        $payment->mode = "MOBILE";
        $this->erbPay->setPayment($payment);
        $payment->phone_no = $request->input("payment_phone_no");

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
            "id" => $elicence->id,
            "message" => "Licence Application has been added successfully."
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
        // $approver->actor = $request->actor;
        $approver->actor_id = $request->actor_id;
        // $approver->actor_email = $request->actor_email;
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

        // $approver = new EApprover;

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
        $sql = "SELECT DISTINCT id, first_name, surname FROM elicence_user limit 5";
        $results = DB::select( $sql );

        return response()->json( [
            "success" => true,
            "users" => $results
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
}
