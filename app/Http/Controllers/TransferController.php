<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Transfer;
use App\Models\USFDocument;


class TransferController extends Controller {
    
    public function storeTransferApplication( Request $request ){
        $transfer = new Transfer;
        $transfer->name = $request->name;
        $transfer->athlete_type = $request->transfer_type;
        $transfer->duration = $request->duration;
        $transfer->effective_date = $request->effective_date;
        $transfer->current_role = $request->current_role;
        $transfer->position = $request->role_position;
        $transfer->usf_member = $request->usf_member;
        $transfer->current_membership = $request->current_membership;
        $transfer->application_status = $request->status;
        $transfer->email_address = $request->user_email;
        $transfer->application_status = $request->status;
 
        $transfer->save();
        
        foreach( $request->documents as $pdf ) {
            if( isset( $pdf ) ){
                $document = new USFDocument;
            
                $document->title = $pdf['title'];
                $document->referenceId = $transfer->id;
                $document->type = $pdf['type'];
                $document->details = $pdf['details'];
                $document->tag = $pdf['tag'];
                $document->size = $pdf['size'];
                $document->document = $pdf['file'];
                
                $document->save();
            }
        }
        
        return response()->json( [
            "success" => true,
            "message" => "Transfer Application has been added."
        ], 200 );
        
    }

    public function getTransferApplication( $id ) {
        $response = Transfer::find( $id );
        return response()->json( $response, 200 );
    }
    public function updateTransferApplication( Request $request ) {
        $transfer = Transfer::find( $request->id );
        $transfer->application_status = $request->status;
        $transfer->remarks = $request->remarks;
        $transfer->save();

        return response()->json( [
            "success" => true,
            "message" => "Transfer Application has been updated"
        ], 200 );
    }
    public function getAllTransfers(){
        $transfers = Transfer::all();
        return response()->json( $transfers, 200 );
    }
    public function fetchAttached( Request $request ){
        $records = DB::table("athletes")->where('athlete_type', '=', $request->type )->where('institution_id', '=', $request->institution_id )->get();
        
        return response()->json( [
            "success" => true,
            "records" => $records
        ], 200 );
    }

}
