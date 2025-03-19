<?php

namespace App\Services;

use GuzzleHttp\Client;

class PlusofonService
{
    private $client;
    private $apiUrl;
    private $clientId;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = 'https://restapi.plusofon.ru/api/v1/flash-call/send';
        $this->clientId = '10553';
        $this->apiKey = 'AKe303zz67WlFDMfYoMbFmbOsqttg34w';
    }

    public function sendFlashCall(string $phoneNumber): array
    {
        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client' => $this->clientId,
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'json' => [
                    'phone' => $phoneNumber,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
