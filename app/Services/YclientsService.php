<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class YclientsService
{
    private $baseUrl;
    private $login;
    private $password;

    private $token;

    public function __construct()
    {
        $this->baseUrl = Config::get('services.yclients.base_url');
        $this->login = Config::get('services.yclients.login');
        $this->password = Config::get('services.yclients.password');

        $this->authenticate();
    }

    public function authenticate()
    {
        $response = Http::post($this->baseUrl . '/auth', [
            'login' => $this->login,
            'password' => $this->password
        ]);

        dd($response->json());

//        if ($response->successful()) {
//            $this->token = $response->json('token');
//            return $response->json();
//        } else {
//            throw new \Exception('Authentication failed: ' . $response->status());
//        }
    }

    public function getStaff($companyId)
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . "/company/$companyId/staff");

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Failed to fetch staff: ' . $response->status());
        }
    }

    public function getStaffMember($companyId, $staffId)
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . "/company/$companyId/staff/$staffId");

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Failed to fetch staff member: ' . $response->status());
        }
    }
}
