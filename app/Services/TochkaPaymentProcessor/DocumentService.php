<?php

namespace App\Services\TochkaPaymentProcessor;

use App\Services\TochkaService;
use App\Services\CreatePdf;
use Illuminate\Support\Facades\Log;

class DocumentService
{
    private $tochkaService;
    private $createPdf;

    public function __construct()
    {
        $this->tochkaService = new TochkaService();
        $this->createPdf = new CreatePdf();
    }

    public function uploadDocumentDeal(string $beneficiaryId, string $dealId, string $documentPath, string $documentDate, string $documentNumber)
    {
        try {
            Log::info("Загрузка документа для сделки с ID бенефициара: " . $beneficiaryId . ", сделка: " . $dealId);
            $response = $this->tochkaService->uploadDocumentDeal($beneficiaryId, $dealId, $documentPath, $documentDate, $documentNumber);
            $documentId = $response['body']['document_id'] ?? null;
            if (!$documentId) {
                throw new \Exception('Создание документа не вернуло идентификатор.');
            }
            return $documentId;
        } catch (\Exception $e) {
            Log::error("Ошибка при загрузке документа: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCommissionDocument($owner)
    {
        $fileName = 'commission_' . date('Y-m-d') . '_' . $owner['baseOrderId'] . '.pdf';
        $customerName = 'Клиент ' . $owner['payerName'];

        $data = [
            'id' => $owner['baseOrderId'],
            'customer' => $customerName,
            'executor' => 'ООО Ягода (ИНН0800024353)',
            'file_name' => $fileName,
            'items' => [
                [
                    'name' => 'Вознаграждение сервиса',
                    'quantity' => 1,
                    'unit_price' => $owner['commission'],
                    'total_price' => $owner['commission'],
                ]
            ]
        ];

        $this->createPdf->create($data);

        return $data['file_name'];
    }

    public function getPaymentContractDocument($owner)
    {
        $fileName = 'payment_contract_' . date('Y-m-d') . '_' . $owner['baseOrderId'] . '.pdf';
        $customerName = 'Клиент ' . $owner['customerOrganizaionName'] . ' (ИНН ' . $owner['customerInn'] . ')';

        $items = [];
        foreach ($owner['orderItems'] as $product) {
            $discountAmount = $product['product_price'] * ((float)$owner['orderDiscount'] / 100);
            $discountedPrice = $product['product_price'] - $discountAmount;
            $roundedPrice = $discountedPrice >= 0 ? floor($discountedPrice) : 0;

            $items[] = [
                "name" => $product['product_name'],
                "unit_price" => $roundedPrice,
                "quantity" => $product['quantity'],
                "total_price" => $roundedPrice * $product['quantity'],
            ];
        }

        $data = [
            'id' => $owner['baseOrderId'] . '_2',
            'customer' => $customerName,
            'executor' => 'ООО Ягода (ИНН0800024353)',
            'file_name' => $fileName,
            'items' => $items
        ];

        $this->createPdf->create($data);
        return $data['file_name'];
    }

    public function getPaymentContractToCardDocument($owner)
    {
        $microtime = microtime(true);
        $milliseconds = round(($microtime - floor($microtime)) * 1000);
        $formattedDate = date('Y-m-d H:i:s', $microtime) . '-' . str_pad($milliseconds, 3, '0', STR_PAD_LEFT);

        $fileName = 'payment_contract_to_card_' . $formattedDate . '_' . $owner['baseOrderId'] . '.pdf';
        $customerName = 'Клиент ' . $owner['payerName'];
        $executorName = 'Сотрудник ' . $owner['emloyeePhone'] . ' ' . $owner['emloyeeName'];
        $recipients = [];

        $recipients[] = [
            'name' => 'Вознаграждение за работу (Чаевые). Дарение.',
            'quantity' => 1,
            'unit_price' => $owner['amount'],
            'total_price' => $owner['amount'],
        ];

        $data = [
            'id' => $owner['baseOrderId'] . '_3',
            'customer' => $customerName,
            'executor' => $executorName,
            'file_name' => $fileName,
            'items' => $recipients
        ];

        $this->createPdf->create($data);

        return $data['file_name'];
    }

    private function distributeTips($owner)
    {
        if ($owner['tips'] <= 0) {
            return [];
        }

        return [
            [
                'amount' => $owner['tips'],
            ]
        ];
    }
}
