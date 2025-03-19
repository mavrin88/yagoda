<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AtolService
{
    protected $login;
    protected $pass;

    public function __construct()
    {
        $this->login = config('atol.login');
        $this->pass = config('atol.password');
    }

    public function getToken()
    {
        $url = sprintf(
            'https://fiscalization.evotor.ru/possystem/v5/getToken?login=%s&pass=%s',
            urlencode($this->login),
            urlencode($this->pass)
        );

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Ошибка при получении токена: ' . $response->body());
        }
    }

    public function documentRegistrationAgents($data)
    {
        $totalAmount = 0;
        $items = [];

        $currentDateTime = Carbon::now();
        $formattedDateTime = $currentDateTime->format('d.m.Y H:i:s');
        $formattedDate = $currentDateTime->format('d.m.Y');

        foreach ($data['orderItems'] as $product) {

            $items[] = [
                "name" => $product['product_name'],
                "price" => $product['discounted_total'],
                "quantity" => $product['quantity'],
                "measure" => 0,
                "sum" => $product['discounted_total'],
                "payment_method" => "full_payment",
                "payment_object" => 1,
                "vat" => [
                    "type" => "none",
                ],
                "user_data" => "Дополнительный реквизит предмета расчета",
                "agent_info" => [
                    "type" => "attorney",
                ],
                "supplier_info" => [
                    "name" => $data['organization']['name'],
                    "inn" => $data['organization']['inn']
                ],
                "sectoral_item_props" => [
                    [
                        "date" => $formattedDate,
                        "value" => "tm=mdlp&sid=00000000405195&",
                        "number" => "123/43",
                        "federal_id" => "001"
                    ]
                ]
            ];
        }

        $array = [
            "timestamp" => $formattedDateTime,
            "external_id" => Str::uuid()->toString(),
            "receipt" => [
                "client" => [
                    "email" => $data['emailReceipt']
                ],
                "company" => [
                    "email" => "email@evotor.ru",
                    "sno" => "usn_income",
                    "inn" => "0800024353",
                    "payment_address" => "yagoda.team",
                ],
                "cashier" => "Автоматическое устройство",
                "items" => $items,
                "payments" => [
                    [
                        "type" => 1,
                        "sum" => $data['orderTotal']
                    ]
                ],
                "sectoral_check_props" => [
                    [
                        "date" => $formattedDate,
                        "value" => "tm=mdlp&sid=00752852194630&",
                        "number" => "123/89",
                        "federal_id" => "002"
                    ]
                ],
                "total" => $totalAmount,
                "additional_user_props" => [
                    "name" => "название доп реквизита",
                    "value" => "значение доп реквизита"
                ]
            ]
        ];

        $groupCode = 'bbd44068-724b-4a33-b720-8c0ff47d5622';
        $operation = 'sell';
        $token = $this->getToken()['token'];

        $url = "https://fiscalization.evotor.ru/possystem/v5/{$groupCode}/{$operation}?token={$token}";

        try {
            $response = Http::post($url, $array);

            Log::info($response->json());

            if ($response->successful()) {
                return response()->json(['success' => true, 'data' => $response->json()]);
            } else {
                return response()->json(['success' => false, 'error' => $response->body()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function documentRegistrationMy($data)
    {
        $items = [];

        $currentDateTime = Carbon::now();
        $formattedDateTime = $currentDateTime->format('d.m.Y H:i:s');

        foreach ($data['orderItems'] as $product) {

            $items[] = [
                "name" => $product['product_name'],
                "price" => $product['discounted_total'],
                "quantity" => $product['quantity'],
                "sum" => $product['discounted_total'],
                "payment_method" => "full_payment",
                "payment_object" => "commodity",
                "vat" => [
                    "type" => "none",
                ],
            ];
        }

        $arrayVar = [
            "receipt" => [
                "items" => $items,
                "total" => $data['orderTotal'],
                "client" => [
                    "email" => $data['emailReceipt']
                ],
                "company" => [
                    "inn" => "0800024353",
                    "sno" => "usn_income",
                    "email" => "igor.fee@gmail.com",
                    "payment_address" => "https://yagoda.team",
                ],
                "payments" => [["sum" => $product['discounted_total'], "type" => 1]],
            ],
            "timestamp" => $formattedDateTime,
            "external_id" => Str::uuid()->toString(),
        ];

        $groupCode = 'c9cbdf8c-bf9f-4930-8d0e-1578d3d55eef';
        $operation = 'sell';
        $token = $this->getToken()['token'];

        $url = "https://fiscalization.evotor.ru/possystem/v4/{$groupCode}/{$operation}?token={$token}";

        try {
            $response = Http::post($url, $arrayVar);

            Log::info($response->json());

            if ($response->successful()) {
                return response()->json(['success' => true, 'data' => $response->json()]);
            } else {
                return response()->json(['success' => false, 'error' => $response->body()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getDocument($uuid)
    {
        $groupCode = 'c9cbdf8c-bf9f-4930-8d0e-1578d3d55eef';

        $token = $this->getToken()['token'];

        $url = "https://fiscalization.evotor.ru/possystem/v5/{$groupCode}/report/{$uuid}?token={$token}";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                return response()->json(['success' => true, 'data' => $response->json()]);
            } else {
                return response()->json(['success' => false, 'error' => $response->body()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
