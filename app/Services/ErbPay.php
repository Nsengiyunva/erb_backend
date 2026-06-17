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
        $this->baseURL  = env("ERB_BASE");
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

    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return void
     * @throws PaymentException
     */
    public function pay(array $data)
    {
        // Normalize phone to local format 0XXXXXXXXX - FlexiPay rejects international format
        $phone = preg_replace('/\D/', '', $data['phone_no']);
        if (str_starts_with($phone, '256')) {
            $phone = '0' . substr($phone, 3);
        }
        $data['phone_no'] = $phone;

        // Use source_system from caller, default to MTN if not provided
        if (empty($data['source_system'])) {
            $data['source_system'] = 'MTN';
        }

        $data['payment_callback'] = env("APP_URL") . "/api/payments/callback";

        $headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];

        Log::info('ErbPay request payload: ', $data);

        $response = Http::withHeaders($headers)->post($this->baseURL . "/flexi/payments/", $data);
        $info     = $response->json();

        Log::info('ErbPay full response: ', $info ?? []);
        Log::info('ErbPay status code: ' . $response->status());

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
            Log::error('ErbPay validation failed: ', [
                'errors'   => $validator->errors()->toArray(),
                'response' => $info,
            ]);
            throw new PaymentException($validator->errors());
        }

        $this->payment->reference = $info["reference"];
        $this->payment->status    = config("payments.STATES.PENDING");
        $this->payment->amount    = $data["amount"];

        return $info["reference"]; 
    }

}
