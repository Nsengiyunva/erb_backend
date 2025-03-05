<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Payment::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = Payment::find($id);
        return $response;
    }

    public function fetchAllPayments() {
        $results = Payment::all();
        return $results;
    }

    public function callback(Request $request){
        $reference = $request->json( "reference" );
        $amount = $request->json( "amount" );
        $status = $request->json( "status" );
        $notes = $request->json("notes" );

        $payment = Payment::where("reference",$reference)->first();
        $payment->amount = $amount;

        switch( strtolower( $status ) ){
            case "completed":
                $payment->status = config("payments.STATES.COMPLETED");
                break;
            case "expired":
                $payment->status = config("payments.STATES.EXPIRED");
                break;
            default:
                $payment->status = config("payments.STATES.FAILED");
                $payment->notes = $notes;
                break;
        }
        $payment->save();
        return $payment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
