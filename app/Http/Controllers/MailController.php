<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendmail(Request $request){
        $title = 'Tree Planting and Licensing Application';
        $user_details = [
            'name' => $request->name,
            'content' => $request->content,
            'email' => $request->email
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
}
