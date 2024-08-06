<?php 
namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;


class OtherController extends Controller {

    public function index( Request $request ) {
        Mail::to( $request->email )->send( new OrderShipped() );
        return response([
            'status' => 'success',
            'code' => $request->email,
            'message' => "User successfully registered",
            'data' => "Test"
        ], 200 );
    }

}

