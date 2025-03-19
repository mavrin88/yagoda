<?php

namespace App\Services\TochkaPaymentProcessor;

use App\Models\Bill;
use App\Models\Debt;
use App\Models\OrderParticipant;
use App\Models\TochkaDeal;
use App\Services\TochkaService;
use App\Services\TochkaService\CommissionCalculator;
use App\Services\TelegramService;
use App\Services\TochkaService\TipsDistributor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DealService
{
    private $tochkaService;
    private $commissionCalculator;
    private $telegram;

    private $missingAmounts = [];
    private $recipientsDistributeTips = [];

    public function __construct(
        TochkaService $tochkaService,
        CommissionCalculator $commissionCalculator,
        TelegramService $telegram
    ) {
        $this->tochkaService = $tochkaService;
        $this->commissionCalculator = $commissionCalculator;
        $this->telegram = $telegram;
    }

    public function createDeal(array $prepareIidentifyPayments)
    {
        if (empty($prepareIidentifyPayments['owners'])) {
            Log::info('Нет данных для создания сделок.');
            return;
        }

        foreach ($prepareIidentifyPayments['owners'] as $index => $owner) {
            if (!$owner['processPaymentTest']) {
                $tochkaDeal = TochkaDeal::create([
                    'unidentified_payment_id' => $owner['unidentifiedPayment']->id,
                    'organization_id' => $owner['organizationId'],
                    'status' => 'new',
                ]);
            }

            $recipients = $this->prepareRecipients($owner);
            $amountToCredit = $this->commissionCalculator->calculateCommission(
                $owner['amount'],
                $owner['paymentMode'],
                0 // Процент комиссии уже учтен в PaymentProcessor
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

            $prepareDealData = $this->finalCheckPrepare($prepareDealData);
            $prepareDealData = $this->fixMass($prepareDealData);

            if (!$owner['processPaymentTest']) {
                $tochkaDeal->update(['deal_prepare_data' => $prepareDealData]);
                $deal = $this->tochkaService->createDeal($prepareDealData);

                if (isset($deal['body']['result']['deal_id'])) {
                    $dealId = $deal['body']['result']['deal_id'];
                    $tochkaDeal->update(['deal_id' => $dealId, 'deal_data' => $deal, 'status' => 'created']);

                    $this->uploadDocuments($dealId, $owner['organization']['tips'], $owner['beneficiaryId'], $owner);
                    $this->executeDeal($dealId);
                    $this->afterCreateDeal($owner);
                } else {
                    $tochkaDeal->update(['deal_data' => $deal, 'status' => 'error']);
                    $this->telegram->sendMessage('802964868', 'Ошибка при создании сделки: ' . json_encode($deal));
                }
            }
        }
    }

    private function prepareRecipients($owner)
    {
        $recipients = [
            [
                "number" => 1,
                "type" => "commission",
                "amount" => $owner['organization']['commission'],
            ]
        ];

        foreach ($owner['organization']['organizartion'] as $key => $value) {
            $recipient = [
                "number" => 2 + $key,
                "type" => "payment_contract",
                "amount" => $value['sum'],
                "account" => $value['account'],
                "bank_code" => $value['bank_code'],
                "name" => $value['name'],
                "inn" => $value['inn'],
            ];
            if (isset($value['kpp'])) {
                $recipient['kpp'] = $value['kpp'];
            }
            $recipients[] = $recipient;
        }

        if ($owner['organization']['tips'] > 0) {
            $distributeTips = $this->distributeTips($owner);
            foreach ($distributeTips as $key => $value) {

                $this->recipientsDistributeTips[] = $value;

                unset($value['roleId'], $value['id'], $value['role']);

                $recipients[] = $value;
            }
        }

        return $recipients;
    }

    private function finalCheckPrepare($prepareDealData)
    {
        $totalRecipients = array_sum(array_column($prepareDealData['recipients'], 'amount'));
        $payerAmount = $prepareDealData['payers'][0]['amount'];

        if ($totalRecipients !== $payerAmount) {
            Log::warning('Сумма получателей (' . $totalRecipients . ') не совпадает с суммой плательщика (' . $payerAmount . ')');
            $difference = $payerAmount - $totalRecipients;
            $recipients = &$prepareDealData['recipients'];
            $recipients[0]['amount'] += $difference; // Корректируем комиссию
        }

        return $prepareDealData;
    }

    private function fixMass($prepareDealData)
    {
        $existingDebt = 0;

        // Фильтруем контракты оплаты
        $filteredContracts = array_filter($prepareDealData['recipients'], function($item) {
            return $item['type'] === 'payment_contract';
        });

        $filteredContracts = array_values($filteredContracts);

        // Поскольку мы уверены, что есть хотя бы один счет
        $bill = Bill::where('number', $filteredContracts[0]['account'])->first();
        $debt = Debt::find($bill->id);

        if ($debt) {
            $existingDebt = $debt->amount;
        }

        $adjustedAmount = 0;
        $newDebtAmount = 0;

        // Проверяем элементы в 'recipients' на наличие комиссии
        foreach ($prepareDealData['recipients'] as $key => $recipient) {
            if ($recipient['type'] === 'commission') {

                // Если получаем еще один долг
                if ($recipient['amount'] < 0) {
                    // Если сумма отрицательная, запоминаем её (по абсолютному значению)
                    $adjustedAmount = abs($recipient['amount']);
                    // $adjustedAmount = $adjustedAmount + $existingDebt;
                    $newDebtAmount = $adjustedAmount + $existingDebt;
                    // Удаляем элемент с type = 'commission'
                    unset($prepareDealData['recipients'][$key]);
                }

                // Если получаем прибыль
                if ($recipient['amount'] > 0) {
                    // Если есть долг и не покрываем его
                    if ($existingDebt > $recipient['amount']) {

                        $newDebtAmount = (float)number_format($existingDebt - $recipient['amount'], 2, '.', '');

                        unset($prepareDealData['recipients'][$key]);

                        // Находим и обновляем соответствующий элемент с типом payment_contract
                        foreach ($prepareDealData['recipients'] as &$paymentRecipient) {
                            if ($paymentRecipient['type'] === 'payment_contract') {
                                $paymentRecipient['amount'] += $recipient['amount'];
                                break;
                            }
                        }
                    }


                    // Если есть долг и покрываем весь долг
                    if ($recipient['amount'] >= $existingDebt) {

                        $newDebtAmount = 0;

                        $prepareDealData['recipients'][$key]['amount'] = (float)number_format($recipient['amount'] - $existingDebt, 2, '.', '');

                        if ((float)number_format($recipient['amount'] - $existingDebt, 2, '.', '') == 0) {
                            unset($prepareDealData['recipients'][$key]);
                        }


                        // Находим и обновляем соответствующий элемент с типом payment_contract
                        foreach ($prepareDealData['recipients'] as &$paymentRecipient) {
                            if ($paymentRecipient['type'] === 'payment_contract') {
                                $paymentRecipient['amount'] += $existingDebt;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Корректируем сумму в 'payment_contract', если есть долг
        foreach ($prepareDealData['recipients'] as $key => &$recipient) { // используем & для ссылки на элемент
            if ($recipient['type'] === 'payment_contract') {
                $recipient['amount'] -= $adjustedAmount;
                break;


                // Записываем долг в БД, если adjustedAmount больше 0
                if (($adjustedAmount + $newDebtAmount) > 0) {
                    // Проверка на существующий долг по аккаунту
                    // $existingDebt = Debt::where('account', $recipient['account'])->first();

                    if ($existingDebt) {
                        // Если долг уже существует, обновляем сумму долга
                        $existingDebt += $adjustedAmount;
                        // $existingDebt->save(); // сохраняем изменения
                    } else {
                        // Если долга нет, создаем новую запись
                        // Debt::create([
                        //     'account' => $recipient['account'],
                        //     'amount' => $adjustedAmount,
                        //     'status' => 'unpaid', // или любой другой статус, который вам нужен
                        //     // Другие поля, если необходимо
                        // ]);
                    }
                }
            }
        }

        // Переиндексация массива после удаления
        $prepareDealData['recipients'] = array_values($prepareDealData['recipients']);

        // dump($newDebtAmount);
        // dd($prepareDealData);

        // if ($missingAmount > 0) {
        $this->missingAmounts[] = [
            'account' => $filteredContracts[0]['account'],
            'amount' => $newDebtAmount,
        ];
        // }

        return $prepareDealData;
    }

    private function afterCreateDeal($owner)
    {
        // Обработка долгов
        $this->processDebts();

        // Распределение чаевых
        $this->saveDistributeTips($owner);
    }

    /**
     * Обрабатывает долги на основе missingAmounts.
     */
    private function processDebts()
    {
        foreach ($this->missingAmounts as $key => $value) {
            $bill = Bill::where('number', $value['account'])->first();

            if ($bill) {
                $debt = Debt::firstOrCreate(
                    ['bill_id' => $bill->id],
                    ['amount' => $value['amount']]
                );

                if (!$debt->wasRecentlyCreated) {
                    $debt->amount = $value['amount'];
                    $debt->save();
                }
            }
        }

        $this->missingAmounts = [];
    }

    /**
     * Сохраняет чаевые среди участников заказа.
     *
     * @param array $owner Данные владельца заказа.
     */
    private function saveDistributeTips($owner)
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

    public function executeDeal(string $dealId)
    {
        Log::info('Исполнение сделки: ' . $dealId);
        $response = $this->tochkaService->executeDeal($dealId);
        if (isset($response['body']['result']['deal_id'])) {
            Log::info('Сделка успешно выполнена: ' . $response['body']['result']['deal_id']);
            TochkaDeal::where('deal_id', $dealId)->update(['status' => 'executing']);
            return $response['body']['result']['deal_id'];
        }
        Log::error('Ошибка при выполнении сделки: ' . json_encode($response));
        TochkaDeal::where('deal_id', $dealId)->update(['status' => 'execution_error']);
        return null;
    }

    public function getDeal(string $dealId)
    {
        $response = $this->tochkaService->getDeal($dealId);
        if (isset($response['body']['result']['deal']['id'])) {
            Log::info('Сделка успешно получена: ' . $response['body']['result']['deal']['id']);
            Log::info('Статус сделки: ' . $response['body']['result']['deal']['status']);
        } else {
            Log::error('Ошибка получения сделки: ' . json_encode($response));
        }
        return $response;
    }

    private function distributeTips($prepareIidentifyPayment)
    {
        $distributor = new TipsDistributor($prepareIidentifyPayment);
        return $distributor->distributeTips();
    }


    // Метод будет переопределен в TochkaCommand через DI
    public function uploadDocuments(string $dealId, ?float $tips, string $beneficiaryId, array $owner) {}
}
