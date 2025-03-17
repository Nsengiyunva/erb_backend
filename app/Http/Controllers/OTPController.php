<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
// use App\Mail\OTPMail;
use App\Models\ELicenceUser;
use Illuminate\Support\Facades\Cache;

class OTPController extends Controller
{
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache for 5 minutes
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

        // Send email
        Mail::to($request->email)->send(new OTPMail($otp));
        // $sendmail = Mail::to($request->email)->send(
        //     new SendMail($title,$user_details)
        // );

        return response()->json(['message' => 'OTP sent successfully'], 200);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|integer'
        ]);

        $storedOTP = Cache::get('otp_' . $request->email);

        if ($storedOTP && $storedOTP == $request->otp) {
            // OTP is valid
            Cache::forget('otp_' . $request->email);
            return response()->json(['message' => 'OTP verified successfully'], 200);
        } else {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }
    }
}
