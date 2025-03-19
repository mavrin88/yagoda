<?php

namespace App\Console\Commands;

use App\Services\EncryptionCardNumber;
use App\Services\TochkaService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;

class TransferMoneyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $tochkaService;

    /**
     * Execute the console command.
     */

    public function handle(TochkaService $tochkaService, EncryptionCardNumber $encryptionCardNumber)
    {
        $this->encryptionCardNumber = $encryptionCardNumber;

        $payment_id = "tender-helpers-76f1ab6085174824a01a9fb1d90a9330";
        $beneficiary_id = 'c37183a9-5d2d-4514-8cbc-bfdccba978cf';
        $virtual_account = "bcb3cf18-af5c-43f4-8f9c-d114851a930f";
        $deal_id = "9fb298bd-20e2-4a39-91b2-03a8a96e2e48";


        $this->tochkaService = $tochkaService;

//        $this->transferMoney();
//        $this->listPayments();
//        $this->getPayment($payment_id);
//        $this->createBeneficiaryUl("5027206537", "40702810620000087431", "044525104", "ООО \"Рога и Копыта\"", "246301001");
//        $this->listBeneficiarySearchToINN("5027206537");
//        $this->uploadDocument($beneficiary_id);
//        $this->tryUploadDocument();
//        $this->getDocument();
//        $this->createVirtualAccount('c37183a9-5d2d-4514-8cbc-bfdccba978cf');
//        $this->identificationPayment($payment_id, $virtual_account, 1000);
//        $this->createDeal($virtual_account);
//        $this->uploadDocumentDeal($beneficiary_id, $deal_id);
//        $this->executeDeal($deal_id);
//        $this->getDeal($deal_id);


//        $this->point13();
//        $this->point14();
//        $this->point15();
//        $this->point16();
//        $this->send();
//        $this->listBeneficiary();
    }

    public function runScript($card)
    {
        $scriptPath = public_path('encrypt.py');

        return shell_exec("python3 $scriptPath " . escapeshellarg($card) . " 2>&1");
    }

    private function transferMoney()
    {
        $json2 = '{
            "id": "108e999e-7da6-43ae-8eec-557b341d18d8",
            "jsonrpc": "2.0",
            "method": "transfer_money",
            "params": {
                "recipient_account": "40702810620000087431",
                "recipient_bank_code": "044525104",
                "amount": 1000,
                "purpose": "Оплата по договору 12345"
            }
        }';

        $headers = [
            "Content-Type" => "application/json"
        ];

        $datas = [
            "id" => "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc" => "2.0",
            "method" => "transfer_money",
            "params" => [
                "recipient_account" => "40702810620000087431",
                "recipient_bank_code" => "044525104",
                "amount" => "1000",
                "purpose" => "Оплата по договору 12345",
            ]
        ];

//        $json2 = json_encode($datas);

//        $response = $this->tochkaService->fetch($headers, $datas);

//        dd($json2);

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/tender-helpers/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
                'sign-thumbprint' => '21346a3bb3924c2530c8a52887d4cadc036b55b6',
                'sign-system' => 'yagoda',
                'Content-Type' => 'application/json',
            ],
            RequestOptions::BODY => $json2,
        ]);

        if ($response->getStatusCode() === 200) {
            dd(json_decode($response->getBody(), true));
        } else {
            dd($response->getStatusCode(), $response->getBody());
        }
    }

    private function listPayments()
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $datas = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "list_payments",
            "params" => [
                "filters" => [
                    "identify" => "false"
                ]
            ]
        ];

        $json2 = json_encode($datas);

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
                'sign-thumbprint' => '21346a3bb3924c2530c8a52887d4cadc036b55b6',
                'sign-system' => 'yagoda',
                'Content-Type' => 'application/json',
            ],
            RequestOptions::BODY => $json2,
        ]);

        if ($response->getStatusCode() === 200) {
            dd(json_decode($response->getBody(), true));
        } else {
            dd($response->getStatusCode(), $response->getBody());
        }
    }

    private function getPayment($payment_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $datas = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "get_payment",
            "params" => [
                "payment_id" => $payment_id
            ]
        ];

        $json = json_encode($datas);

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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

        $response = $this->tochkaService->fetch($headers, $datas);

        dd($response);
    }

    private function createBeneficiaryUl($inn, $nominal_account_code, $nominal_account_bic, $name, $kpp)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];


        $datas = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "create_beneficiary_ul",
            "params" => [
                "inn" => "7731483420",
                "nominal_account_code" => "40702810620000087431",
                "nominal_account_bic" => "044525104",
            ],
            "beneficiary_data" => [
                "name" => "6666",
                "kpp" => "773101001"
            ]
        ];

        $json = '{
            "id": "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc": "2.0",
            "method": "create_beneficiary_ul",
            "params": {
                "inn": "7731483420",
                "nominal_account_code": "40702810620000087431",
                "nominal_account_bic": "044525104",
                "beneficiary_data": {
                    "name": "ЦЕНТРАЛЬНЫЙ",
                    "kpp": "773101001"
                }
            }
        }';

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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
    }

    public function listBeneficiarySearchToINN($inn)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $datas = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "list_beneficiary",
            "params" => [
                "filters" => [
                    "inn" => $inn
                ]
            ]
        ];

        $json = json_encode($datas);

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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

        $response = $this->tochkaService->fetch($headers, $datas);

        dd($response);
    }

    public function uploadDocument($beneficiaryId)
    {
        $client = new Client();

        $url = "https://pre.tochka.com/api/v1/cyclops/upload_document/beneficiary";

        $documentData = [
            'document_type' => 'contract_offer',
            'document_date' => '2020-01-01',
            'document_number' => '1101',
        ];

        $array = [
            'beneficiary_id' => $beneficiaryId,
            'document_type' => $documentData['document_type'],
            'document_date' => $documentData['document_date'],
            'document_number' => $documentData['document_number'],
        ];

        // Путь к файлу
        $filePath = public_path('DraftPay-173-2024-10-21.pdf');

        $privateKey = file_get_contents(base_path('rsaprivkey.pem'));
        $privateKeyResource = openssl_pkey_get_private($privateKey);

        $datas = json_encode($array);

        openssl_sign($datas, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        $signData = str_replace(array("\r", "\n"), '', base64_encode($signature));

        // Подготовка заголовков
        $headers = [
            'sign-data' => '12345',
            'sign-thumbprint' => '6f1ff645ae3cba4143b151d6b6693b3f8636969e',
            'sign-system' => 'yagoda',
            'Content-Type' => 'application/pdf',
        ];

        try {
            // Выполнение запроса
            $response = $client->post($url, [
                'query' => $array,
                'headers' => $headers,
                'body' => fopen($filePath, 'r')
            ]);

            if ($response->getStatusCode() === 200) {
                dd(json_decode($response->getBody(), true));
            } else {
                dd($response->getStatusCode(), $response->getBody());
            }

            // Получение ответа
            $responseBody = $response->getBody()->getContents();

            return response()->json(['status' => 'success', 'data' => json_decode($responseBody)], 200);

        } catch (RequestException $e) {
            // Обработка ошибок
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function getDocument()
    {
        $json = '{
            "id": "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc": "2.0",
            "method": "get_document",
            "params": {
                "document_id": "cyclops-241023061507346-b5b7f7ac-2feb-4f88-977a-cc75cbf2b32e"
            }
        }';

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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
    }

    private function createVirtualAccount($beneficiary_id)
    {
        $datas = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "create_virtual_account",
            "params" => [
                "beneficiary_id" => $beneficiary_id
            ]
        ];

        $this->sendResponse($datas);
    }

    private function identificationPayment($payment_id, $virtual_account, $amount)
    {
        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "identification_payment",
            "params" => [
                "payment_id" => $payment_id,
                "owners" => [
                    [
                        'virtual_account' => $virtual_account,
                        'amount' => $amount,
                    ]
                ]
            ]
        ];

        $this->sendResponse($data);
    }

    private function createDeal($virtual_account)
    {
        $card = $this->runScript('2201382000000021');

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "create_deal",
            "params" => [
                "amount" => 1000,
                "payers" => [
                    [
                        "virtual_account" => $virtual_account,
                        "amount" => 1000
                    ]
                ],
                "recipients" => [
                    [
                        "number" => 3,
                        "type" => "payment_contract_to_card",
                        "amount" =>1000,
                        "card_number_crypto_base64" => $card,
                        "recipient_fio" => [
                            "first_name" => "Иванов",
                            "last_name" => "Иван",
                            "middle_name" => "Иванович",
                        ]
                    ]
                ]
            ]
        ];

        $this->sendResponse($data);
    }

    public function uploadDocumentDeal($beneficiaryId, $dealId)
    {
        $client = new Client();

        $url = "https://pre.tochka.com/api/v1/cyclops/upload_document/deal";

        $documentData = [
            'document_type' => 'contract_offer',
            'document_date' => '2020-01-01',
            'document_number' => '1101',
        ];

        // Подготовка заголовков
        $headers = [
            'sign-data' => '12345',
            'sign-thumbprint' => '21346a3bb3924c2530c8a52887d4cadc036b55b6',
            'sign-system' => 'yagoda',
            'Content-Type' => 'application/pdf',
        ];

        // Путь к файлу
        $filePath = public_path('DraftPay-173-2024-10-21.pdf');

        try {
            // Выполнение запроса
            $response = $client->post($url, [
                'query' => [
                    'beneficiary_id' => $beneficiaryId,
                    'deal_id' => $dealId,
                    'document_type' => $documentData['document_type'],
                    'document_date' => $documentData['document_date'],
                    'document_number' => $documentData['document_number'],
                ],
                'headers' => $headers,
                'body' => fopen($filePath, 'r')
            ]);

            if ($response->getStatusCode() === 200) {
                dd(json_decode($response->getBody(), true));
            } else {
                dd($response->getStatusCode(), $response->getBody());
            }

        } catch (RequestException $e) {
            // Обработка ошибок
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function executeDeal($deal_id)
    {
        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "execute_deal",
            "params" => [
                "deal_id" => $deal_id
            ]
        ];

        $this->sendResponse($data);
    }

    private function getDeal($deal_id)
    {
        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "get_deal",
            "params" => [
                "deal_id" => $deal_id
            ]
        ];

        $this->sendResponse($data);
    }

    private function point13()
    {
        $json = '{
            "id": "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc": "2.0",
            "method": "deactivate_beneficiary",
            "params": {
                "beneficiary_id": "c37183a9-5d2d-4514-8cbc-bfdccba978cf"
            }
        }';

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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
    }

    private function point14()
    {
        $json = '{
            "id": "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc": "2.0",
            "method": "activate_beneficiary",
            "params": {
                "beneficiary_id": "36458a29-7360-4df1-94df-2885fbc6627c"
            }
        }';

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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
    }

    private function point15()
    {
        $json = '{
            "id": "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc": "2.0",
            "method": "rejected_deal",
            "params": {
                "deal_id": "017d67e7-f6c5-48d5-bee9-8e86ce78deff"
            }
        }';

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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
    }

    private function point16()
    {
        $json = '{
            "id": "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc": "2.0",
            "method": "update_deal",
            "params": {
                "deal_id": "b665c4b6-4f13-46ca-bcd2-751ed2cc16e3",
                "deal_data": {
                    "amount": 100,
                    "payers":[{
                        "virtual_account": "99e2cee0-1953-4a0e-abdb-a2a607ac117b",
                        "amount": 100
                    }],
                    "recipients":[
                        {
                            "number": 1,
                            "type": "commission",
                            "amount": 10
                        },
                        {
                            "number": 2,
                            "type": "payment_contract",
                            "amount": 90,
                            "account": "40702810901500016731",
                            "bank_code": "044525999",
                            "name": "ООО \"БУКАШКА\"",
                            "inn": "6950211138",
                            "kpp": "695001001",
                            "document_number": "1",
                            "identifier": "shipment_worker1338"
                        }
                    ]
                }
            }
        }';

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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
    }



//    public function handle()
//    {
//        $url = 'https://stage.tochka.com/api/v1/tender-helpers/jsonrpc';
//
//        $data = [
//            "id" => "108e999e-7da6-43ae-8eec-557b341d18d8",
//            "jsonrpc" => "2.0",
//            "method" => "transfer_money",
//            "params" => [
//                "recipient_account" => "40702810713500000456",
//                "recipient_bank_code" => "044525104",
//                "amount" => 1000,
//                "purpose" => "Оплата по договору 12345"
//            ]
//        ];
//
//        // Вычисляем заголовки
//        $signData = '12345';
//        $thumbprint = '<ваш отпечаток>';
//        $systemName = '<ваше наименование площадки>';
//
//        // Выполняем POST запрос
//        $response = Http::withHeaders([
//            'Content-Type' => 'application/json',
//            'sign-data' => $signData,
//            'sign-thumbprint' => $thumbprint,
//            'sign-system' => $systemName,
//        ])->post($url, $data);
//
//        dd($response->json());
//    }

//    private function listPayments()
//    {
//        $url = 'https://stage.tochka.com/api/v1/cyclops/v2/jsonrpc';
//
//        $data = [
//            "filters" => [
//                "identify" => false,
//            ]
//        ];
//
//        $signData = '12345';
//        $thumbprint = '<ваш отпечаток>';
//        $systemName = '<ваше наименование площадки>';
//
//
//        $response = Http::withHeaders([
//            'Content-Type' => 'application/json',
//            'sign-data' => $signData,
//            'sign-thumbprint' => $thumbprint,
//            'sign-system' => $systemName,
//        ])->post($url, $data);
//
//        dd($response);
//
//    }

    public function send()
    {
        // Извлечение заголовков и данных из запроса
        $headers = [
            "Content-Type" => "application/json"
        ];

        $datas = [
            "id" => "0d6a26ea-84f0-4be2-9999-b46edc9b59b6",
            "jsonrpc" => "2.0",
            "method" => "list_payments",
            "params" => [
                "filters" => [
                    "identify" => false
                ]
            ]
        ];

        // Загрузка приватного и публичного ключей
        $privateKey = file_get_contents(storage_path('keys/laravel_private_key.pem'));
        $publicKey = file_get_contents(storage_path('keys/rsaacert.pem'));

        // Подписание данных
        $signature = $this->signData($datas, $privateKey);

        // Получение отпечатка публичного ключа
        $thumbprint = $this->getThumbprint($publicKey);

        // Формирование заголовков
        $headers = array_merge($headers, [
            "sign-data: $signature",
            "sign-thumbprint: 21346a3bb3924c2530c8a52887d4cadc036b55b6",
            "sign-system: yagoda"
        ]);

        // Отправка запроса
        $response = $this->fetchData($headers, $datas);

        // Возврат ответа
        return response()->json($response);
    }

    private function signData($data, $privateKey)
    {
        // Сериализуем данные в JSON
        $jsonData = json_encode($data);

        // Подписываем данные
        openssl_sign($jsonData, $signature, openssl_pkey_get_private($privateKey), OPENSSL_ALGO_SHA256);

        // Кодируем подпись в base64 и удаляем переносы строк
        return str_replace(array("\r", "\n"), '', base64_encode($signature));
    }

    private function getThumbprint($publicKey)
    {
        return base64_encode(hash('sha256', openssl_pkey_get_details(openssl_pkey_get_public($publicKey))['key'], true));
    }

    private function fetchData($headers, $data)
    {
        try {
            // Выполнение запроса с использованием Http клиента Laravel
            $response = Http::withHeaders($headers)
                ->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', json_encode($data));

            $data = [
                'connection_validation' => $response->successful(),
                'response' => $response->json()
            ];

            dd($data);

//            dd($headers);
        } catch (\Exception $e) {
            // Логируем исключение
            \Log::error('Ошибка при выполнении запроса: ' . $e->getMessage());
            return [
                'connection_validation' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 5. Затем нужно загрузить документ по бенефициару: upload_document/beneficiary
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tryUploadDocument()
    {
        $client = new Client();

        $filePath = public_path('DraftPay-173-2024-10-21.pdf');
        $fileBody = file_get_contents($filePath);

        $urlParams = [
            'beneficiary_id' => 'a230f545-76f3-46bc-b139-0d5565fa4db3',
            'document_type' => 'contract_offer',
            'document_date' => '2024-05-31',
            'document_number' => '123',
        ];

        $signData = $this->tochkaService->signData($fileBody);

        $headers = [
            'sign-system' => 'yagoda',
            'sign-thumbprint' => '6f1ff645ae3cba4143b151d6b6693b3f8636969e',
            'Content-Type' => 'text/xml',
            'sign-data' => $signData,
        ];

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/upload_document/beneficiary', [
            'headers' => $headers,
            'query' => $urlParams,
            'body' => $fileBody,
        ]);

        dd(json_decode($response->getBody(), true));
    }

    public function listBeneficiary()
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $datas = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "list_beneficiary",
            "params" => [
                "filters" => [
                    "is_active" => "true"
                ]
            ]
        ];

        $response = $this->tochkaService->fetch($headers, $datas);

        dd($response);
    }

    private function sendResponse($datas)
    {
        $json = json_encode($datas);

        $client = new Client();

        $response = $client->post('https://pre.tochka.com/api/v1/cyclops/v2/jsonrpc', [
            'headers' => [
                'sign-data' => '12345',
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

        $response = $this->tochkaService->fetch($headers, $datas);

        dd($response);
    }

}
