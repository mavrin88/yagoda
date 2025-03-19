<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class VbrrService
{
    private $url = 'https://vbrr.rbsuat.com/payment/rest/register.do';
    private $paymentOrder = 'https://vbrr.rbsuat.com/payment/rest/paymentorder.do';
    private $urlQrCodeSBP = 'https://vbrr.rbsuat.com/payment/rest/sbp/c2b/qr/dynamic/get.do';
    private $urlP2P = 'https://vbrr.rbsuat.com/payment/rest/api/p2p/registerP2P.do';
    private $clientId = '259753456';
    private $language = 'en';
    private $username;
    private $password;
    private $returnUrl;
    private $failUrl;

    public function __construct()
    {
        $this->username = Config::get('services.vbrr.username');
        $this->password = Config::get('services.vbrr.password');
        $this->returnUrl = Config::get('services.vbrr.return_url');
        $this->failUrl = Config::get('services.vbrr.fail_url');
    }

    public function registerPayment($data)
    {
        $order = [
            'amount' => $data['amount'],
            'userName' => $this->username,
            'password' => $this->password,
            'orderNumber' => $data['orderNumber'],
            'returnUrl' => $this->returnUrl,
            'failUrl' => $this->failUrl,
            'clientId' => $this->clientId,
            'language' => $this->language,
            'orderBundle' => [
                'cartItems' => [
                    'items' => $data['orderItems']
                ]
            ],
            'language' => 'ru'
        ];

        $order['orderBundle'] = json_encode($order['orderBundle']);

        $response = Http::asForm()->post($this->url, $order);

        return $response->json();
    }

    public function registerP2P()
    {
        $payload = [
            'username' => 'RateWell-api',
            'password' => 'RateWell',
            'orderNumber' => '313',
            'amount' => 9500,
            'returnUrl' => 'https://mail.ru/',
            'failUrl' => 'https://ya.ru/',
            'language' => 'ru',
            'currency' => 643,
            'features' => [
                'feature' => [
                    'WITHOUT_FROM_CARD'
                ]
            ],
            'transactionTypeIndicator' => 'A'
        ];

        $response = Http::asForm()->post($this->urlP2P, $payload);

        dd($payload);




//        try {
//            $response = $client->post('https://vbrr.rbsuat.com/payment/rest/api/p2p/registerP2P.do', [
//                'json' => $payload
//            ]);
//
//            dd($response->getBody());
//
//            $data = json_decode($response->getBody(), true);
//
//            dd(response()->json($data, $response->getStatusCode()));
//        } catch (GuzzleException $e) {
//            dd(response()->json(['error' => $e->getMessage()], 400));
//        }
    }
}
