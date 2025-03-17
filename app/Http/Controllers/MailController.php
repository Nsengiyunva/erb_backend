<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendmail(Request $request){
        $title = 'Sending an Email Notification';
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
}
