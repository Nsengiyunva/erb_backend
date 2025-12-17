<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveApplicationController extends Controller
{
    public function store(Request $request) {
        $application = new LeaveApplication;
        $application->date = $request->date;
        $application->staff_id = $request->staff_id;
        $application->staff_name = $request->staff_name;
        $application->designation = $request->designation;
        $application->salary_grade = $request->salary_grade;
        $application->leave_type = $request->leave_type;
        $application->leave_rate = $request->leave_rate;
        $application->number_of_days = $request->number_of_days;
        $application->fromdate = $request->fromdate;
        $application->todate = $request->todate;
        $application->address_leave = $request->address_leave; 
         
        $application->leave_due_year = $request->leave_due_year;
        $application->leave_days_applied = $request->leave_days_applied;
        $application->less_leave_days = $request->less_leave_days;
        $application->recommender = $request->recommender;
        $application->hr_date = $request->hr_date;
        $application->leave_allowance = $request->leave_allowance;
        $application->monthly_pay = $request->monthly_pay;
        $application->status = $request->status;
        $application->md_date = $request->md_date;
        $application->organisation = $request->organisation;
        $application->email_address = $request->email_address;

        $application->save();
        if( $application->id ){
            return response()->json([
                'success' => true,
                'data' => $application
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => []
        ]);
        
    }
    public function updateRecord(Request $request){
        $record = LeaveApplication::where('id', $request->id)->first();

        $record->leave_due_year = $request->input('leave_due_year');
        $record->leave_days_applied = $request->input('leave_days_applied');
        $record->less_leave_days = $request->input('less_leave_days');
        $record->recommender = $request->input('recommender');
        $record->hr_date = $request->input('hr_date');
        $record->leave_allowance = $request->input('leave_allowance');
        $record->monthly_pay = $request->input('monthly_pay');
        $record->status = $request->input('status');

        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function getAll(){
        $records = DB::select("SELECT * FROM leave_application");
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
    public function updateLeave(Request $request){
        $record = LeaveApplication::where('id', $request->id)->first();
        $record->status = $request->input('status');
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }

}
