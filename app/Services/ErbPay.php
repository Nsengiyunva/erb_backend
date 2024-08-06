<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ErbPay
{

    private $username;
    private $password;
    private $baseURL;
    private $accessToken;
    private $payment;

    public function __construct()
    {

        $this->username = env("ERB_USERNAME");
        $this->password = env("ERB_PASSWORD");
        $this->baseURL = env("ERB_BASE");
    }

    /**
     * @return string
     */
    private function generateAccessToken()
    {
        // Encode the consumer key and consumer secret in base64
        $encCreds = base64_encode($this->username . ':' . $this->password);

        // Send the request to obtain the access token
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $encCreds,
        ])->post($this->baseURL . '/flexi/token/');

        return $response->json()['access_token'];
    }

    public function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }
        $this->accessToken = $this->generateAccessToken();
        return $this->accessToken;
    }

    public function setPayment(Payment $payment){
        $this->payment = $payment;
    }
    /**
     * @return Payment
     */
    public function pay(array $data)
    {
        $paymentCallbackUrl = "/api/payments/callback";
        $paymentCallbackUrl = env("APP_URL").$paymentCallbackUrl;
        $data["payment_callback"] = $paymentCallbackUrl;

        $headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];

        $response = Http::withHeaders($headers)->post($this->baseURL . "/flexi/payments/", $data);
        $info = $response->json();
        Log::info($info);
        $validator = Validator::make($info, [
            "status" => [
                "required",
                "string",
                function($attribute, $value, $fail) {
                    if ($value !== 'initiated') {
                        $fail($attribute . ' must be initiated.');
                    }
                },
            ],
            "reference" => "required"
        ]);
        
        if($validator->fails()){
            throw new PaymentException($validator->errors());
        }

        $this->payment->reference = $info["reference"];
        $this->payment->status = config("payments.STATES.PENDING");
        $this->payment->amount = $data["amount"];
    }

}
