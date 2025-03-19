<?php

namespace App\Services\TochkaPaymentProcessor\YagodaTipsPayments;

use App\Models\OrderParticipant;
use App\Models\TochkaDeal;
use App\Services\TochkaPaymentProcessor\DocumentService;
use App\Services\TochkaService;
use App\Services\TochkaService\CommissionCalculator;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TipsDealService
{
    private $tochkaService;
    private $commissionCalculator;
    private $telegram;
    private $documentService;
    private $recipientsDistributeTips = [];
    private $beneficiaryId = '8d9ee009-b855-4d41-bf0f-e93b08031607';

    public function __construct(
        TochkaService $tochkaService,
        CommissionCalculator $commissionCalculator,
        TelegramService $telegram,
        DocumentService $documentService
    ) {
        $this->tochkaService = $tochkaService;
        $this->commissionCalculator = $commissionCalculator;
        $this->telegram = $telegram;
        $this->documentService = $documentService;
    }

    /**
     * Создаёт сделки на основе подготовленных данных платежей.
     *
     * @param array $prepareIidentifyPayments Данные для создания сделок
     * @return void
     */
    public function createDeal(array $prepareIidentifyPayments): void
    {
        if (empty($prepareIidentifyPayments['owners'])) {
            Log::info('Нет данных для создания сделок в TipsDealService.');
            return;
        }

        foreach ($prepareIidentifyPayments['owners'] as $owner) {
            $tochkaDeal = $this->createTochkaDeal($owner);
            $prepareDealData = $this->prepareDealData($owner);

            if ($owner['processPaymentTest']) {
                continue;
            }

            $tochkaDeal->update(['deal_prepare_data' => $prepareDealData]);
            $deal = $this->tochkaService->createDeal(
                $this->removeOriginalFromRecipients($prepareDealData)
            );

            if (isset($deal['body']['result']['deal_id'])) {
                $dealId = $deal['body']['result']['deal_id'];
                $tochkaDeal->update(['deal_id' => $dealId, 'deal_data' => $deal, 'status' => 'created']);

                $this->uploadDealDocuments($dealId, $owner);
                $this->executeDeal($dealId);
                $this->afterCreateDeal($owner);
            } else {
                $tochkaDeal->update(['deal_data' => $deal, 'status' => 'error']);

                $this->telegram->sendMessage(
                    '802964868',
                    sprintf(
                        'Ошибка при создании сделки для organization_id %d: %s',
                        $owner['groupId'],
                        json_encode($deal)
                    )
                );
            }
        }
    }

    /**
     * Создаёт запись TochkaDeal для владельца.
     *
     * @param array $owner Данные владельца
     * @return TochkaDeal
     */
    private function createTochkaDeal(array $owner): TochkaDeal
    {
        return TochkaDeal::create([
            'unidentified_payment_id' => $owner['unidentifiedPayment']->id,
            'organization_id' => $owner['groupId'],
            'status' => 'new',
        ]);
    }

    /**
     * Подготавливает данные для сделки.
     *
     * @param array $owner Данные владельца
     * @return array
     */
    private function prepareDealData(array $owner): array
    {
        $recipients = $this->prepareRecipients($owner);
        $amountToCredit = $this->commissionCalculator->calculateCommission(
            $owner['amount'],
            $owner['paymentMode'],
            0
        );

        $prepareDealData = [
            'ext_key' => Str::uuid()->toString(),
            'amount' => $amountToCredit['amount_to_credit'],
            'payers' => [
                [
                    'virtual_account' => $owner['virtual_account'],
                    'amount' => $amountToCredit['amount_to_credit']
                ]
            ],
            'recipients' => $recipients,
        ];

        return $this->finalCheckPrepare($prepareDealData);
    }

    /**
     * Удаляет поле 'original' из получателей.
     *
     * @param array $data Данные сделки
     * @return array
     */
    private function removeOriginalFromRecipients(array $data): array
    {
        $result = $data;
        foreach ($result['recipients'] as &$recipient) {
            unset($recipient['original']);
        }
        return $result;
    }

    /**
     * Подготавливает список получателей для сделки.
     *
     * @param array $owner Данные владельца
     * @return array
     */
    private function prepareRecipients(array $owner): array
    {
        $recipients = [
            [
                'number' => 1,
                'type' => 'commission',
                'amount' => $owner['organization']['commission'],
            ]
        ];

        if ($owner['organization']['tips'] > 0) {
            $distributeTips = $this->distributeTips($owner);
            foreach ($distributeTips as $value) {
                $this->recipientsDistributeTips[] = $value;
                $recipient = $value;
                unset($recipient['roleId'], $recipient['id'], $recipient['role']);
                $recipients[] = $recipient;
            }
        }

        return $recipients;
    }

    /**
     * Проверяет и корректирует данные сделки перед отправкой.
     *
     * @param array $prepareDealData Данные сделки
     * @return array
     */
    private function finalCheckPrepare(array $prepareDealData): array
    {
        $totalRecipients = array_sum(array_column($prepareDealData['recipients'], 'amount'));
        $payerAmount = $prepareDealData['payers'][0]['amount'];

        if ($totalRecipients !== $payerAmount) {
            Log::warning(sprintf(
                'Сумма получателей (%f) не совпадает с суммой плательщика (%f)',
                $totalRecipients,
                $payerAmount
            ));
            $difference = $payerAmount - $totalRecipients;
            $prepareDealData['recipients'][0]['amount'] += $difference;
        }

        return $prepareDealData;
    }

    /**
     * Загружает документы для сделки.
     *
     * @param string $dealId Идентификатор сделки
     * @param array $owner Данные владельца
     * @return void
     * @throws \Exception Если загрузка документа не удалась
     */
    private function uploadDealDocuments(string $dealId, array $owner): void
    {
        $contractId = $this->beneficiaryId;
        $documentDate = now()->format('Y-m-d');

        $commissionDoc = $this->documentService->getCommissionDocument([
            'baseOrderId' => $owner['baseOrderId'],
            'payerName' => $owner['payerName'],
            'commission' => $owner['organization']['commission'],
        ]);

        $this->uploadDocument($contractId, $dealId, $commissionDoc, $documentDate);

        foreach ($this->recipientsDistributeTips as $item) {
            if ($item['type'] !== 'payment_contract_to_card') {
                continue;
            }

            $paymentDoc = $this->documentService->getPaymentContractToCardDocument([
                'baseOrderId' => $owner['baseOrderId'],
                'payerName' => $owner['payerName'],
                'emloyeePhone' => $item['original']['phone'],
                'emloyeeName' => $item['original']['name'],
                'amount' => $item['amount'],
            ]);

            $this->uploadDocument($contractId, $dealId, $paymentDoc, $documentDate);
        }
    }

    /**
     * Загружает документ для сделки с проверкой результата.
     *
     * @param string $contractId Идентификатор контракта
     * @param string $dealId Идентификатор сделки
     * @param string $document Путь или имя документа
     * @param string $date Дата документа
     * @param int $version Версия документа
     * @return void
     * @throws \Exception
     */
    private function uploadDocument(string $contractId, string $dealId, string $document, string $date, int $version = 1): void
    {
        $result = $this->documentService->uploadDocumentDeal($contractId, $dealId, $document, $date, $version);

        if (!$result) {
            Log::error("Ошибка загрузки документа для сделки {$dealId}: {$document}");
            throw new \Exception("Не удалось загрузить документ: {$document}");
        }

        Log::info("Документ успешно загружен для сделки {$dealId}: {$document}");
    }

    /**
     * Выполняет действия после создания сделки.
     *
     * @param array $owner Данные владельца
     * @return void
     */
    private function afterCreateDeal(array $owner): void
    {
        // $this->processDebts();
        $this->saveDistributeTips($owner);
    }

    /**
     * Сохраняет чаевые среди участников заказа.
     *
     * @param array $owner Данные владельца
     * @return void
     */
    private function saveDistributeTips(array $owner): void
    {
        foreach ($this->recipientsDistributeTips as $item) {
            $orderParticipant = OrderParticipant::where('order_id', $owner['baseOrderId'])
                ->where('user_id', $item['id'])
                ->where('role_id', $item['roleId'])
                ->first();

            if ($orderParticipant) {
                $orderParticipant->tips = $item['amount'];
                $orderParticipant->save();
            }
        }
    }

    /**
     * Выполняет сделку.
     *
     * @param string $dealId Идентификатор сделки
     * @return string|null
     */
    public function executeDeal(string $dealId): ?string
    {
        Log::info("Исполнение сделки: {$dealId}");

        $response = $this->tochkaService->executeDeal($dealId);

        if (isset($response['body']['result']['deal_id'])) {
            Log::info("Сделка успешно выполнена: {$response['body']['result']['deal_id']}");
            TochkaDeal::where('deal_id', $dealId)->update(['status' => 'executing']);
            return $response['body']['result']['deal_id'];
        }

        Log::error("Ошибка при выполнении сделки: " . json_encode($response));

        TochkaDeal::where('deal_id', $dealId)->update(['status' => 'execution_error']);

        return null;
    }

    /**
     * Получает информацию о сделке.
     *
     * @param string $dealId Идентификатор сделки
     * @return array
     */
    public function getDeal(string $dealId): array
    {
        $response = $this->tochkaService->getDeal($dealId);

        if (isset($response['body']['result']['deal']['id'])) {
            Log::info("Сделка успешно получена: {$response['body']['result']['deal']['id']}");
            Log::info("Статус сделки: {$response['body']['result']['deal']['status']}");
        } else {
            Log::error("Ошибка получения сделки: " . json_encode($response));
        }

        return $response;
    }

    /**
     * Распределяет чаевые между участниками.
     *
     * @param array $prepareIidentifyPayment Данные для распределения
     * @return array
     */
    private function distributeTips(array $prepareIidentifyPayment): array
    {
        $distributor = new TipsDistributor($prepareIidentifyPayment);
        return $distributor->distributeTips();
    }
}
