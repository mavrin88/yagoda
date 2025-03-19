<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CallService
{
    private $serviceId;
    private $secretKey;

    public function __construct()
    {
        $this->serviceId = 449;
        $this->secretKey = 'f9572c00cd932f6511c31a4722db9e1e';
    }

    public function makeCall($clientPhone)
    {
        $response = Http::get('https://api.nerotech.ru/api/v1/call', [
            'service_id' => $this->serviceId,
            'secret_key' => $this->secretKey,
            'phone' => $clientPhone,
        ]);

        return $response->json();
    }

    public function checkCode($call_id, $code)
    {
        $response = Http::get('https://api.nerotech.ru/api/v1/checkCode', [
            'call_id' => $call_id,
            'secret_key' => $this->secretKey,
            'code' => $code,
        ]);

        return $response->json();
    }

}
