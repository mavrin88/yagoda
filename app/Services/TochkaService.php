<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;

class TochkaService
{
    protected $url = "https://api.tochka.com/api/v1/cyclops/v2/jsonrpc";
    protected $client;
    private $signThumbprint = "84822aa5c89febee70edf5607c4b625e404f441c";
    private $signSystem = "yagoda2";

    private $recipient_account = "40702810020000169454";
    private $recipient_bank_code = "044525104";

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     *
     * Поплняем счёт
     *
     * @throws Exception
     */
    public function transferMoney()
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "transfer_money",
            "params" => [
                "recipient_account" => $this->recipient_account,
                "recipient_bank_code" => $this->recipient_bank_code,
                "amount" => "1000",
                "purpose" => "Оплата по договору 12345",
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Проверка наличия неидентифицированных денег на н/с
     *
     * @throws Exception
     */
    public function listPayments()
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "list_payments",
            "params" => [
                "filters" => [
                    "identify" => "false"
                ]
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Получение информации о конкретном платеже
     *
     * @throws Exception
     */
    public function getPayment($payment_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "get_payment",
            "params" => [
                "payment_id" => $payment_id
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Ищем бенефициара по ИНН
     *
     * @throws Exception
     */
    public function listBeneficiaryUl()
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
                    "legal_type" => "I"
                ]
            ]
        ];

        return $this->fetch($headers, $datas);
    }

    /**
     *
     * Ищем бенефициара по ИНН
     *
     * @throws Exception
     */
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

        return $this->fetch($headers, $datas);
    }

    /**
     *
     * Активация бенефициара
     *
     * @throws Exception
     */
    public function activateBeneficiary($beneficiary_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "activate_beneficiary",
            "params" => [
                "beneficiary_id" => $beneficiary_id
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Список виртуальных счетов
     *
     * @throws Exception
     */
    public function listVirtualAccount($beneficiary_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "list_virtual_account",
            "params" => [
                "filters" => [
                    "beneficiary" => [
                        "id" => $beneficiary_id
                    ]
                ]
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Информация по виртуальному счёту
     *
     * @throws Exception
     */
    public function getVirtualAccount($virtual_account)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "get_virtual_account",
            "params" => [
                "virtual_account" => $virtual_account
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Идентификация платежей
     *
     * @throws Exception
     */
    public function identificationPayment($identifyPayment)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "identification_payment",
            "params" => [
                "payment_id" => $identifyPayment['payment_id'],
                "owners" => $identifyPayment['owners']
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Возврат неидентифицированных платежей и идентифицированных платежей по СБП.
     *
     * @throws Exception
     */
    public function returnIdentificationPaymentSBP($payment_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "refund_payment",
            "params" => [
                "payment_id" => $payment_id,
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Создание сделки
     *
     * @throws Exception
     */
    public function createDeal($deal)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "create_deal",
            "params" => [
                "ext_key" => $deal['ext_key'],
                "amount" => $deal['amount'],
                "payers" => $deal['payers'],
                "recipients" => $deal['recipients']
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Исполнение сделки
     *
     * @throws Exception
     */
    public function executeDeal($deal_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "execute_deal",
            "params" => [
                "deal_id" => $deal_id
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Информация по сделке
     *
     * @throws Exception
     */
    public function getDeal($deal_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "get_deal",
            "params" => [
                "deal_id" => $deal_id
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Отмена сделки
     *
     * @throws Exception
     */
    public function rejectedDeal($deal_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "rejected_deal",
            "params" => [
                "deal_id" => $deal_id
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Информация по сделке
     *
     * @throws Exception
     */
    public function listDeal()
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "list_deal",
            "params" => [
                "filters" => [
                    "created_date_from" => "2024-11-14",
                    "created_date_to" => "2024-11-14",
                ]
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Список сделок новая
     *
     * @throws Exception
     */
    public function listDeals($status = 'new')
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "list_deals",
            "params" => [
                "filters" => [
                    "status" => $status,
                ]
            ]
        ];

        return $this->fetch($headers, $data);
    }

    /**
     *
     * Обновление сделки
     *
     * @throws Exception
     */
    public function updateDeal($params)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "update_deal",
            "params" => $params
        ];

        return $this->fetch($headers, $data);
    }

    public function uploadDocumentDeal($beneficiary_id, $deal_id, $documentPath, $document_date, $document_number)
    {
        $filePath = storage_path('app/documents/' . $documentPath);
        $fileBody = file_get_contents($filePath);

        $urlParams = [
            'beneficiary_id' => $beneficiary_id,
            'deal_id' => $deal_id,
            'document_type' => 'service_agreement',
            'document_date' => $document_date ?? '2025-01-21',
            'document_number' => $document_number ?? '3',
        ];

        $signData = $this->signData($fileBody);

        $headers = [
            'sign-system' => $this->signSystem,
            'sign-thumbprint' => $this->signThumbprint,
            'Content-Type' => 'application/pdf',
            'sign-data' => $signData,
        ];

        try {
            $response = $this->client->post('https://api.tochka.com/api/v1/cyclops/upload_document/deal', [
                'headers' =>$headers,
                'query' => $urlParams,
                'body' => $fileBody,
                'verify' => false,
            ]);

            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true),
            ];

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;

            return [
                "connection_validation" => $statusCode,
                "response" => $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : null,
            ];
        }
    }

    /**
     *
     * Информация по документу
     *
     * @throws Exception
     */
    public function getDocument($document_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "get_document",
            "params" => [
                "document_id" => $document_id
            ]
        ];

        return $this->fetch($headers, $data);
    }

    public function createBeneficiaryUl($organization)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "create_beneficiary_ul",
            "params" => [
                "inn" => $organization->inn,
                "beneficiary_data" => [
                    "name" => $organization->full_name,
                    "kpp" => $organization->kpp
                ]
            ],
        ];

        return $this->fetch($headers, $data);
    }

    public function createBeneficiaryIP($organization)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "create_beneficiary_ip",
            "params" => [
                "inn" => $organization->inn,
                "beneficiary_data" => [
                    "first_name" => 'Язев',
                    "middle_name" => 'Игорь',
                    "last_name" => 'Викторович'
                ]
            ]
        ];

        return $this->fetch($headers, $data);
    }

    public function uploadDocumentBeneficiary($beneficiary_id)
    {
        $today = Carbon::now();
        $filePath = public_path('user_agreement.pdf');
        $fileBody = file_get_contents($filePath);

        $urlParams = [
            'beneficiary_id' => $beneficiary_id,
            'document_type' => 'contract_offer',
            'document_date' => $today->toDateString(),
            'document_number' => 'Договор оферты',
        ];

        $signData = $this->signData($fileBody);

        $headers = [
            'sign-system' => $this->signSystem,
            'sign-thumbprint' => $this->signThumbprint,
            'Content-Type' => 'application/pdf',
            'sign-data' => $signData,
        ];

        try {
            $response = $this->client->post('https://api.tochka.com/api/v1/cyclops/upload_document/beneficiary', [
                'headers' =>$headers,
                'query' => $urlParams,
                'body' => $fileBody,
                'verify' => false,
            ]);

            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true),
            ];

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;

            return [
                "connection_validation" => $statusCode,
                "response" => $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : null,
            ];
        }
    }

    /**
     *
     * Создание виртуального счёта
     *
     * @return array
     */
    public function createVirtualAccount($beneficiary_id)
    {
        $headers = [
            "Content-Type" => "application/json"
        ];

        $data = [
            "id" => Str::uuid()->toString(),
            "jsonrpc" => "2.0",
            "method" => "create_virtual_account",
            "params" => [
                "beneficiary_id" => $beneficiary_id,
            ],
        ];

        return $this->fetch($headers, $data);
    }



    private function fetch(array $headers, $datas)
    {
        $datas = json_encode($datas,JSON_UNESCAPED_UNICODE);

        $signData = $this->signData($datas);

        try {
            $response = $this->client->post($this->url, [
                'headers' => array_merge($headers, [
                    "sign-data" => $signData,
                    "sign-thumbprint" => $this->signThumbprint,
                    "sign-system" => $this->signSystem,
                ]),
                'body' => $datas,
                'verify' => false,
            ]);

            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true),
            ];

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;

            return [
                "connection_validation" => $statusCode,
                "response" => $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : null,
            ];
        }
    }

    public function signData($requestData)
    {
        $privateKey = file_get_contents(base_path('rsaprivkey_new.pem'));
        $privateKeyResource = openssl_pkey_get_private($privateKey);

        if ($privateKeyResource === false) {
            throw new Exception('Invalid private key provided.');
        }

        openssl_sign($requestData, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        return str_replace(array("\r", "\n"), '', base64_encode($signature));
    }

}
