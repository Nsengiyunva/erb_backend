<?php

namespace App\Exceptions;

use Exception;

class PaymentException extends Exception
{
    public function __construct(string $message = "Payment failed", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage()
        ], 422);
    }
}