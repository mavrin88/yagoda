<?php

namespace App\Console\Commands;

use App\Services\TochkaService;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class FetchData extends Command
{
    protected $signature = 'fetch:data';
    protected $description = 'Fetch data with signed request';
    protected $tochkaService;

    public function handle(TochkaService $tochkaService)
    {

        $this->tochkaService = $tochkaService;

        $headers = [
            "Content-Type" => "application/json"
        ];

        $datas = [
            "id" => "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc" => "2.0",
            "method" => "list_payments",
            "params" => [
                "filters" => [
                    "identify" => "false"
                ]
            ]
        ];

        $response = $this->tochkaService->fetch($headers, $datas);

        dd($response);

//        try {
//            $result = $this->tochkaService->fetch($headers, $datas);
//            return response()->json($result);
//        } catch (Exception $e) {
//            return response()->json([
//                'error' => $e->getMessage()
//            ], 500);
//        }

//        $headers = [
//            "Content-Type" => "application/json"
//        ];
//
//        $datas = [
//            "id" => "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
//            "jsonrpc" => "2.0",
//            "method" => "list_payments",
//            "params" => [
//                "filters" => [
//                    "identify" => "false"
//                ]
//            ]
//        ];
//
//        $result = $this->fetch($headers, $datas);
//        $this->info(json_encode($result, JSON_PRETTY_PRINT));
    }



    private function fetch(array $headers, array $datas)
    {
        // Загружаем приватный ключ
        $privateKey = file_get_contents(storage_path('keys/laravel_private_key.pem'));
        $privateKeyResource = openssl_pkey_get_private($privateKey);

        if ($privateKeyResource === false) {
            throw new \Exception('Invalid private key provided.');
        }

        // Сериализуем данные в JSON
        $datasJson = json_encode($datas);

        // Подписываем данные
        openssl_sign($datasJson, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        // Конвертируем подпись в base64
        $base64Signature = base64_encode($signature);
        $signData = str_replace(array("\r", "\n"), '', $base64Signature);


        $json = '{
            "id": "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc": "2.0",
            "method": "list_payments",
            "params": {
                "filters": {
                    "identify": false
                }
            }
        }';

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => $signData,
                'sign-thumbprint' => '21346a3bb3924c2530c8a52887d4cadc036b55b6',
                'sign-system' => 'yagoda',
                'Content-Type' => 'application/json',
            ],
            RequestOptions::BODY => $json,
        ]);

        if ($response->getStatusCode() === 200) {
            dd(json_decode($response->getBody(), true));
        } else {
            dd($response->getStatusCode(), $response->getBody());
        }



        // Выполняем запрос
        $response = Http::withHeaders(array_merge($headers, [
            "sign-data" => '12345',
            "sign-thumbprint" => "21346a3bb3924c2530c8a52887d4cadc036b55b6",
            "sign-system" => "yagoda"
        ]))->post("https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc", $datasJson);

        dd($response);

        return [
            "connection_validation" => $response->failed(),
            "response" => $response->json()
        ];
    }
}
