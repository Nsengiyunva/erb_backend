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
use Illuminate\Support\Facades\Http;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;

class EngineersController extends Controller
{

    protected $erbPay;

    public function __construct(ErbPay $erbPay)
    {
        $this->erbPay = $erbPay;
    }

    public function getAllUsers() {
        $sql = "SELECT DISTINCT id, first_name, surname, other_names, name, user_type, email, gender, country, registered, licence_no FROM elicence_user";
        $results = DB::select( $sql );

        return response()->json( [
            "success" => true,
            "users" => $results
        ] );
    }

    public function makePayment( $application_id, $applicant_id, $phone_number, $source = "MTN", $narrative = "Payment for application fees", $person = "ERB", $amount  ){
        $payment = new Payment;

        $this->erbPay->setPayment($payment);

        $payment->mode = "MOBILE";
        $payment->phone_no = $phone_number;

        
        $this->erbPay->pay( [
            "phone_no" => $phone_number,
            "source_system" => $source,
            "amount" => $amount,
            "narrative" => $narrative,
            "sent_from" => $person
        ] );
        
        $payment->elicense_id = $application_id;
        $payment->created_by = $applicant_id;
        $payment->save();

        return $payment->id;
    }

    public function sendmail(Request $request){
        $title = 'ERB Licensing';

        $user_details = [
            'name' => "Test Name",
            'content' => "Test Content",
            'email' => "isaacnsengiyunva@gmail.com"
        ];

        $sendmail = Mail::to($user_details['email'])->send(
            new SendMail($title,$user_details)
        );
        if(empty( $sendemail )){
            return response()->json([ 'message' => 'Mail has been sent successfully' ], 200 );
        }
        else {
            return response()->json([ 'message' => 'Mail Sent fail'], 400);
        }
    }

    public function getDraftsById( Request $request ) {
        $sql = "SELECT DISTINCT * FROM elicence WHERE id LIKE %".$request->id."%";
        $results = DB::select( $sql );
        return response()->json( [
            "success" => true,
            "results" => $results
        ] );
    }

    public function fetchDraftsByEmail ( $email ) {
        $sql = "SELECT DISTINCT * FROM elicence WHERE draft_type LIKE '%draft%' AND email_address = '".$email."'";
        $results = DB::select( $sql );

        return response()->json( [
            "success" => true,
            "results" => $results
        ] );
    }

    public function storeDraft( Request $request ){
        $elicence = new ELicence;

        $elicence->type = $request->type;
        $elicence->profession = $request->profession;
        $elicence->sponsor_score = $request->sponsor_score;
        $elicence->category = $request->category;
        $elicence->draft_type = $request->draft_type;

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
        $elicence->ever_convicted = $request->ever_convicted;
        $elicence->conviction_details = $request->conviction_details;

        $elicence->status = $request->status;
        $elicence->progress = $request->progress;
        $elicence->tracking_no = $request->tracking_no;
        $elicence->stage = $request->stage;

        $elicence->name = $request->name;
        $elicence->email_address = $request->email_address;
        $elicence->birth_place = $request->birth_place;
        $elicence->application_type = $request->application_type;
        $elicence->draft_type = $request->draft_type;

        $elicence->user_picture = $request->user_picture;
        $elicence->document_type = $request->document_type;
        $elicence->document_id = $request->document_id;
        $elicence->draft_type = "draft";

        $elicence->created_at = now();
        $elicence->updated_at = now();

        $elicence->save();

        if( !is_null( $request->education ) ) {
            foreach ($request->education as $child) {
                $sql = DB::table('elicence_education')->insert(
                    [
                        'parentID' => $elicence->id,
                        'start_date' => $child['start_date'],
                        'end_date' => $child['end_date'],
                        'qualification' => $child['qualification'],
                        'institution' => $child['institution'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }

        
        if( !is_null( $request->sponsors ) ) {
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
        }

        
        if( !is_null( $request->membership )  ) {
            foreach ($request->membership as $child) {
                $sql = DB::table('elicence_membership')->insert(
                    [
                        'parentID' => $elicence->id,
                        'membership_name' => $child['membership_name'],
                        'attach_file' => $child['attach_file'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }

        if( !is_null( $request->engineering ) ) {
            foreach ($request->engineering as $child) {
            $sql = DB::table('elicence_engineering')->insert(
                [
                    'parentID' => $elicence->id,
                    'start_date' => $child['start_date'],
                    'institution' => $child['institution'],
                    'file' => $child['attach_file'],
                    'summary' => $child['summary'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }
        }

        if( !is_null( $request->positions )  ) {
            foreach ($request->positions as $child) {
                $sql = DB::table('elicence_positions')->insert(
                    [
                        'parentID' => $elicence->id,
                        'start_date' => $child['start_date'],
                        'end_date' => $child['end_date'],
                        'organisation' => $child['organisation'],
                        'cadre' => $child['cadre'],
                        // 'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }


        if( !is_null( $request->practicals ) ) {
            foreach ($request->practicals as $child) {
                $sql = DB::table('elicence_practicals')->insert(
                    [
                        'parentID' => $elicence->id,
                        'start_date' => $child['start_date'],
                        'end_date' => $child['end_date'],
                        'organisation' => $child['organisation'],
                        'cadre' => $child['cadre'],
                        // 'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }

        return response()->json([
            "success" => true,
            "ID" => $elicence->id
        ]);
    }

    public function updateDraft( Request $request ) {
        $updated = DB::table('elicence')
            ->where('id', $request->applicationID )
            ->update([
                'type' => $request->type,
                'profession' => $request->profession,
                'sponsor_score' => $request->sponsor_score,
                'category' => $request->category,
                'draft_type' => $request->draft_type,
                'firstname' => $request->firstname,
                'surname' => $request->surname,
                'other_names' => $request->other_names,
                'address' => $request->address,
                'dob' => $request->birth_date,
                'nationality' => $request->nationality,
                'pob' => $request->birth_place,
                'nin' => $request->document_id,
                'telephone' => $request->telephone,
                'applicant_id' => $request->applicant_id,
                'ever_convicted' => $request->ever_convicted,
                'conviction_details' => $request->conviction_details,
                'status' => $request->status,
                'applicant_id' => $request->applicant_id,
                'progress' => $request->progress,
                'tracking_no' => $request->tracking_no,
                'stage' => $request->stage,
                'name' => $request->name,
                'application_type' => $request->application_type,
                'user_picture' => $request->user_picture,
                'document_type' => $request->document_type,
                'document_id' => $request->document_id,
                'updated_at' => now(),
            ]);


        //other tables
        if( !is_null( $request->education ) ) {
            DB::delete('DELETE FROM elicence_education WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->education as $child) {
            $sql = DB::table('elicence_education')->insert(
                [
                    'parentID' => $request->applicationID,
                    'start_date' => $child['start_date'],
                    'end_date' => $child['end_date'],
                    'qualification' => $child['qualification'],
                    'institution' => $child['institution'],
                    'summary' => $child['summary'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
            }
        }

        if( !is_null( $request->sponsors ) ) {
            DB::delete('DELETE FROM elicence_sponsors WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->sponsors as $child) {
            $sql = DB::table('elicence_sponsors')->insert(
                [
                    'parentID' => $request->applicationID,
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
        }

        if( !is_null( $request->membership ) ) {
            DB::delete('DELETE FROM elicence_membership WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->membership as $child) {
                $sql = DB::table('elicence_membership')->insert(
                    [
                        'parentID' => $request->applicationID,
                        'membership_name' => $child['membership_name'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }


        if( !is_null( $request->engineering ) ) {
            DB::delete('DELETE FROM elicence_engineering WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->engineering as $child) {
                $sql = DB::table('elicence_engineering')->insert(
                    [
                        'parentID' => $request->applicationID,
                        'start_date' => $child['start_date'],
                        'end_date' => $child['end_date'],
                        'institution' => $child['institution'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }

        if( !is_null( $request->positions ) ) {
            DB::delete('DELETE FROM elicence_positions WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->positions as $child) {
            $sql = DB::table('elicence_positions')->insert(
                [
                    'parentID' => $request->applicationID,
                    'start_date' => $child['start_date'],
                    'end_date' => $child['end_date'],
                    'organisation' => $child['organisation'],
                    'cadre' => $child['cadre'],
                    'summary' => $child['summary'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
        }
        }

        if( !is_null( $request->practicals ) ) {
            DB::delete('DELETE FROM elicence_practicals WHERE parentID = ?', [ $request->applicationID ]);
            
            foreach ($request->practicals as $child) {
                $sql = DB::table('elicence_practicals')->insert(
                    [
                        'parentID' => $request->applicationID,
                        'start_date' => $child['start_date'],
                        'end_date' => $child['end_date'],
                        'organisation' => $child['organisation'],
                        'cadre' => $child['cadre'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }
        
        if ($updated) {
            $licence = DB::table('elicence')->where('id', $request->applicationID )->first();

            return response()->json([
                'message' => 'License updated successfully',
                'licence' => $licence,
            ]);
        } 
        else {
            return response()->json([
                'message' => 'License not found or no changes made',
            ], 404);
        }
    }

    public function storeLicence(Request $request) {

        $updated = DB::table('elicence')
            ->where('id', $request->applicationID )
            ->update([
                'type' => $request->type,
                'profession' => $request->profession,
                'sponsor_score' => $request->sponsor_score,
                'category' => $request->category,
                'draft_type' => $request->draft_type,
                'firstname' => $request->firstname,
                'surname' => $request->surname,
                'other_names' => $request->other_names,
                'address' => $request->address,
                'dob' => $request->birth_date,
                'nationality' => $request->nationality,
                'pob' => $request->birth_place,
                'nin' => $request->document_id,
                'telephone' => $request->telephone,
                'applicant_id' => $request->applicant_id,
                'ever_convicted' => $request->ever_convicted,
                'conviction_details' => $request->conviction_details,
                'status' => $request->status,
                'applicant_id' => $request->applicant_id,
                'progress' => $request->progress,
                'tracking_no' => $request->tracking_no,
                'stage' => $request->stage,
                'name' => $request->name,
                'application_type' => $request->application_type,
                'user_picture' => $request->user_picture,
                'document_type' => $request->document_type,
                'document_id' => $request->document_id,
                'updated_at' => now(),
            ]);


        //other tables
        if( !is_null( $request->education ) ) {
            DB::delete('DELETE FROM elicence_education WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->education as $child) {
            $sql = DB::table('elicence_education')->insert(
                [
                    'parentID' => $request->applicationID,
                    'start_date' => $child['start_date'],
                    'end_date' => $child['end_date'],
                    'qualification' => $child['qualification'],
                    'institution' => $child['institution'],
                    'summary' => $child['summary'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
             );
            }
        }

        if( !is_null( $request->sponsors ) ) {
            DB::delete('DELETE FROM elicence_sponsors WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->sponsors as $child) {
            $sql = DB::table('elicence_sponsors')->insert(
                [
                    'parentID' => $request->applicationID,
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
        }

        if( !is_null( $request->membership ) ) {
            DB::delete('DELETE FROM elicence_membership WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->membership as $child) {
                $sql = DB::table('elicence_membership')->insert(
                    [
                        'parentID' => $request->applicationID,
                        'membership_name' => $child['membership_name'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }


        if( !is_null( $request->engineering ) ) {
            DB::delete('DELETE FROM elicence_engineering WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->engineering as $child) {
                $sql = DB::table('elicence_engineering')->insert(
                    [
                        'parentID' => $request->applicationID,
                        'start_date' => $child['start_date'],
                        'end_date' => $child['end_date'],
                        'institution' => $child['institution'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }

        if( !is_null( $request->positions ) ) {
            DB::delete('DELETE FROM elicence_positions WHERE parentID = ?', [ $request->applicationID ]);

            foreach ($request->positions as $child) {
            $sql = DB::table('elicence_positions')->insert(
                [
                    'parentID' => $request->applicationID,
                    'start_date' => $child['start_date'],
                    'end_date' => $child['end_date'],
                    'organisation' => $child['organisation'],
                    'cadre' => $child['cadre'],
                    'summary' => $child['summary'],
                    'created_at' => now(),
                    'updated_at' =>  now(),
                ]
            );
         }
        }

        if( !is_null( $request->practicals ) ) {
            DB::delete('DELETE FROM elicence_practicals WHERE parentID = ?', [ $request->applicationID ]);
            
            foreach ($request->practicals as $child) {
                $sql = DB::table('elicence_practicals')->insert(
                    [
                        'parentID' => $request->applicationID,
                        'start_date' => $child['start_date'],
                        'end_date' => $child['end_date'],
                        'organisation' => $child['organisation'],
                        'cadre' => $child['cadre'],
                        'summary' => $child['summary'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                    ]
                );
            }
        }

         $payment_id = $this->makePayment( $request->applicationID, $request->applicant_id, $request->payment_phone_no, $request->payment_source_system, $request->amount );
        
        if ($updated) {
            $licence = DB::table('elicence')->where('id', $request->applicationID )->first();

            return response()->json([
                'message' => 'License updated successfully',
                'licence' => $licence,
                "payment_id" => $payment_id
            ]);
        } 
        else {
            return response()->json([
                'message' => 'License not found or no changes made',
            ], 404);
        }
    }

    public function getRegisteredEngineers() {
        $sql = ELicenceUser::select('country as country_name', 'licence_no as license_number')->where( "registered", "Yes" )->get();
        if( empty( $sql ) ) {
            return response()->json(['message' => 'No results were found.'], 404);
        }
        return response()->json( $sql );
    }

    public function isEngineerAuthenticated( Request $request ) {
        $sql =  ELicenceUser::where( "email", $request->user_name )->where( "password", $request->password )->first();
        if( empty( $sql ) ) {
            return response()->json([
                'message' => 'Engineer was not found in the databaase and maybe not registered yet.'
            ], 404);
        }
        return response()->json( [
            "is_autheticated" => true,
            "result" => $sql
        ] );
    }

    public function fetchErbEngineers( $category ) {
        $sql = "SELECT DISTINCT * FROM erb_baseline WHERE type LIKE '%".$category."%' ";
        $results = DB::select( $sql );

        return response()->json( [
            "success" => true,
            "results" => $results
        ] );
    }

    public function updateLicencePayment (Request $request){
        $elicence = ELicence::where("id", $request->id)->first();

        $elicence->account_status = $request->account_status;
        
        $elicence->save();

        return response()->json([
            "success" => true,
            "message" => "Payment Status has been updated successfully."
        ]);
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
        $applications = ELicence::all();
        return response()->json( [
            "success" => true,
            "records" => $applications
        ] );
    }

    public function checkLicenses( $user_id ) {
        $applications = ELicence::where('applicant_id', $user_id )->get();
        return response()->json( [
            "success" => true,
            "records" => $applications
        ] );
    }

    public function fetchAccountLicenses( $user_id ) {
        $applications = ELicence::where('applicant_id', $user_id )->where('draft_type', 'COMPLETE')->get();
        
        return response()->json( [
            "success" => true,
            "records" => $applications
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
        $application = ELicence::where("id", $request->parentID)->first();

        if( $request->sponsor_score ) {
            $application->sponsor_score = $request->sponsor_score;
        }

        if ($request->status) {
            $sponsor->status = $request->status;
        }

        if ($request->progress) {
            $sponsor->progress = $request->progress;
        }

        if ($request->comment) {
            $sponsor->comment = $request->comment;
        }
        
        $sponsor->save();
        $application->save();

        return response()->json([
            "success" => true,
            "message" => "Sponsor has been updated successfully."
        ]);
    }


    public function fetchEngineers( Request $request ) {
        $sql = "SELECT DISTINCT id, name, email, telephone, licence_no, category, registered, gender, created_at FROM elicence_user";
        $results = DB::select( $sql );

        return response()->json( [
            "success" => true,
            "users" => $results
        ] );
    }

    public function storeUser(Request $request){
        // if( $request->registered = "No" ) {
        //     $sql = "SELECT DISTINCT * FROM elicence_user WHERE email LIKE '%".$request->email."%'";
        //     $record = DB::select( $sql );   

        //     if( !empty( $record )  ) {
        //         return response()->json( [
        //             "success" => true,
        //             "result" => 1,
        //             "message" => "Account has already been created with this Email Address ".$request->email.". Email Address already exists in the database."
        //         ] );
        //     }
        //     else {
        //         // $user = new ELicenceUser;

        //         // $user->type = $request->type;
        //         // $user->name = $request->name;
        //         // $user->first_name = $request->first_name;
        //         // $user->last_name = $request->last_name; //add
        //         // $user->surname = $request->surname;
        //         // $user->other_names = $request->other_names;
        //         // $user->telephone = $request->telephone;
        //         // $user->phone_no = $request->phone_no; //add
        //         // $user->origin_license_no = $request->origin_license_no; //add
        //         // $user->email = $request->email;
        //         // $user->birth_place = $request->birth_place;
        //         // $user->dob = $request->birth_date;
        //         // $user->gender = $request->gender;
        //         // $user->company_name = $request->company_name;
        //         // $user->address = $request->address;
        //         // $user->status = $request->status;
        //         // $user->user_type = $request->user_type;
        //         // $user->password = $request->password;
        //         // $user->country = $request->country;
        //         // $user->registered = $request->registered;
        //         // $user->category = $request->category;
        //         // $user->licence_no = $request->licence_no;
        //         // $user->user_picture = $request->user_picture;

        //         // $user->created_at = now();
        //         // $user->updated_at = now();
        //         // $user->save();

        //         // return response()->json([
        //         //     "success" => true,
        //         //     "result" => 0,
        //         //     "message" => "Account has been created successfully."
        //         // ]);

               
        //     // }
        // }
        // else {
        //     $sql = "SELECT DISTINCT * FROM elicence_user WHERE licence_no LIKE '%".$request->licence_no."%'";
        //     $record = DB::select( $sql );


        //     if( !empty( $record )  ) {
        //         return response()->json( [
        //             "success" => true,
        //             "result" => 1,
        //             "message" => "Account has already been created with this registration number ".$request->licence_no.". Registration Number already exists in the database."
        //         ] );
        //     }
        //     else {
        //         $user = new ELicenceUser;

        //         $user->type = $request->type;
        //         $user->name = $request->name;
        //         $user->first_name = $request->first_name;
        //         $user->surname = $request->surname;
        //         $user->other_names = $request->other_names;
        //         $user->telephone = $request->telephone;
        //         $user->email = $request->email;
        //         $user->birth_place = $request->birth_place;
        //         $user->dob = $request->birth_date;
        //         $user->gender = $request->gender;
        //         $user->company_name = $request->company_name;
        //         $user->address = $request->address;
        //         $user->status = $request->status;
        //         $user->user_type = $request->user_type;
        //         $user->password = $request->password;
        //         $user->country = $request->country;
        //         $user->registered = $request->registered;
        //         $user->category = $request->category;
        //         $user->licence_no = $request->licence_no;
        //         $user->user_picture = $request->user_picture;

        //         $user->created_at = now();
        //         $user->updated_at = now();
        //         $user->save();

        //         return response()->json([
        //             "success" => true,
        //             "result" => 0,
        //             "message" => "Account has been created successfully."
        //         ]);
        //     }
        // }    
        return response()->json( [
            "success" => "we are testing"
        ] );   
    }

    public function loginUser(Request $request)
    {
        $sql = "SELECT DISTINCT * FROM elicence_user WHERE email LIKE '%".$request->email."%' AND STATUS = 'APPROVED'";
        $record = DB::select($sql);
        
        return response()->json( [
            "success" => true,
            "record" => $record
        ] );
    }

    public function getLicenceRemarks( $licenceId )
    {
        $results =  ELicenceApprover::where( "licence_application_id", $licenceId )->get();
        return response()->json( [
            "success" => true,
            "record" => $results
        ] );
    }

    public function paymentSuccess( Request $request ) {
        return response()->json( [

        ] );
    }
}
