<?php

namespace App\Services;

use App\Models\TochkaAccess;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TochkaApiService
{
    protected $client;
    protected $apiVersion = 'v1.0';

    protected $chatIds = [
        '802964868',
        '272393976',
        '95113171'
    ];

    protected $telegram;

    public function __construct()
    {
        $this->telegram = new TelegramService();

        $this->client = new Client([
            'base_uri' => 'https://enter.tochka.com/uapi/acquiring/',
            'timeout'  => 5.0,
        ]);
    }

    public function createPayment($data)
    {
        try {
            $response = $this->client->post("{$this->apiVersion}/payments", [
                'json' => [
                    'Data' => $data,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getToken()
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->telegram->sendMessageToUsers($this->chatIds, 'Ошибка оплаты !!! ' . $e->getMessage());
            Log::info(dump($e->getMessage()));
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }

    public function getPaymentOperationList($data = ['customerCode' => '304590585'])
    {
        try {
            $response = $this->client->get("{$this->apiVersion}/payments", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getToken(),
                ],
                'query' => $data,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }

    public function getPaymentOperationInfo($operationId)
    {
        try {
            $response = $this->client->get("{$this->apiVersion}/payments/" . $operationId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getToken(),
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ];
        }
    }

    protected function getToken()
    {
        return $this->getAccessToken();
    }

    public function getAccessToken()
    {
        $tokenData = TochkaAccess::find(1);

        if ($tokenData && $tokenData->expires_at > now()) {
            return $tokenData->access_token;
        }

        return $this->refreshAccessToken($tokenData);
    }

    function refreshAccessToken($tokenData)
    {
        $response = Http::asForm()->post('https://enter.tochka.com/connect/token', [
            'client_id' => $tokenData->client_id,
            'client_secret' => $tokenData->client_secret,
            'grant_type' => $tokenData->grant_type,
            'refresh_token' => $tokenData->refresh_token,
        ]);

        if ($response->successful()) {

            $data = $response->json();

            $tokenData->update([
                'access_token' => $data['access_token'],
                'expires_at' => now()->addSeconds($data['expires_in']),
                'refresh_token' => $data['refresh_token'],
            ]);

            return $data['access_token'];
        }

        throw new \Exception("Не удалось обновить токен");
    }
}
