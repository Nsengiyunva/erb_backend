<?php 
//refactor

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
        $encCreds = base64_encode($this->username . ':' . $this->password);


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



    public function pay(array $data) {
    $paymentCallbackUrl = "/api/payments/callback";
    $paymentCallbackUrl = env("APP_URL") . $paymentCallbackUrl;
    $data["payment_callback"] = $paymentCallbackUrl;

    $headers = [
        'Authorization' => 'Bearer ' . $this->getAccessToken(),
    ];

    $response = Http::withHeaders($headers)->post($this->baseURL . "/flexi/payments/", $data);

    Log::info("ErbPay raw response: " . $response->body());
    Log::info("ErbPay payment object: " . json_encode($this->payment));

    $info = $response->json();

    if (!$info) {
        throw new Exception($response->body());
    }

    $validator = Validator::make($info, [
        "status" => [
            "required",
            "string",
            function ($attribute, $value, $fail) {
                if ($value !== 'initiated') {
                    $fail($attribute . ' must be initiated.');
                }
            },
        ],
        "reference" => "required"
    ]);

    if ($validator->fails()) {
        Log::error("ErbPay validator failed: " . json_encode($validator->errors()));
        throw new PaymentException($validator->errors());
    }

    Log::info("ErbPay validator passed - reference: " . $info["reference"]);

    $this->payment->reference = $info["reference"];
    $this->payment->status    = config("payments.STATES.PENDING");
    $this->payment->amount    = $data["amount"];
    $this->payment->save();

    Log::info("ErbPay payment saved - id: " . $this->payment->id . " reference: " . $this->payment->reference);

    return $this->payment;
  }

}
