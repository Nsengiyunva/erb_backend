<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requistion;
use App\Models\DocumentFile;
use App\Models\ProcurementItems;
use App\Models\Remark;
use App\User;
use App\Models\Children;
use App\Models\Spouses;

use App\Models\Tracker;
use App\Models\Flow;
use App\Models\NEMAUser;
use App\Models\NEMAEIA;
use App\Models\NEMATOR;
use App\Models\NEMAPrac;
use App\Models\Delegate;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Contact;
use App\Models\Company;
use App\Models\MOFAUser;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class RequestController extends Controller
{
    public function upload_csv( Request $request ) {
        $file = $request->file('file');
        $fileContents = file($file->getPathname());
        
        foreach ($fileContents as $line) {
            $data = str_getcsv($line);
    
            Delegate::create( [
                'first_name' => trim( $data[0], "\xEF\xBB\xBF"),
                'middle_name' => trim( $data[1], "\xEF\xBB\xBF"),
                'last_name' => trim( $data[2], "\xEF\xBB\xBF"),
                'country' => trim( $data[3], "\xEF\xBB\xBF"),
                'accredited' => trim( $data[4], "\xEF\xBB\xBF"),
                'passport_number' => trim( $data[5], "\xEF\xBB\xBF"),
                'email' => trim( $data[6], "\xEF\xBB\xBF"),
                'gender' => trim( $data[7], "\xEF\xBB\xBF"),
                'hotel' => trim( $data[8], "\xEF\xBB\xBF"),
                'flight' => trim( $data[9], "\xEF\xBB\xBF"),
                'airline' => trim( $data[10], "\xEF\xBB\xBF"),
                'arrival_time' => trim( $data[11], "\xEF\xBB\xBF"),
                'departure_time' => trim( $data[12], "\xEF\xBB\xBF"),
                'summit' => trim( $data[13], "\xEF\xBB\xBF"),
                'telephone' => trim( $data[14], "\xEF\xBB\xBF"),
                'created_by' => $request->created_by
            ] );
        }

        return response()->json( [
            "success" => "data uploaded successfully."
        ] );
    }

    public static function convert_from_latin1_to_utf8_recursively($dat)
   {
      if (is_string($dat)) {
         return mb_convert_encoding($dat, 'ISO-8859-1', 'UTF-8');
      } elseif (is_array($dat)) {
         $ret = [];
         foreach ($dat as $i => $d) $ret[ $i ] = self::convert_from_latin1_to_utf8_recursively($d);
         return $ret;
      } elseif (is_object($dat)) {
         foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);
         return $dat;
      } else {
         return $dat;
      }
   }

    public function upload_cars( Request $request ) {
        $file = $request->file('file');
        $fileContents = file($file->getPathname());
        
        foreach ($fileContents as $line) {
            $data = str_getcsv( $line );
    
            Vehicle::create( [
                'plate_number' =>  trim( $data[0], "\xEF\xBB\xBF"),
                'brand' => trim( $data[1], "\xEF\xBB\xBF"),
                'type' => trim( $data[2], "\xEF\xBB\xBF"),
                'mileage' => trim( $data[3], "\xEF\xBB\xBF"),
                'company' => trim( $data[4], "\xEF\xBB\xBF"),
                'driver_name' => trim( $data[5], "\xEF\xBB\xBF"),
                'driver_telephone' => trim( $data[6], "\xEF\xBB\xBF"),
                'driver_residence' => trim( $data[7], "\xEF\xBB\xBF"),
                'status' => trim( $data[8], "\xEF\xBB\xBF"),
                'created_by' => $request->created_by
            ] );
        }

        return response()->json( [
            "success" => "data uploaded successfully.",
            "email" => $request->created_by
        ] );
    }

    public function upload_drivers( Request $request ) {
        $file = $request->file( 'file' );
        $fileContents = file( $file->getPathname() );
        
        foreach ( $fileContents as $line ) {
            $data = str_getcsv($line);
    
            Driver::create( [
                'first_name' => trim( $data[0], "\xEF\xBB\xBF"),
                'middle_name' => trim( $data[1], "\xEF\xBB\xBF"),
                'last_name' => trim( $data[2], "\xEF\xBB\xBF"),
                'gender' => trim( $data[3], "\xEF\xBB\xBF"),
                'license_number' => trim( $data[4], "\xEF\xBB\xBF"),
                'license_expiry_date' => trim( $data[5], "\xEF\xBB\xBF"),
                'residence' => trim( $data[6], "\xEF\xBB\xBF"),
                'phone_no' => trim( $data[7], "\xEF\xBB\xBF"),
                'company' => trim( $data[8], "\xEF\xBB\xBF"),
                'medication' => trim( $data[9], "\xEF\xBB\xBF"),
                'created_by' => $request->created_by
            ] );
        }

        return response()->json( [
            "success" => "data uploaded successfully."
        ] );
    }

    public function upload_rental_companies( Request $request ) {
        $file = $request->file('file');
        $fileContents = file($file->getPathname());
        
        foreach ($fileContents as $line) {
            $data = str_getcsv($line);
    
            Company::create( [
                'plate_number' => trim( $data[0], "\xEF\xBB\xBF"),
                'brand' => trim( $data[1], "\xEF\xBB\xBF"),
                'type' => trim( $data[2], "\xEF\xBB\xBF"),
                'mileage' => trim( $data[3], "\xEF\xBB\xBF"),
                'company' => trim( $data[4], "\xEF\xBB\xBF"),
                'driver_name' => trim( $data[5], "\xEF\xBB\xBF"),
                'driver_telephone' => trim( $data[6], "\xEF\xBB\xBF"),
                'driver_residence' => trim( $data[7], "\xEF\xBB\xBF"),
                'status' => trim( $data[8], "\xEF\xBB\xBF"),
                'created_by' => $request->created_by
            ] );
        }

        return response()->json( [
            "success" => "data uploaded successfully."
        ] );
    }

    public function fetchAllDelegates() {
        $results = Delegate::all();
        return response()->json( $results );
    }

    public function registerNEMAccount( Request $request ){
        return response()->json([
            'message' => 'We are here'
        ]);
    }

    public function getNewStaff() {
        $records = DB::select( "SELECT DISTINCT * FROM users WHERE type = 'NEW_STAFF' " );
        return response()->json( $records );
    }

    public function signInNEMAccount( Request $request ) {
        $record = DB::select("SELECT DISTINCT * FROM nema_users 
        WHERE email LIKE '%".$request->email."%'");

        return response()->json( $record );
    }

    public function loginUser( Request $request ) {
        $record = DB::select("SELECT DISTINCT * FROM users WHERE email_address LIKE '%".$request->username."%'");

        return response()->json( $record );
    }

    public function saveEIA( Request $request ) {
        $eia = new NEMAEIA;

        $eia->assesment_type = $request->assesment_type;
        $eia->name = $request->name;
        $eia->developer_name = $request->developer_name;

        $eia->save();

        return response()->json( [
            "data" => $eia
        ] );
    }
    public function fetchAllEIA(){
        $records =  NEMAEIA::all();
        return response()->json( $records );
    }

    public function fetchAllCars(){
        $records =  Vehicle::all();
        return response()->json( $records );
    }

    public function addDelegate( Request $request ) {
        $delegate = new Delegate;

        $delegate->accredited = $request->accredited;
        $delegate->airline = $request->airline;
        $delegate->arrival_time = $request->arrival_time;
        $delegate->country = $request->country;
        $delegate->departure_time = $request->departure_time;
        $delegate->email = $request->email_address;
        $delegate->first_name = $request->first_name;
        $delegate->middle_name = $request->middle_name;
        $delegate->last_name = $request->last_name;
        $delegate->flight = $request->flight_no;
        $delegate->gender = $request->gender;
        $delegate->hotel = $request->hotel;
        $delegate->passport_number = $request->passport_number;
        $delegate->summit = $request->summit;
        $delegate->telephone = $request->telephone;

        $delegate->save();

        return response()->json( [
            "success" => true,
            "data" => $delegate
        ] );
    }

    public function updateDelegate( Request $request ) {
        $delegate = Delegate::where( "id", $request->id );
 
        $delegate->accredited = $request->accredited;
        $delegate->airline = $request->airline;
        $delegate->arrival_time = $request->arrival_time;
        $delegate->country = $request->country;
        $delegate->departure_time = $request->departure_time;
        $delegate->email = $request->email_address;
        $delegate->first_name = $request->first_name;
        $delegate->middle_name = $request->middle_name;
        $delegate->last_name = $request->last_name;
        $delegate->flight = $request->flight_no;
        $delegate->gender = $request->gender;
        $delegate->hotel = $request->hotel;
        $delegate->summit = $request->summit;
        $delegate->telephone = $request->telephone;
        $delegate->passport_number = $request->passport_number;

        $delegate->save();
        

        return response()->json( [
            "message" => "The Delegate Record has been successfully updated"
        ] );
     }

    
    
    
     public function saveTOR( Request $request ) {
        $eia = new NEMATOR;

        $eia->tin = $request->tin;
        $eia->nationalid = $request->nationalid;
        $eia->developer_name = $request->developer_name;

        $eia->save();

        return response()->json( [
            "data" => $eia
        ] );
    }

    public function addCar( Request $request ) {
        $car = new Vehicle;

        $car->brand = $request->brand;
        $car->type = $request->type;
        $car->color = $request->color;
        $car->mileage = $request->mileage;
        $car->company = $request->company;
        $car->plate_number = $request->plate_number;
        $car->status = $request->status;
        $car->driver_name = $request->driver_name;
        $car->driver_residence = $request->driver_residence;
        $car->driver_telephone = $request->driver_telephone;
        $car->created_by = $request->created_by;

        $car->save();

        return response()->json( [
            "success" => true,
            "data" => $car
        ] );
    }

    public function updateCar( Request $request ) {
        $sql = "UPDATE vehicles 
            SET brand = '".$request->brand."',
            color = '".$request->color."',
            plate_number = '".$request->plate_number."',
            status = '".$request->status."',
            type = '".$request->type."',
            mileage = '".$request->mileage."',
            driver_name = '".$request->driver_name."',
            driver_residence = '".$request->driver_residence."',
            driver_telephone = '".$request->driver_telephone."',
            created_by = '".$request->created_by."',
        WHERE id = ".$request->id."";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json( [
            "message" => "The Car has been successfully updated"
        ] );
    }

    public function fetchAllDrivers(){
        $records =  Driver::all();
        return response()->json( $records );
    }

    public function addDriver( Request $request ) {
        $driver = new Driver;

        $driver->first_name = $request->first_name;
        $driver->middle_name = $request->middle_name;
        $driver->last_name = $request->last_name;
        $driver->gender = $request->gender;
        $driver->phone_no = $request->phone_no;
        $driver->country = $request->country;
        $driver->residence = $request->residence;
        $driver->license_number = $request->license_number;
        $driver->license_expiry_date = $request->license_expiry_date;
        $driver->company = $request->company;
        $driver->status = $request->status;
        $driver->medication = $request->medication;
        $driver->created_by = $request->created_by;

        $driver->save();

        return response()->json( [
            "success" => true,
            "data" => $driver
        ] );
    }

    public function updateDriver( Request $request ) {
        $sql = "UPDATE drivers 
            SET first_name = '".$request->first_name."',
            middle_name = '".$request->middle_name."',
            last_name = '".$request->last_name."',
            gender = '".$request->gender."',
            status = '".$request->status."',
            country = '".$request->country."',
            residence = '".$request->residence."',
            license_number = '".$request->license_number."',
            company = '".$request->company."',
            license_expiry_date = '".$request->license_expiry_date."',
            phone_no = '".$request->phone_no."',
            medication = '".$request->medication."',
            created_by = '".$request->created_by."'
        WHERE id = ".$request->id."";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json( [
            "message" => "The Driver has been successfully updated"
        ] );
    }

    public function fetchAllCompanies(){
        $records =  Company::all();
        return response()->json( $records );
    }

    public function addCompany( Request $request ) {
        $company = new Company;

        $company->name = $request->name;
        $company->physical_location = $request->physical_location;
        $company->contact_person = $request->contact_person;
        $company->phone_number = $request->phone_number;
        $company->email_address = $request->email_address;
        $company->number_cars = $request->number_cars;
        $company->inspected = $request->inspected;
        $company->created_by = $request->created_by;
        $company->status = $request->status;
        $company->created_by = $request->created_by;


        $company->save();

        return response()->json( [
            "success" => true,
            "data" => $company
        ] );
    }

    public function updateCompany( Request $request ) {
        $sql = "UPDATE rental_companies 
            SET name = '".$request->name."',
            physical_location = '".$request->physical_location."',
            contact_person = '".$request->contact_person."',
            status = '".$request->status."',
            email_address = '".$request->email_address."',
            inspected = '".$request->inspected."',
            created_by = '".$request->created_by."',
            phone_number = '".$request->phone_number."',
            created_by = '".$request->created_by."'
        WHERE id = ".$request->id."";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json( [
            "message" => "The Company has been successfully updated"
        ] );
    }

    public function fetchAllContacts(){
        $records =  Contact::all();
        return response()->json( $records );
    }

    public function addContact( Request $request ) {
        $contact = new Contact;

        $contact->first_name = $request->first_name;
        $contact->middle_name = $request->middle_name;
        $contact->last_name = $request->last_name;
        $contact->gender = $request->gender;
        $contact->national_id = $request->national_id;
        $contact->institution = $request->institution;
        $contact->title = $request->title;
        $contact->created_by = $request->created_by;
        $contact->status = $request->status;
        $contact->phone_number = $request->phone_number;
        $contact->email_address = $request->email_address;
        $contact->country = $request->country;
        $contact->work_id = $request->work_id;
        $contact->created_by = $request->created_by;

        $contact->save();

        return response()->json( [
            "success" => true,
            "data" => $contact
        ] );
    }

    public function updateContact( Request $request ) {
        $sql = "UPDATE contacts 
            SET first_name = '".$request->first_name."',
            middle_name = '".$request->middle_name."',
            last_name = '".$request->last_name."',
            gender = '".$request->gender."',
            national_id = '".$request->national_id."',
            institution = '".$request->institution."',
            title = '".$request->title."',
            created_by = '".$request->created_by."',
            email_address = '".$request->email_address."',
            phone_number = '".$request->phone_number."',
            work_id = '".$request->work_id."',
            created_by = '".$request->created_by."'
        WHERE id = ".$request->id."";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json( [
            "message" => "The Contact has been successfully updated"
        ] );
    }

    public function addMOFAUser( Request $request ) {
        $user = new MOFAUser;

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->organisation = $request->organisation;
        $user->phone_no = $request->phone_no;
        $user->role = $request->role;
        $user->passcode = $request->passcode;
        $user->created_by = $request->created_by;
        $user->status = $request->status;
        $user->designation = $request->designation;
        $user->email_address = $request->email_address;
        $user->username = $request->username;
        $user->title = $request->title;


        $user->save();

        return response()->json( [
            "success" => true,
            "data" => $user
        ] );
    }

    public function loginMOFAUser( Request $request ) {
        $record = MOFAUser::where( "username", $request->username )->where( "passcode", $request->password )->first();
        return response()->json( [
            "success" => true,
            "record" => $record
        ] );
    }

    public function getMOFAUsers(){
        $records =  MOFAUser::all();
        return response()->json( $records );
    }


    public function fetchAllTOR(){
        $records =  NEMATOR::all();
        return response()->json( $records );
    }

    public function savePrac( Request $request ) {
        $eia = new NEMAPrac;

        $eia->tin = $request->tin;
        $eia->nationalid = $request->nationalid;
        $eia->gender = $request->gender;
        $eia->organisation = $request->organisation;

        $eia->save();

        return response()->json( [
            "data" => $eia
        ] );
    }
    public function fetchAllPrac(){
        $records =  NEMAPrac::all();
        return response()->json( $records );
    }

    public function saveAccessRecord( Request $request ) {
        $tracker = new Tracker;
        $tracker->application_id = $request->application_id;
        $tracker->form = $request->form;
        $tracker->progress = $request->progress;
        $tracker->remark = $request->remark;
        $tracker->assignee = $request->assignee;
        $tracker->access_role = $request->access_role;
        $tracker->organisation =$request->organisation;
        $tracker->vetting_tracker = $request->vetting_tracker;
        $tracker->stage = $request->stage;
        $tracker->action = $request->action;

        $tracker->save();

        if( $tracker->id ) {
            return response()->json( [
                "success" => true,
                "id" => $tracker->id,
                "message" => "The tracker record could has been Added"
            ] );
        }
        return response()->json( [
            "success" => false,
            "message" => "The tracker record could not be Added"
        ] );
    }

    public function fetchAccessRecords( Request $request ) {
        $sql = "SELECT DISTINCT * FROM tracker WHERE application_id =  ".$request->application_id." AND form LIKE '%".$request->form."%' ";
        $record = DB::select( $sql );
        //return the results
        return response()->json( $record );
    }

    public function updateAccessRecord( Request $request ) {
       $tracker = Tracker::where( "id", $request->id );

       $tracker->progress = $request->progress;
       $tracker->save();
       
       if( $tracker->id ) {
        return response()->json( [
            "message" => "The Access Record has been successfully updated"
        ] );
       }
    }

    public function saveItems( Request $request){
        if( sizeof( $request->items ) > 0 ){
            foreach( $request->items as $key => $value) {
                $sql = DB::table('procurement_items')->insert(
                    [ 'description' => $value[ 'itemDescription' ], 
                      'subject' => $value[ 'itemSubject' ],
                      'userid' => $value[ 'userid' ],
                      'quantity' => $value[ 'itemQuantity' ],
                      'unit_cost' => $value[ 'itemUnitCost' ],
                      'created_at' => now(),
                      'updated_at' =>  now()
                    ]
                );
           }
           return response()->json([
               "message" => "Procurement items were saved successfully in the database"
           ]);
        }
    }

    public function addSpouse( Request $request ){
        $record  = Spouses::where('staff_id',  $request->staff_id );
        if( !empty( $record ) ){
           $sql = DB::select('DELETE FROM staff_spouses WHERE staff_id 
                LIKE "%'.$request->staff_id.'%"  AND spouse_name !=""');
            
        }

        if( sizeof( $request->spouses ) > 0 ){
            foreach( $request->spouses as $spouse ){
                $sql = DB::table('staff_spouses')->insert(
                    [
                        'staff_id' => $request->staff_id,
                        'spouse_name' => $spouse['spouse_name'],
                        'spouse_phone' => $spouse['spouse_phone'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                        'type' => 'spouse'
                    ]
                );
            }
            return response()->json([
                "message" => "Spouses have been Added"
            ]);
        }
    }

    public function addChildren( Request $request ) {
        $record  = Children::where('staff_id',  $request->staff_id );
        if( !empty( $record ) ){
           $sql = DB::select('DELETE FROM staff_children WHERE staff_id 
                LIKE "%'.$request->staff_id.'%" AND child_name !="" ');
            
        }

        if( sizeof( $request->children ) > 0 ){
            foreach( $request->children as $child ){
                $sql = DB::table('staff_children')->insert(
                    [
                        'staff_id' => $request->staff_id,
                        'child_name' => $child['child_name'],
                        'created_at' => now(),
                        'updated_at' =>  now(),
                        'type' => 'child'
                    ]
                );
            }
            return response()->json([
                "message" => "Spouse Children have been Added"
            ]);
        }
    }

    public function getChildren(Request $request){
        $record = DB::select("SELECT DISTINCT id, staff_id, child_name, created_at, updated_at  
            FROM staff_children 
            WHERE staff_id LIKE  '".$request->staff_id."'");

        if( !empty( $record ) ){
            return response()->json( $record  );
        }
    }

    public function getSpouses(Request $request){
        $record = DB::select("SELECT DISTINCT id, staff_id, spouse_name, spouse_phone, created_at, updated_at  
            FROM staff_spouses 
            WHERE staff_id LIKE  '".$request->staff_id."'");

        if( !empty( $record ) ){
            return response()->json( $record  );
        }
    }

    public function store(Request $request) {
        //capture all input field names
        $requistion = new Requistion;
        $requistion->staff_name = $request->staff_name;
        $requistion->staff_id = $request->staff_id;
        $requistion->date = $request->date;
        $requistion->title = $request->title;
        $requistion->department = $request->department;
        $requistion->email_address = $request->email_address;
        $requistion->organisation = $request->organisation;
        $requistion->status = $request->status;
        $requistion->subject = $request->subject;
        $requistion->description = $request->description;
        $requistion->remarks = $request->remarks;
        $requistion->remarks_by = $request->remarks_by;
        $requistion->recommender = $request->recommender;
        $requistion->feature = 'requisition';
        $record->cashier_approved = 'no';
        $record->director_approved = 'no';

        $requistion->save();
        if( $requistion->id ){
            return response()->json([
                'success' => true,
                'data' => $requistion
            ]);
        }
        
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }

    public function fetchAll(){
       $records =  Requistion::all();
       return response()->json( $records );
    }
    public function getAll(Request $request){
        if($request->role == "MD"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%MD%' ");
        }
        if($request->role == "ACCOUNTANT"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%PENDING_ACCOUNTANT%' ");
        }
        if($request->role == "GM_FINANCE"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%GM_FINANCE%' 
            OR status LIKE '%GM_AUTHORIZE_VOUCHER%' OR status LIKE '%GM_FINANCE_AUTHORIZE%' 
            OR status LIKE '%PENDING_GM_AUTHORIZATION%' OR status LIKE '%PENDING_GM_FINANCE_TO_PAY%' ");
        }
        if($request->role == "CIA"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%CIA%' ");
        }
        if($request->role == "HOD"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE department LIKE '%".$request->department."%' ");
        }
        if($request->role === "CASHIER"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%CASHIER%' 
                OR status LIKE '%PROCESS_PAYMENT%' OR status LIKE '%PAYING%'   OR status LIKE '%PROCESSING_PAYMENT%'
                OR status LIKE '%PAID%'  ");
        }
        if($request->role === "PROCUREMENT"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE status LIKE '%PENDING_PROCUREMENT%'");
        }
        if($request->role === "User"){
            $records = DB::select("SELECT * FROM payment_requistion WHERE email_address LIKE '%".$request->email_address."%' ");
        }
        if($request->role === "All"){
            $records = DB::select("SELECT DISTINCT * FROM payment_requistion");
        }
       
        if( !empty( $records ) ){
            return response(
                [ 
                    'success' => true,
                    'results' => $records
                ], 200 );
        }
        return response([ 
            'success' => false, 
            'message' => 'There are no records available on the server' 
        ]);
    }
    public function getHOD(Request $request){
        $records = DB::select("SELECT DISTINCT * FROM payment_requistion 
        WHERE department LIKE '%".$request->department."%' AND 
        status LIKE '%PENDING_HOD%' ");

        return response()->json( $records );
    }
    public function updateItem(Request $request){
        $record = Requistion::where( 'id', $request->id )->first();
        $record->status = $request->input('status');
        $record->belongsTo = $request->input('belongsTo');
        $record->feature = !empty( $request->input('feature') ) ?  $request->input('feature') : 'requisition';
        
        if( !is_null( $request->input('director_approved') ) ){
            $record->director_approved = $request->input('director_approved');
        }
        if( !is_null( $request->input('director_date_approved') ) ){
            $record->director_date_approved = $request->input('director_date_approved');
        }
        if( !is_null( $request->input('cashier_approved') ) ){
            $record->cashier_approved = $request->input('cashier_approved');
        }
        if( !is_null( $request->input('cashier_date_approved') ) ){
            $record->cashier_date_approved = $request->input('cashier_date_approved');
        }
        if( !is_null( $request->input('voucher_approved') ) ){
            $record->voucher_approved = $request->input('voucher_approved');
        }
        if( !is_null( $request->input('voucher_date_approved') ) ){
            $record->voucher_date_approved = $request->input('voucher_date_approved');
        }
        if( !is_null( $request->input('voucher_id') ) ){
            $record->voucher_id = $request->input('voucher_id');
        }
        $record->save();

        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function updateCashierVoucher(Request $request){
        $record = Requistion::where('id', $request->id )->first();
        $record->status = $request->input('status');
        $record->voucher_id = $request->input('voucher_id');
        $record->belongsTo = $request->input('belongsTo');
        $record->feature = !empty( $request->input('feature') ) ?  $request->input('feature') : 'requisition';
        $record->payee = $request->input('payee');
        $record->cashier_amount = $request->input('cashier_amount');
        $record->cashier_description = $request->input('cashier_description');
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function getPaymentVoucher(Request $request){
        $records =Requistion::where('id', $request->id )->first();
        return response()->json( $records );
    }

    public function updateRequisition(Request $request){
        $record = Requistion::where('id', $request->id)->first();
        $record->status = $request->input('status');
        $record->department = $request->input('department');
        $record->subject = $request->input('subject');
        $record->description = $request->input('description');
        $record->revert = $request->input('revert');
        $record->email_address = $request->input('email_address');
        $record->feature = 'requisition';
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }

    public function approved(){
        $record = DB::select("SELECT * FROM payment_requistion WHERE status = 'PAID' OR status = 'CONTRACT_AWARDED' ");
        return response()->json([
            "success" => true,
            "data" => $record
        ]);
    }

    public function getProcurementItems(Request $request){
        $record = DB::table( 'procurement_items' )->where('userid', $request->staff_id )->get();
        return response()->json( $record );
    }
    public function getRole(Request $request){
        $record = DB::table('users')->where( 'email_address', $request->email )->get();
        return response()->json( $record );
    }
    public function getRemarks(Request $request){
        if( $request->station ) {
            $records = DB::select("SELECT DISTINCT * FROM user_remarks WHERE requisition_id = ".$request->id."");
            return response()->json( $records );
        }
        else {
            $records = DB::select("SELECT DISTINCT * FROM user_remarks WHERE requisition_id = ".$request->id." AND destination='".$request->destination."'");
            return response()->json( $records );
        }
       
    }
    public function add_procure_document( Request $request ) {
        $document = new DocumentFile;
        $document->file = $request->file;
        $document->name = $request->title;
        $document->size = $request->size;
        $document->type = $request->type;
        $document->description = $request->details;
        $document->requisition_id = $request->requisition_id;
        $document->details = $request->details;
        // $document->status = $request->status;
        $document->created_at = now();
        $document->updated_at = now();

        $document->save();
    }
    public function add_remark(Request $request){
        $remark = new Remark;
        $remark->user_id = $request->user_id;
        $remark->requisition_id = $request->request_id;
        $remark->date = $request->date;
        $remark->remarks = $request->remarks; 
        $remark->remarks_by = $request->remarks_by;
        $remark->organisation = $request->organisation;
        $remark->destination = $request->destination;
        $remark->type = $request->type;
        $remark->email_address = $request->email_address;
        $remark->created_at = now();
        $remark->updated_at = now();

        $remark->save();
        if( $remark->id > 0 ){
            return response()->json([
                'message' => 'Remark has been saved successfully'
            ]);
        }

    }
    public function getOfficerRemarks(Request $request){
        $results = DB::select( "SELECT * FROM user_remarks 
            WHERE destination = 'User' 
            AND email_address = ".$request->id."" );
        return response()->json( $results );
    }
    public function getUserByEmail( Request $request ) {
        $record = DB::select( "SELECT DISTINCT * FROM users WHERE email_address = '".$request->email_address."'" );
        if( !empty( $record ) ){
            return response()->json( $record );
        }
        else {
            // Test
        }
    }
    public function generateReport(Request $request) {
        require base_path().'/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['tempDir'=>storage_path('tempdir')]);

        $html ='<body style="display: flex; background-color: #FFFDD0;">
            <div style="height:100%; width: 100%;">
                <div style="display:flex; justify-content: center; align-items: center;margin: 0 auto; padding">
                    <img src="'.public_path().'/images/logo_nfa.png'.'" alt="NFA Logo" 
                        style="object-fit: contain;height: 100px; padding: 2px; margin-left: 40mm;" />
                </div>

                <div>
                    <h2 style="font-size: 16px; padding-left: 20mm;">THE NATIONAL FORESTRY AND TREE PLANTING ACT No. 8 /2003</h2>
                    <h4 style="font-size: 16px; padding-left: 20mm; padding-top: 5mm">TREE FARMING LICENSE IN THE CENTRAL FOREST RESERVES</h4>
                <div>
                <div>
                    <p style="line-height: 8mm;padding: 2mm;">
                        <span style="font-weight: bold;">No:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->licenseID.'</span> 
                        <span style="font-weight: bold">Date:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->datePrepared.'</span> 
                        <span style="font-weight: bold">Management Area:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->range.'</span>, 
                        <span style="font-weight: bold">Sector:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->sector.'</span>
                        Subject to provisions of the National Forestry and Tree Planting Act( No.8/2003) and 
                        any Regulations as saved by the Act or made under and to the terms and conditions stated herein.
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        M/S. <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->name.'</span>
                        (Licensees) of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->address.'</span> 
                        <span style="font-weight: bold">Tel:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->telephone.'</span>  
                        <span style="font-weight: bold">Email Address:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->email_address.'</span>
                        is hereby granted license by the National Foresty Authority( Licensor ) to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->purpose.'</span>  on an <span style="font-weight:bold;">Area</span> of 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->hectaresAllocated.' hectares</span>
                        in <span style="font-weight: bold">Block No: </span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->blocknumber.'</span>  
                        <span style="margin-left: 0.25mm;padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->reserve.'</span>  <span style="font-weight: bold">Central Forest Reserve</span>.
                        This License is valid for a <span style="font-weight: bold;">Period</span> of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->period.' year(s)</span>
                        from <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->startdate.'</span> to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->end_date.'</span>
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        License fees shall be paid on an annual basis for the area of land allocated or under license at a rate reserved 
                        in the license agreement per hectare for purposes of growing trees.
                        
                        <div style="padding-bottom: 2mm;">
                        <h4>APPROVED BY THE DIRECTOR:</h4>
                        Signature:<div style="border-bottom: 1px dotted black; width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="margin-top: 1mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->director.'</span>
                        </div>

                        <div style="padding-top: 2mm;">
                        <h4>AUTHORISED BY THE EXECUTIVE DIRECTOR:</h4>
                        Signature: <div style="border-bottom: 1px dotted black;width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="padding-top: 3mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->executive_director.'</span>
                        </div>
                    </p>
                        
                    <p>
                        <div style="border-bottom: 1px solid green; padding-top: 2mm; width: 100%;" />
                    </p>
                    <p style="padding-bottom: 2mm;">
                        <div><span style="font-weight: bold;">Copies to:</span> Original to Licensee; Range Manager; Finance Department;</div>
                        <div>Notes:</div>
                    </p>

                </div>

                </div>
            </div>
        </body>';
        $mpdf->setTitle("National Forestry Authority License");
        $mpdf->showWatermarkImage = false;
        $mpdf->setDisplayMode( 'fullpage' );
        $mpdf->WriteHTML( $html );
        $location = public_path().'/assets/';

        $mpdf->Output( 'License_'.$request->id.'.pdf', "F" );

        $filePath = public_path( ".pdf" );
        $headers = [ "Content-Type: application/pdf" ];
        
        return response()->json( [
            "success" => true,
            "message" => "File has been create and saved "
        ] );
    }
    public function generateMembership(Request $request) {
        require base_path().'/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['tempDir'=>storage_path('tempdir')]);

        $html ='<body style="height:100vh; width: 100vw;font-family: ubuntu;">
        <div style="border: 4px solid #2b3665;">
            <div style="padding-top: 5px;">
                <img src="'.public_path().'/images/usf_logo.jpeg'.'" alt="USF Logo" style="object-fit: contain; height: 150px; margin-left: 300px; margin-right: 300px;" />
            </div>
            <div style="width: 100%;line-height: 18px;">
                <h4 style="color: #b23b2f; font-size: 20px;text-align: center;">UGANDA SWIMMING FEDERATION</h4>
                <h1 style="text-decoration: underline;text-align: center;color: #3d456b;font-family: industrial;font-weight: normal;font-size: 50px;">
                    MEMBERSHIP CERTIFICATE
                </h1>
            </div>

            <div style="line-height: 16px;">
                <p style="text-align: center;color: #44403a;font-size: 20px;">This is to certify that</p>
                <h3 style="font-weight: normal;text-align: center;color:#82724f;font-family: industrial;font-size: 48px;">
                   '.$request->name.'
                </h3>

                <p style="text-align: center;color: #44403a;font-size: 20px;">
                    is a duly registered Member of the
                </p>
                <p style="text-align: center;color: #44403a;font-size: 20px;">
                    Uganda Swimming Federation under
                </p>
                <h5 style="font-weight: normal;font-family: industrial;text-align: center;color: #82724f;font-size: 40px;">
                    Reg. No. USF '.$request->registration_number.'
                </h5>

            </div>
            
            <div style="line-height: 24px;margin-left: 15px; margin-right: 15px;">
                <p style="color: #293669;font-size: 20px;font-weight: normal;">
                    This certificate is issued under seal this 4th day of January 2019 and shall remain valid
                    as long as its holder maintains its Membership with the Uganda Swimming Federation.
                </p>
            </div>


            <div style="margin-left: 15px; margin-right: 15px;margin-top:15px; margin-bottom: 15px;">
                <div style="float:left;width: 250px;height: 100px; padding-top: 50px;">
                    <div style="font-size: 16px;border-top: 2px solid #34385a; color: #34385a; font-weight: bold;">
                        Donald Rukare(Dr.)
                    </div>
                    <div style="color: #34385a;font-size: 16px;">PRESIDENT</div>
                </div>

                <div style="margin-left: 30px;float:left; outline: none;width: 100px; height: 100px;background-color: #5c4729;border-radius: 70px;">
                    <div style="color: #FFF;font-size: 14px; text-align: center;margin: 40;">
                        SEAL
                    </div>
                </div>

                <div style="float:left;width: 250px;height: 100px; padding-top: 50px; margin-left: 25px;">
                    <div style="font-size: 16px;border-top: 2px solid #34385a; color: #34385a; font-weight: bold;">
                        Moses Mwase
                    </div>
                    <div style="color: #34385a;font-size: 16px;">SECRETARY GENERAL</div>
                </div>
            </div>

            <div style="margin-left: 15px; margin-right: 15px;">
                <p style="color: #363d5a;">USF is a registered entity and is affliated to Fina, Cana, UOC, NCS</p>
            </div>

            <div>
                <p style="text-align: center;color: #e91e63; font-size: 16px; font-weight: bold;">S/N 002</p>
            </div>
        </div>
        
     </body>';

        $mpdf->setTitle("Uganda Swimming Federation");
        $mpdf->showWatermarkImage = false;
        $mpdf->setDisplayMode( 'fullpage' );
        $mpdf->WriteHTML( $html );
        $location = public_path().'/assets/';

        $mpdf->Output( 'Membership_'.$request->id.'.pdf', "F" );

        $filePath = public_path( ".pdf" );
        $headers = [ "Content-Type: application/pdf" ];
        
        return response()->json( [
            "success" => true,
            "message" => "File has been create and saved "
        ] );
    }
    public function downloadLicense( $name ){
        $filePath = public_path( "/".$name.".pdf" );
        $headers = [ "Content-Type: application/pdf" ];
        $fileName = $name.".pdf";

        return response()->download( $filePath, $fileName, $headers );
    }
    public function downloadMembership( $name ) {
        $filePath = public_path( "/".$name.".pdf" );
        $headers = [ "Content-Type: application/pdf" ];
        $fileName = $name.".pdf";

        return response()->download( $filePath, $fileName, $headers );
    }
    public function fetchApprovedRequests(Request $request) {
        if( $request->type == "director"){
            $records = Requistion::where('director_approved', "yes")->get();
            if(!empty( $records ) ){
                return response()->json( $records );
            }
        }
        if( $request->type == "cashier"){
            $records = Requistion::where('cashier_approved', "yes")->get();
            if(!empty( $records ) ){
                return response()->json( $records );
            }
        }

        if( $request->type == "signed"){
            $records = Requistion::where('voucher_approved', "yes")->get();
            if(!empty( $records ) ){
                return response()->json( $records );
            }
        }
    }

    public function getAllUsers(){
        $sql = "SELECT distinct * FROM users GROUP BY id";
        $results = DB::select( DB::raw( $sql ) );

        return response()->json( $results );
    }

    public function deleteStaffFile( Request $request ) {
        $sql = "DELETE FROM staff_files WHERE id = ".$request->id."";
        $deleted = DB::select( DB::raw( $sql ) );
        return response()->json( $deleted );
    }

    public function updateUser(Request $request){
        $sql = "UPDATE users 
            SET role = '".$request->role."',
                position = '".$request->position."',
                department = '".$request->department."',
                department_no = '".$request->department_no."',
                next_of_kin = '".$request->next_of_kin."',
                nok_phone = '".$request->nok_phone."',
                title = '".$request->title."',
                designation = '".$request->designation."',
                nssf_no = '".$request->nssf_no."'
            WHERE email_address = '".$request->email_address."'";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json([
            "success" => true,
            "message" => "User Updated"
        ]);
    }

    public function updatePassword(Request $request){
        $encrypted = bcrypt($request->password);
        $sql = "UPDATE users 
            SET
            password = '".$encrypted."',
            passcode = '".$request->password."'
        WHERE email_address = '".$request->email_address."'";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json([
            "success" => true,
            "message" => $encrypted
        ]);
    }

    public function createDocument(Request $request) {
        // $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        // $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        // require_once $root. '/dms_backend/vendor/autoload.php';
        // $file =  $root.'/dms_backend/documents/file1.pdf';

        // $mpdf = new \Mpdf\Mpdf();

        // $html ='<body style="display: flex; background-color: #ffe087;">
        //     <div style="height:100%; width: 100%;">
        //        <div style="display:flex; justify-content: center; align-items: center;margin: 0 auto; padding">
        //         <img src="'.$root.'/dms_backend/assets/logo.png'.'" alt="NFA Logo" 
        //             style="object-fit: contain;height: 100px; padding: 2px; margin-left: 40mm;" />
        //        </div>

        //         <div>
        //             <h2 style="font-size: 16px; padding-left: 20mm;">THE NATIONAL FORESTRY AND TREE PLANTING ACT No. 8 /2003</h2>
        //             <h4 style="font-size: 16px; padding-left: 20mm; padding-top: 5mm">TREE FARMING LICENSE IN THE CENTRAL FOREST RESERVES</h4>
        //         <div>
        //         <div>
        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 <span style="font-weight: bold;">No:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->licenseID.'</span> 
        //                 <span style="font-weight: bold">Date:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->datePrepared.'</span> 
        //                 <span style="font-weight: bold">Management Area:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->range.'</span>, 
        //                 <span style="font-weight: bold">Sector:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->sector.'</span>
        //                 Subject to provisions of the National Forestry and Tree Planting Act( No.8/2003) and 
        //                 any Regulations as saved by the Act or made under and to the terms and conditions stated herein.
        //             </p>

        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 M/S. <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->name.'</span>
        //                 (Licensees) of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->address.'</span> 
        //                 <span style="font-weight: bold">Tel:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->telephone.'</span>  
        //                 <span style="font-weight: bold">Email Address:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->email_address.'</span>
        //                 is hereby granted license by the National Foresty Authority( Licensor ) to 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->purpose.'</span>  on an <span style="font-weight:bold;">Area</span> of 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->hectaresAllocated.' hectares</span>
        //                 in <span style="font-weight: bold">Block No: </span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->blocknumber.'</span>  
        //                 <span style="margin-left: 0.25mm;padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->reserve.'</span>  <span style="font-weight: bold">Central Forest Reserve</span>.
        //                 This License is valid for a <span style="font-weight: bold;">Period</span> of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->period.' year(s)</span>
        //                 from <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->startdate.'</span> to 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->end_date.'</span>
        //             </p>

        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 License fees shall be paid on an annual basis for the area of land allocated or under license at a rate reserved 
        //                 in the license agreement per hectare for purposes of growing trees.
                        
        //                 <div style="padding-bottom: 2mm;">
        //                 <h4>APPROVED BY THE DIRECTOR:</h4>
        //                 Signature:<div style="border-bottom: 1px dotted black; width: 80%;" />
        //                 <span style="padding-bottom: 2mm;"/>
        //                 Name: <span style="margin-top: 1mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->director.'</span>
        //                 </div>

        //                 <div style="padding-top: 2mm;">
        //                 <h4>AUTHORISED BY THE EXECUTIVE DIRECTOR:</h4>
        //                 Signature: <div style="border-bottom: 1px dotted black;width: 80%;" />
        //                 <span style="padding-bottom: 2mm;"/>
        //                 Name: <span style="padding-top: 3mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->executive_director.'</span>
        //                 </div>
        //             </p>
                       
        //             <p>
        //                 <div style="border-bottom: 1px solid green; padding-top: 2mm; width: 100%;" />
        //             </p>
        //             <p style="padding-bottom: 2mm;">
        //                 <div><span style="font-weight: bold;">Copies to:</span> Original to Licensee; Range Manager; Finance Department;</div>
        //                 <div>Notes:</div>
        //             </p>

        //         </div>

        //        </div>
        //     </div>
        // </body>';
        // $mpdf->setTitle("List of Approved Requisitions");
        // $mpdf->showWatermarkImage = false;
        // $mpdf->setDisplayMode('fullpage');
        // $mpdf->WriteHTML( $html );
        // $mpdf->Output('output.pdf','F');

        // $path = public_path('output.pdf');
        // $data = file_get_contents($path);

        // $base64 = base64_encode($data);
        // // return response()->json( [ "file" => $base64 ] );
        // return response()->download( $file, 'filename.pdf');

    }

    public function generateDDALicense( Request $request ){
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";

        $html = '
            <html>
                <head>
                    <style>
                        .container {
                            height: 100%;
                            width: 100%;
                        }
                        .image-container {
                            display: flex;
                            justify-items: center;
                            align-items: center;
                            width: 100%;
                        }
                        .logo {
                            width: 120;
                            padding-left: 65mm;
                        }
                        .centered {
                            text-align: center;
                        }
                        .subtitle {
                            font-size: 14px;
                        }
                        span.first {
                            padding-left: 0;
                            padding-right: 25mm;
                        }
                        span.last {
                            padding-left: 25mm;
                            padding-right: 0;
                        }
                        .horizontal_dotted_line:after {
                            content: "................................";
                            padding: 5px;
                        }
                        .company_name_text {
                            border-bottom: 2px dotted;
                            padding: 10px;
                            width: 100%;
                        }
                        .label-title {
                            padding-top: 1em;
                        }
                    </style>
                </head>
            <body>
                <div class="container">
                    <div class="image-container">
                        <img class="logo" src="assets/dda_logo.png" />&nbsp;
                    </div>
                    <div>
                        <h3 class="centered title">DAIRY DEVELOPMENT AUTHORITY</h3>
                        <h5 class="centered">ESTABLISHED BY ACT OF PARLIAMENT - THE DAIRY INDUSTRY ACT, 1998 </h5>
                        <p class="centered subtitle">The Dairy ( Marketing and Processing of milk and milk Products ) Regulations, 2003 and as<br/> amended 2006</p>
                        <h5 class="centered">CERTIFICATE OF REGISTRATION TO OPERATE COOLERS</h5>

                        <div class="flexed centered">
                            <span class="first">FIFTH SCHEDULE</span>
                            <span class="last">REG. 10( 4 )</span>
                        </div>

                        <div class="flexed centered">
                            <span class="first">FIFTH SCHEDULE</span>
                            <span class="last">REG. 10( 4 )</span>
                        </div>
                        <div>
                            <p>This certificate is hereby issued to: <br/></p>
                            
                            <div>
                                <div class="label-title">Name of Company: <span class="company_name_text"></span></div>
                                <div class="label-title">Address: <span class="company_name_text"></span></text>
                                <div class="label-title">Valid from 1st January 2021 to the 31st December 2022</div>
                                <div>Amount ..................<div>
                                <div>Receipt No ..............</div>
                                <div>
                                    <p>This certificate is issued on the following conditions:</p>
                                    <ol>
                                        <li> The premise and surrondings should be hygienically kept </li>
                                        <li> The personnel should be periodically examined and be issued with medical certificates for health fitness</li>
                                        <li> Only fresh and wholesome raw milk should be sold </li>
                                        <li> No other goods and materials should be kept or sold in the Dairy </li>
                                    </ol>
                                </div>

                                <div>
                                    <span>....................</span>
                                    <h6>EXECUTIVE DIRECTOR</h6>
                                    <p>Dairy Development Authority</p>
                                </div>

                                <div>
                                    <span>....................</span>
                                    <h6>DATE</h6>
                                </div>

                                <b>P.O.Box 34006, Tel: 256-41-343901/3, Kampala. E-mail: ed@dda.or.ug; website: https://www.dda.go.ug</b>
                            </div>
                        </div>
                    </div>
                </div>
            </body>';

            
            require_once $root. '/dms_backend/vendor/autoload.php';

            $mpdf = new \Mpdf\Mpdf( [
                'margin_left' => 20,
                'margin_right' => 15,
                'margin_top' => 25,
                'margin_bottom' => 25,
                'margin_header' => 10,
                'margin_footer' => 10
            ] );

            $mpdf->SetProtection( array( 'print' ) );
            $mpdf->SetTitle( 'DAIRY DEVELOPMENT AUTHORITY' );
            $mpdf->SetAuthor("NITA-U");
            $mpdf->watermark_font = 'DejaVuSansCondensed';

            $mpdf->SetDisplayMode( 'fullpage' );
            $mpdf->SetWatermarkImage('assets/dda_logo.png');
            $mpdf->showWatermarkImage = true;
            $mpdf->watermarkTextAlpha = 0.5;

            $mpdf->WriteHTML( $html );
            // $mpdf->Output();
            $mpdf->Output('dda.pdf','F');

            $path = public_path('dda.pdf');
            $data = file_get_contents($path);

            $base64 = base64_encode($data);
            return response()->json( [ "file" => $base64 ] );
    }


    public function downloadDocument(Request $request) {
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        require_once $root. '/dms_backend/vendor/autoload.php';
        $file =  $root.'/dms_backend/documents/file1.pdf';

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML('<div>Section 1 text</div>');

        $mpdf->Output('output.pdf','F');

        $path = public_path( $file );
        $data = file_get_contents($path);

        $base64 = base64_encode($data);

        $html ='<body style="display: flex; background-color: #ffe087;">
            <div style="height:100%; width: 100%;">
                <div style="display:flex; justify-content: center; align-items: center;margin: 0 auto; padding">
                <img src="'.$root.'/dms_backend/assets/logo.png'.'" alt="NFA Logo" 
                    style="object-fit: contain;height: 100px; padding: 2px; margin-left: 40mm;" />
                </div>

                <div>
                    <h2 style="font-size: 16px; padding-left: 20mm;">THE NATIONAL FORESTRY AND TREE PLANTING ACT No. 8 /2003</h2>
                    <h4 style="font-size: 16px; padding-left: 20mm; padding-top: 5mm">TREE FARMING LICENSE IN THE CENTRAL FOREST RESERVES</h4>
                <div>
                <div>
                    <p style="line-height: 8mm;padding: 2mm;">
                        <span style="font-weight: bold;">No:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->licenseID.'</span> 
                        <span style="font-weight: bold">Date:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->datePrepared.'</span> 
                        <span style="font-weight: bold">Management Area:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->range.'</span>, 
                        <span style="font-weight: bold">Sector:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->sector.'</span>
                        Subject to provisions of the National Forestry and Tree Planting Act( No.8/2003) and 
                        any Regulations as saved by the Act or made under and to the terms and conditions stated herein.
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        M/S. <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->name.'</span>
                        (Licensees) of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->address.'</span> 
                        <span style="font-weight: bold">Tel:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->telephone.'</span>  
                        <span style="font-weight: bold">Email Address:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->email_address.'</span>
                        is hereby granted license by the National Foresty Authority( Licensor ) to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->purpose.'</span>  on an <span style="font-weight:bold;">Area</span> of 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->hectaresAllocated.' hectares</span>
                        in <span style="font-weight: bold">Block No: </span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->blocknumber.'</span>  
                        <span style="margin-left: 0.25mm;padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->reserve.'</span>  <span style="font-weight: bold">Central Forest Reserve</span>.
                        This License is valid for a <span style="font-weight: bold;">Period</span> of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->period.' year(s)</span>
                        from <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->startdate.'</span> to 
                        <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->end_date.'</span>
                    </p>

                    <p style="line-height: 8mm;padding: 2mm;">
                        License fees shall be paid on an annual basis for the area of land allocated or under license at a rate reserved 
                        in the license agreement per hectare for purposes of growing trees.
                        
                        <div style="padding-bottom: 2mm;">
                        <h4>APPROVED BY THE DIRECTOR:</h4>
                        Signature:<div style="border-bottom: 1px dotted black; width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="margin-top: 1mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->director.'</span>
                        </div>

                        <div style="padding-top: 2mm;">
                        <h4>AUTHORISED BY THE EXECUTIVE DIRECTOR:</h4>
                        Signature: <div style="border-bottom: 1px dotted black;width: 80%;" />
                        <span style="padding-bottom: 2mm;"/>
                        Name: <span style="padding-top: 3mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->executive_director.'</span>
                        </div>
                    </p>
                        
                    <p>
                        <div style="border-bottom: 1px solid green; padding-top: 2mm; width: 100%;" />
                    </p>
                    <p style="padding-bottom: 2mm;">
                        <div><span style="font-weight: bold;">Copies to:</span> Original to Licensee; Range Manager; Finance Department;</div>
                        <div>Notes:</div>
                    </p>

                </div>

                </div>
            </div>
        </body>';
        $mpdf->setTitle("List of Approved Requisitions");
        $mpdf->showWatermarkImage = false;
        $mpdf->setDisplayMode('fullpage');
        $mpdf->WriteHTML( $html );
        $mpdf->Output('output.pdf','F');

        $path = public_path('output.pdf');
        $data = file_get_contents($path);

        $base64 = base64_encode($data);
        return response()->json( [ "file" => $base64 ] );
        // return response()->download( $file, 'filename.pdf');

    }

    public function country_list(){
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        require_once $root. '/dms_backend/vendor/autoload.php';
        $file =  $root.'/dms_backend/documents/file1.pdf';

        $mpdf = new \Mpdf\Mpdf();

        $mpdf->WriteHTML('<div>Section 1 text</div>');

        $mpdf->Output('output.pdf','F');
    }

    public function getMaxVoucherId() {
        $sql = "select distinct id, voucher_id, updated_at from payment_requistion";

        $query = DB::select( DB::raw( $sql ) );
        return response()->json( $query, 200 );
    }

    public function generateDocument_2(Request $request) {
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        
        require_once $root. '/dms_backend/vendor/autoload.php';
        $file =  $root.'/dms_backend/documents/file1.pdf';

        $mpdf = new \Mpdf\Mpdf();

        $html ='<body style="height:100vh; width: 100vw;font-family: ubuntu;">
        <div style="border: 4px solid #2b3665;">
            <div style="padding-top: 5px;">
                <img src="usf_logo.jpeg" alt="USF Logo" style="object-fit: contain; height: 150px; margin-left: 300px; margin-right: 300px;" />
            </div>
            <div style="width: 100%;line-height: 18px;">
                <h4 style="color: #b23b2f; font-size: 20px;text-align: center;">UGANDA SWIMMING FEDERATION</h4>
                <h1 style="text-decoration: underline;text-align: center;color: #3d456b;font-family: industrial;font-weight: normal;font-size: 50px;">
                    MEMBERSHIP CERTIFICATE
                </h1>
            </div>

            <div style="line-height: 16px;">
                <p style="text-align: center;color: #44403a;font-size: 20px;">This is to certify that</p>
                <h3 style="font-weight: normal;text-align: center;color:#82724f;font-family: industrial;font-size: 48px;">
                    BLUE WHALES SWIM CLUB
                </h3>

                <p style="text-align: center;color: #44403a;font-size: 20px;">
                    is a duly registered Member of the
                </p>
                <p style="text-align: center;color: #44403a;font-size: 20px;">
                    Uganda Swimming Federation under
                </p>
                <h5 style="font-weight: normal;font-family: industrial;text-align: center;color: #82724f;font-size: 40px;">
                    Reg. No. USF 2016/0001
                </h5>

            </div>
            
            <div style="line-height: 24px;margin-left: 15px; margin-right: 15px;">
                <p style="color: #293669;font-size: 20px;font-weight: normal;">
                    This certificate is issued under seal this 4th day of January 2019 and shall remain valid
                    as long as its holder maintains its Membership with the Uganda Swimming Federation.
                </p>
            </div>


            <div style="margin-left: 15px; margin-right: 15px;margin-top:15px; margin-bottom: 15px;">
                <div style="float:left;width: 250px;height: 100px; padding-top: 50px;">
                    <div style="font-size: 16px;border-top: 2px solid #34385a; color: #34385a; font-weight: bold;">
                        Donald Rukare(Dr.)
                    </div>
                    <div style="color: #34385a;font-size: 16px;">PRESIDENT</div>
                </div>

                <div style="margin-left: 30px;float:left; outline: none;width: 100px; height: 100px;background-color: #5c4729;border-radius: 70px;">
                    <div style="color: #FFF;font-size: 14px; text-align: center;margin: 40;">
                        SEAL
                    </div>
                </div>

                <div style="float:left;width: 250px;height: 100px; padding-top: 50px; margin-left: 25px;">
                    <div style="font-size: 16px;border-top: 2px solid #34385a; color: #34385a; font-weight: bold;">
                        Moses Mwase
                    </div>
                    <div style="color: #34385a;font-size: 16px;">SECRETARY GENERAL</div>
                </div>
            </div>

            <div style="margin-left: 15px; margin-right: 15px;">
                <p style="color: #363d5a;">USF is a registered entity and is affliated to Fina, Cana, UOC, NCS</p>
            </div>

            <div>
                <p style="text-align: center;color: #e91e63; font-size: 16px; font-weight: bold;">S/N 002</p>
            </div>
        </div>
        
        </body>';
        // $mpdf->setTitle("National Forestry Authority License Document");
        // $mpdf->SetWatermarkImage( "'.$root.'/dms_backend/assets/logo.png'" );
        // $mpdf->showWatermarkImage = false;
        // $mpdf->setDisplayMode('fullpage');
        $mpdf->WriteHTML( $html );
        $mpdf->Output('output.pdf','F');

        $path = public_path('output.pdf');
        $data = file_get_contents( $path );

        $base64 = base64_encode($data);
        return response()->json( [ 
            "file" => $base64 
        ] );

    }

    public function downloadOneDayPass(Request $request) {
        require base_path().'/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['tempDir'=>storage_path('tempdir')]);

        $html ='<body style="height:100vh; width: 100vw;">
            <div style="border: 1px solid #2b3665;">
                Uganda Civil Aviation Authortiy
            </div>
        
        </body>';

        $mpdf->setTitle("Uganda Civil Aviation Authority");
        $mpdf->showWatermarkImage = false;
        $mpdf->setDisplayMode( 'fullpage' );
        // $mpdf->WriteHTML( $html );
        // $location = public_path().'/assets/';

        // $mpdf->Output( 'Membership_'.$request->id.'.pdf', "F" );

        // $filePath = public_path( ".pdf" );
        // $headers = [ "Content-Type: application/pdf" ];
        
        // return response()->json( [
        //     "success" => true,
        //     "message" => "File has been create and saved "
        // ] );

        $mpdf->WriteHTML( $html );
        $mpdf->Output('output.pdf','F');

        $path = public_path('output.pdf');
        $data = file_get_contents($path);

        $base64 = base64_encode($data);
        return response()->json( [ "file" => $base64 ] );
    }

    public function getAllMemoIds( Request $request ) {
        $results = DB::select("select max( uniqueId ) as id from memo");
        return response()->json( [
            "success" => true,
            "results" => $results
        ] );
    }

}
