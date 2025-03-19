<?php

namespace App\Console\Commands;

use App\Models\UnidentifiedPayment;
use App\Models\Order;
use App\Models\OrderStatistics;
use App\Models\Role;
use App\Models\Setting;
use App\Models\TochkaDeal;
use App\Models\User;
use App\Models\Organization;
use App\Models\Bill;
use App\Models\Debt;
use App\Services\CreatePdf;
use App\Services\EncryptionCardNumber;
use App\Services\TelegramService;
use App\Services\TochkaApiService;
use App\Services\TochkaService;
use App\Services\TochkaService\TipsDistributor;
use App\Services\TochkaService\CommissionCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Services\TochkaService\OrderFixService;

class TochkaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tochka:run';

    private $unidentifiedPayment;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $unidentifiedPaymentList = [];
    private $unidentifiedPaymentInfo = [];
    private $paymentsAprovedList = [];
    private $prepareIidentifyPayments = [];
    private $prepareIidentifyPaymentsCorrections = [];
    private $missingAmounts = [];
    private $sbp_payment_fee_percent;
    private $processedOrderIds = [];
    private $calculatePaymentDetailsArray = [];
    private $isTest = false;
    protected $chatIds = [
        '802964868',
        '272393976',
        '95113171'
    ];

    private $tochkaService;
    private $encryptionCardNumber;
    private $createPdf;
    private $tochkaApiService;
    private $commissionCalculator;
    private $telegram;

    /**
     * Execute the console command.
     */
    public function handle(
        TochkaService $tochkaService,
        EncryptionCardNumber $encryptionCardNumber,
        CreatePdf $createPdf,
        TochkaApiService $tochkaApiService,
        CommissionCalculator $commissionCalculator,
    )
    {
        $this->tochkaService = $tochkaService;
        $this->encryptionCardNumber = $encryptionCardNumber;
        $this->createPdf = $createPdf;
        $this->tochkaApiService = $tochkaApiService;
        $this->commissionCalculator = $commissionCalculator;

        $this->calculatePaymentDetailsArray = [
            "commission" => 0,
            "percentage" => 0,
            "total" => 0
        ];

        $this->telegram = new TelegramService();

        $this->proccessForUnidentifiedPayments();
        // $this->processPaymentsDetailsTest();
    }

    private function proccessForUnidentifiedPayments()
    {
        $this->myLog('Проверка наличия неидентифицированных денег на н/с');

        try {
            $payments = $this->tochkaService->listPayments();
//             dd($payments);
        } catch (\Exception $e) {
            $this->myLogDd('Ошибка при получении платежей: ' . $e->getMessage());
            return;
        }

        if ($this->hasAvailablePayments($payments)) {
            $payments = $payments['body']['result']['payments'] ?? [];
            $this->myLog('Есть платежи в ответе: ' . count($payments) . ' шт.');
            $this->processPaymentsDetails($payments);
        } else {
            $this->myLogDd('Нет доступных платежей.');
        }
    }

    private function processPaymentsDetailsTest()
    {
        $identy = UnidentifiedPayment::find(178);

        $this->unidentifiedPayment = $identy;

        $this->unidentifiedPaymentInfo = $identy->details;
        $this->processIdentificationSbp();
    }

    private function processPaymentsDetails(array $payments): void
    {
        $reversedPayments = $payments;

        foreach ($reversedPayments as $key => $payment) {
            // if ($payment !== 'cbs-tb-92-2202695628') {
            try {
                $paymentInfo = $this->getPaymentInfo($payment);
                $this->unidentifiedPaymentInfo = $paymentInfo;

//                 dump($paymentInfo);

                $this->unidentifiedPayment = UnidentifiedPayment::firstOrCreate(
                    [
                        'payment_id' => $payment,
                    ],
                    [
                        'type' => $paymentInfo['type'] ?? 'unknown',
                        'details' => $paymentInfo,
                    ]
                );

                if (isset($paymentInfo['purpose']) && str_contains($paymentInfo['purpose'], 'Возврат средств')) {
                    $this->unidentifiedPayment->update(['type' => 'return']);
                    continue;
                }

                $this->processPaymentByType($paymentInfo);

            } catch (Exception $e) {
                Logger::error("Payment processing failed: " . $e->getMessage());
                continue;
            }
            // }
        }
    }

    private function processPaymentByType(array $paymentInfo): void
    {
        switch ($paymentInfo['type'] ?? 'unknown') {
            case 'incoming_sbp':
                $this->telegram->sendMessage('802964868', 'Внимание! Новый платеж неразобранный.');
                $this->processIdentificationSbp();
                break;
            case 'incoming':
//                $this->telegram->sendMessageToUsers($this->chatIds, 'Внимание! Новый платеж неразобранный.');
                $this->processIdentificationCard();
                break;
            default:
                Logger::error("Не известный тип оплаты");
                break;
        }
    }

    private function processIdentificationSbp()
    {
        $purpose = $this->unidentifiedPaymentInfo['purpose'];

        $qrcId = $this->extractQrCode($purpose);

        $orderStatistic = OrderStatistics::where('qrcId', $qrcId)->first();

        if (!$orderStatistic) {
            $this->myLogDd('Не удалось получить информацию о заказе у нас в базе: ' . $qrcId);
            return;
        }

        $operations = $this->proccessPaymentsAprovedSbp();

        $sbpPayments = array_filter($operations, function($operation) use ($orderStatistic){
            return $operation['purpose'] === $orderStatistic->uuid;
        });

        $this->paymentsAprovedList = array_values($sbpPayments);

        $this->updateOtherDataUnidentifiedPayment($this->paymentsAprovedList);

        $this->distributionMmoneyAmongVirtualAccounts();

        $this->createDealNew();
    }

    private function extractQrCode($inputString) {
        $pattern = '/[A-Z0-9]{8,40}/';

        if (preg_match($pattern, $inputString, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Обрабатывает карточные платежи.
     *
     * Этот метод выполняет следующие шаги:
     * 1. Вычисляет детали платежа (комиссию которая приходит по утрам).
     * 2. Записывает дополнительные данные по неидентифицированному платежу.
     * 3. Фильтрует список одобренных платежей по карте.
     * 4. Распределяет деньги между виртуальными счетами.
     * 5. Создает новую сделку.
     *
     * @return void
     */
    private function processIdentificationCard()
    {
        $this->calculatePaymentDetails();
        $this->paymentsAprovedList = $this->filteredPaymentsCard();

        $this->updateOtherDataUnidentifiedPayment($this->paymentsAprovedList);

        // Проверка, является ли массив платежей пустым
        if (empty($this->paymentsAprovedList)) {
            // Если массив пуст, просто выходим из метода без дальнейших действий
            return;
        }

        // Выполняем распределение денег и создание сделок, только если массив не пуст
        $this->distributionMmoneyAmongVirtualAccounts();
        $this->createDealNew();
    }

    private function proccessPaymentsAprovedSbp()
    {
        $this->myLog('Получаем список оплаченных заказов');

        try {
            $paymentsAproved = $this->filteredPaymentsSbp();
        } catch (\Exception $e) {
            $this->myLogDd('Ошибка при получении заказов: ' . $e->getMessage());
            return;
        }

        if ($this->hasAvailablePaymentsAproved($paymentsAproved)) {
            $this->paymentsAprovedList = $paymentsAproved ?? [];
            // $this->unidentifiedPayment->update(['payments_aproved_list' => $this->paymentsAprovedList]);
            $this->myLog('Есть оплаченные заказы в ответе: ' . count($this->paymentsAprovedList) . ' шт.');
            $this->myLog('Сумма оплаченных заказов: ' . array_sum(array_column($this->paymentsAprovedList, 'amount')));
        }else{
            $this->myLogDd('Нет оплаченных заказов.');
        }

        $this->paymentsAprovedList = $paymentsAproved;

        return $this->paymentsAprovedList;
    }

    private function distributionMmoneyAmongVirtualAccounts()
    {
        $this->prepareIidentifyPayments = [
            "payment_id" => $this->unidentifiedPaymentInfo['id'],
            "amount" => $this->unidentifiedPaymentInfo['amount'],
            "amount_to_credit" => $this->unidentifiedPaymentInfo['amount'],
            'owners' => []
        ];

        foreach ($this->paymentsAprovedList as $key => $payment) {
            // if ($key == 2) {
            $this->myLog('Получение информации о конкретном платеже у нас в базе: ' . $payment['purpose']);

            $virtualAccountData = $this->getVirtualAccountDataForPayment($payment);

            if ($virtualAccountData) {
                $this->prepareIidentifyPayments['owners'][] = $virtualAccountData;
            }
            // }
        }

        $prepareIidentifyPayments = $this->prepareIidentifyPayments($this->prepareIidentifyPayments);
        dump($prepareIidentifyPayments);

        if (!$this->isTest) {
            $response = $this->identifyPayment($prepareIidentifyPayments);
            dump($response);
        }
    }

    private function getVirtualAccountDataForPayment($payment)
    {
        $orderStatistic = OrderStatistics::where('uuid', $payment['purpose'])->first();

        if ($orderStatistic) {
            $order = Order::find($orderStatistic->order_id);
            $organization = $order->organization;
            $beneficiaryId = $this->checkBenificiary($organization);
            $virtualAccount = $this->getVirtualAccount($beneficiaryId);

            $amount = $this->calculatePaymentAmount($payment);

            $orderItems = [];

            foreach ($order->orderItems as $orderItem) {
                $orderItems[] = $orderItem->toArray();
            }

            return [
                "virtual_account" => $virtualAccount,
                "amount" => $amount,
                "organization" => $this->orderPracticants($orderStatistic),
                "beneficiaryId" => $beneficiaryId,
                "orderItems" => $orderItems,
                "orderDiscount" => $order->discount ?? 0,
                "organizationEmail" => $organization->email,
                "baseOrderId" => $order->id,
                "customerOrganizaionName" => $organization->name,
                "payerName" => $orderStatistic->payer_name,
                "customerInn" => $organization->inn,
                "paymentMode" => $payment['paymentMode'][0],
                "organizationId" => $order->organization->id,
                "organizationName" => $order->organization->name,
                "agencyAgreementDate" => $order->organization->agency_agreement_date,
                "agencyAgreementNumber" => $order->organization->agency_agreement_number,
            ];
        } else {
            $this->myLogDd('Не удалось получить статистику для оплаченного заказа: ' . $payment['purpose']);
            return null;
        }
    }

    private function prepareIidentifyPayments($identifyPayments)
    {
        $data = [
            "payment_id" => $identifyPayments['payment_id'],
        ];

        foreach ($identifyPayments['owners'] as $key => $identifyPayment) {
//            if ($key == 0) {
            $amount_to_credit = $this->commissionCalculator->calculateCommission(
                $identifyPayment['amount'],
                $identifyPayment['paymentMode'],
                $this->calculatePaymentDetailsArray['percentage']
            );

            $data['owners'][] = [
                'virtual_account' => $identifyPayment['virtual_account'],
                'amount' => $amount_to_credit['amount_to_credit'],
            ];
//            }
        }

        $data = $this->checkAmount($data);

        return $data;
    }

    public function checkAmount($data)
    {
        $targetAmount = $this->prepareIidentifyPayments['amount'];
        $totalAmount = 0.0;

        foreach ($data['owners'] as $owner) {
            $totalAmount += $owner['amount'];
        }

        $totalAmount = round($totalAmount, 2);
        $difference = round($targetAmount - $totalAmount, 2);

        if ($difference > 0) {
            // Не хватает суммы к общей сумме, добиваем к наименьшему значению
            $maxIndex = array_search(min(array_column($data['owners'], 'amount')), array_column($data['owners'], 'amount'));
            $originalAmount = $data['owners'][$maxIndex]['amount'];
            $data['owners'][$maxIndex]['amount'] = round($data['owners'][$maxIndex]['amount'] + $difference, 2);

            // Логируем изменения
            Log::info('Не хватает суммы к общей сумме, добиваем к наименьшему значению: ', [
                'virtual_account' => $data['owners'][$maxIndex]['virtual_account'],
                'original_amount' => $originalAmount,
                'new_amount' => $data['owners'][$maxIndex]['amount'],
                'needed' => $difference
            ]);

        } elseif ($difference < 0) {
            // Сумма лишняя от общей суммы, онимаем в наименьшиму значению
            $minIndex = array_search(max(array_column($data['owners'], 'amount')), array_column($data['owners'], 'amount'));
            $originalAmount = $data['owners'][$minIndex]['amount'];
            $data['owners'][$minIndex]['amount'] = round($data['owners'][$minIndex]['amount'] - abs($difference), 2);

            // Логируем изменения
            Log::info('Сумма лишняя от общей суммы, онимаем в наименьшиму значению: ', [
                'virtual_account' => $data['owners'][$minIndex]['virtual_account'],
                'original_amount' => $originalAmount,
                'new_amount' => $data['owners'][$minIndex]['amount'],
                'excess' => abs($difference)
            ]);
        }

        $this->prepareIidentifyPaymentsCorrections = $data;

        return $data;
    }

    private function calculatePaymentAmount($payment)
    {
        $baseAmount = $payment['amount'];

        $commissionPercentage = $this->getCommissionPercentage($payment['paymentType']);

        $commissionAmount = ($baseAmount * $commissionPercentage) / 100;

        return round($baseAmount - $commissionAmount, 2);
    }

    private function getCommissionPercentage($paymentType)
    {
        switch ($paymentType) {
            case 'sbp':
                // return 0.7;
                return 0;
            case 'card':
                return 0;
            default:
                return 0;
        }
    }

    private function createDealNew()
    {
        foreach ($this->prepareIidentifyPayments['owners'] as $index => $prepareIidentifyPayment) {
//             if ($index == 19) {
            $this->myLog('Начинаем создавать сделку....');

            if (!$this->isTest) {
                $tochkaDeal = TochkaDeal::create([
                    'unidentified_payment_id' => $this->unidentifiedPayment->id,
                    'organization_id' => $this->prepareIidentifyPayments['owners'][0]['organizationId'],
                    'status' => 'new',
                ]);
            }

            $recipients = [];
            $recipientsDistributeTips = [];

            $recipients[] = [
                "number" => 1,
                "type" => "commission",
                "amount" => $prepareIidentifyPayment['organization']['commission'],
                // "purpose" => "Необходимый текст назначения платежа, без НДС"
            ];


            foreach ($prepareIidentifyPayment['organization']['organizartion'] as $key => $value) {
                $recipient = [
                    "number" => 2 + $key,
                    "type" => "payment_contract",
                    "amount" => $value['sum'],
                    "account" => $value['account'],
                    "bank_code" => $value['bank_code'],
                    "name" => $value['name'],
                    "inn" => $value['inn'],
                ];

                // Добавляем КПП только если он существует
                if (isset($value['kpp'])) {
                    $recipient['kpp'] = $value['kpp'];
                }

                $recipients[] = $recipient;
            }

            if ($prepareIidentifyPayment['organization']['tips'] > 0) {
                $distributeTips = $this->distributeTips($prepareIidentifyPayment);
                foreach ($distributeTips as $key => $value) {
                    $recipientsDistributeTips[] = $value;

                    unset($value['roleId'], $value['id'], $value['role'], $value['original']);

                    $recipients[] = $value;
                }
            }

//            dd($tipsWithIds);

            // dd($prepareIidentifyPayment['amount']);

            // dd($prepareIidentifyPayment['organization']['total']);
            // $prepareIidentifyPayment['amount'] = $prepareIidentifyPayment['organization']['total'];
            // ТУТ МЫ КОРРЕКТИРУЕМ. ЛУЧШЕ ИСПРАВИТЬ

            $amount_to_credit = $this->commissionCalculator->calculateCommission(
                $prepareIidentifyPayment['amount'],
                $prepareIidentifyPayment['paymentMode'],
                $this->calculatePaymentDetailsArray['percentage']
            );

            // dd($amount_to_credit);

            $prepareDealData = [
                'ext_key' => Str::uuid()->toString(),
                'amount' => $amount_to_credit['amount_to_credit'],
                'payers' => [
                    [
                        'virtual_account' => $prepareIidentifyPayment['virtual_account'],
                        'amount' => $amount_to_credit['amount_to_credit'],
                    ]
                ],
                'recipients' => $recipients,
            ];

            $prepareDealData = $this->finalCheckPrepare($prepareDealData, $index);
            $prepareDealData = $this->fixMass($prepareDealData);

            // dd($prepareDealData);

            dump($this->missingAmounts);

            if ($this->isTest) {
                dd($prepareDealData);
            }

            if (!$this->isTest) {
                $tochkaDeal->update(
                    [
                        'deal_prepare_data' => $prepareDealData
                    ]
                );
            }

            $deal = $this->tochkaService->createDeal($prepareDealData);

            if (isset($deal['body']['result']['deal_id'])) {
                $dealId = $deal['body']['result']['deal_id'];

                if (!$this->isTest) {
                    $tochkaDeal->update(
                        [
                            'deal_id' => $dealId,
                            'deal_data' => $deal
                        ]
                    );
                }

                $this->myLog('Сделка успешно создана: ' . $dealId);

                $this->uploadDocuments($dealId, $prepareIidentifyPayment['organization']['tips'], $this->prepareIidentifyPayments['owners'][0]['beneficiaryId']);
                $this->executeDeal($dealId);

                $this->afterCreateDeal($dealId);

            } else {
                if (!$this->isTest) {
                    $tochkaDeal->update(
                        [
                            'deal_data' => $deal
                        ]
                    );
                }
                $this->telegram->sendMessageToUsers($this->chatIds, 'Ошибка при создании сделки, продлжение невозможно: ' . json_encode($deal));
//                Log::error('Ошибка при создании сделки, продлжение невозможно: ' . json_encode($deal));
                $this->myLog('Ошибка при создании сделки, продлжение невозможно: ' . json_encode($deal));
            }
//            } else {
//                $this->telegram->sendMessageToUsers($this->chatIds, 'Суммы не совпадают или произошла ошибка, не сможем создать сделку.');
//            }
        }
//        }
    }

    private function afterCreateDeal($dealId)
    {
        sleep(5);
        dump($this->getDeal($dealId));

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

    public function finalCheckPrepare($prepareDealData, $index)
    {
        // Получаем суммы
        $firstAmount = $this->prepareIidentifyPaymentsCorrections['owners'][$index]['amount'];
        $secondAmount = $prepareDealData['amount'];

        // Поиск индекса элемента с типом "commission"
        $commissionIndex = array_search('commission', array_column($prepareDealData['recipients'], 'type'));

        // Поиск индекса элемента с типом "payment_contract"
        $paymentContractIndex = array_search('payment_contract', array_column($prepareDealData['recipients'], 'type'));

        // Вычисляем разницу
        $difference = round($firstAmount - $secondAmount, 2);

        // Выводим значения и разницу
        dump("Сумма из первого массива: {$firstAmount}");
        dump("Сумма из второго массива (recipients[0]['amount']): {$secondAmount}");
        dump("Разница: " . number_format($difference, 2));



        // // Поиск элемента с типом "commission"
        // $commissionArray = array_filter($prepareDealData['recipients'], function($recipient) {
        //     return $recipient['type'] === 'commission';
        // });

        // // Поиск элемента с типом "payment_contract"
        // $paymentContractArray = array_filter($prepareDealData['recipients'], function($recipient) {
        //     return $recipient['type'] === 'payment_contract';
        // });

        // // Получаем первый элемент
        // $firstCommission = !empty($commissionArray) ? reset($commissionArray) : null;
        // $firstPaymentContract = !empty($paymentContractArray) ? reset($paymentContractArray) : null;

        // // Вычисляем разницу
        // $difference = $firstAmount - $secondAmount;

        // // Выводим значения и разницу
        // dump("Сумма из первого массива: {$firstAmount}");
        // dump("Сумма из второго массива (recipients[0]['amount']): {$secondAmount}");
        // dump("Разница: " . number_format($difference, 2));

        // Вычисляем разницу
        $difference = round($firstAmount - $secondAmount, 2);

        // Проверяем, является ли разница отрицательной
        if ($difference < 0) {
            // dd("Разница отрицательная: {$difference}");

            $prepareDealData['amount'] -= abs($difference);
            $prepareDealData['payers'][0]['amount'] -= abs($difference);

            if ($commissionIndex !== false) {
                // Изменяем amount у элемента с типом 'commission'
                $prepareDealData['recipients'][$commissionIndex]['amount'] -= abs($difference);
            }

            // if ($paymentContractIndex !== false) {
            //     // Изменяем amount у элемента с типом 'payment_contract'
            //     $prepareDealData['recipients'][$paymentContractIndex]['amount'] -= $difference;
            // }

        } else {
            // dd("Разница положительная или ноль: {$difference}");
            $prepareDealData['amount'] += $difference;
            $prepareDealData['payers'][0]['amount'] += $difference;

            if ($commissionIndex !== false) {
                // Изменяем amount у элемента с типом 'commission'
                $prepareDealData['recipients'][$commissionIndex]['amount'] += $difference;
                // dump("Обновленный элемент с типом 'commission':", $prepareDealData['recipients'][$commissionIndex]);
            }

            // if ($paymentContractIndex !== false) {
            //     dd($prepareDealData['recipients'][$paymentContractIndex]['amount']);
            //     // Изменяем amount у элемента с типом 'payment_contract'
            //     // $prepareDealData['recipients'][$paymentContractIndex]['amount'] += $difference;
            // }
        }

        return $prepareDealData;

        // dump($this->prepareIidentifyPaymentsCorrections['owners'][$index]);
        // dd($prepareDealData);
    }

    private function distributeTips($prepareIidentifyPayment)
    {
        $distributor = new TipsDistributor($prepareIidentifyPayment);
        return $distributor->distributeTips();
    }

    private function getPaymentInfo(string $payment): array
    {
        try {
            $this->myLog('Запрос информации о платеже: ' . $payment);

            $response = $this->tochkaService->getPayment($payment);

            if (empty($response['body']['result']['payment'])) {
                throw new \Exception('Не удалось получить данные о платеже.');
            }

            return $response['body']['result']['payment'];
        } catch (\Exception $e) {
            $this->myLog('Ошибка при получении информации о платеже ' . $payment . ': ' . $e->getMessage());
            return [];
        }
    }

    private function identifyPayment(array $identifyPayment)
    {
        $this->unidentifiedPayment->update(
            [
                'unidentified_payments_prepare_data' => $identifyPayment
            ]
        );

        try {
            $this->myLog('Зачисляем деньги на вирт. счёт бенефициара, т.е. идентифицируем их');

            $response = $this->tochkaService->identificationPayment($identifyPayment);

            $this->unidentifiedPayment->update(
                [
                    'status' => 'identification_payment_success',
                    'identification_payment' => $response
                ]
            );

            OrderStatistics::whereIn('order_id', $this->processedOrderIds)->update(['processed_at' => Carbon::now()]);


            return $response;
        } catch (\Exception $e) {
            $this->unidentifiedPayment->update(
                [
                    'status' => 'identification_payment_error',
                    'identification_payment' => $e->getMessage()
                ]
            );
            $this->myLog('Ошибка при зачисления деньги на вирт. счёт бенефициара : ' . $e->getMessage());
            return [];
        }
    }

    private function orderPracticants($payment)
    {
//        if ($payment->order_id == 2037) {
        $tips_sum = $payment->tips_sum;
        $organization_summ = 0;
        $orderPracticants = [];

        $order = Order::find($payment->order_id);
        $orderStatistics = OrderStatistics::where('order_id', $order->id)->where('status', 'ok')->first();

        $acquiring_fee_percent = $payment->acquiring_fee;
        $payment_fee_percent = Setting::where('key', $payment->type_pay . '_payment_fee')->first()->value;

        foreach ($order->orderItems as $key => $item) {
            $bill = Bill::find($item->bill_id);

            $discountPercentage = $order->discount; // Процент скидки
            $itemTotal = $item->quantity * $item->product_price; // Общая сумма
            $discountAmount = ($discountPercentage / 100) * $itemTotal; // Сумма скидки
            $finalTotal = $itemTotal - $discountAmount; // Итоговая сумма после вычитания скидки

            // Получаем сумму товара
            $itemTotal = $finalTotal;

            $adjustedTotalSum = $itemTotal;

            $sum = $adjustedTotalSum + $payment->tips_sum + $payment->service_acquiring_commission;


            if ($orderStatistics->source_of_compensation == 'tips') {

                if ($tips_sum > 0 && $payment->service_acquiring_commission > 0) {
                    $tips_sum = $tips_sum - $payment->service_acquiring_commission;
                }

                if ($payment->service_acquiring_commission == 0 && $payment->tips_sum > 0) {
                    $tips_sum = 0;
                }

                // Если источник компенсации ЧАЕВЫЕ, есть чаевые и клиент галку компенсации издержки отжал, тогда вычитаем остаток компенсации из чаевых
                if ($payment->fee_consent > 0 && $payment->tips_sum > 0) {
                    $tips_sum = max(0, $payment->tips_sum - $payment->fee_consent);
                }
            }

            $amount_to_credit = $this->commissionCalculator->calculateCommission(
                $sum,
                $payment['type_pay'],
                $this->calculatePaymentDetailsArray['percentage']
            );

            $acquiring_fee = ($acquiring_fee_percent / 100) * $adjustedTotalSum;
            $card_payment_fee = round(($payment_fee_percent / 100) * $sum, 2);

            $organization_sum = ($adjustedTotalSum - $acquiring_fee);
            $commission_sum = $amount_to_credit['amount_to_credit'] - $organization_sum - $payment->tips_sum;

            if ($orderStatistics->source_of_compensation == 'tips') {
                $commission_sum = $amount_to_credit['amount_to_credit'] - $organization_sum - $tips_sum;
            }

            if (!isset($organizationSums[$item->bill_id])) {
                $organizationSums[$item->bill_id] = [
                    'name' => $order->organization->name,
                    'inn' => $order->organization->inn,
                    'kpp' => $order->organization->kpp,
                    'bank_code' => $bill->bik,
                    'account' => $bill->number,
                    'sum' => 0
                ];
            }

            $organizationSums[$item->bill_id]['sum'] += $organization_sum;

            $organization_summ += $organization_sum;
        }

        array_walk($organizationSums, function (&$item) {
            $item['sum'] = round($item['sum'], 2);
        });

        $totalSumFromOrganizations = array_sum(array_column($organizationSums, 'sum'));

        $organizations = array_values($organizationSums);

        $orderPracticants[] = $this->prepareDistributeTips($order, $payment);

        $amount_to_credit = $this->commissionCalculator->calculateCommission($payment->full_amount, '', $this->calculatePaymentDetailsArray['percentage']);

        $commissionSumm = $amount_to_credit['amount'] - $amount_to_credit['amount_to_credit'];

        $commission = $payment->full_amount - $tips_sum - $commissionSumm - $totalSumFromOrganizations;

        $dealData = [
            'total' => floatval($payment->full_amount),
            'organization_sum' => $totalSumFromOrganizations,
            'tips' => floatval($tips_sum),
            'commission' => $commission,
            'organizartion' => $organizations,
            'orderPracticants' => $orderPracticants
        ];

//         dd($dealData);

        return $dealData;
//        }
    }

    private function prepareDistributeTips($order, $payment)
    {
        $orderPracticants = [];

        $rolespercentArray = [
            'admin' => $payment->admin_percentage,
            'master' => $payment->master_percentage,
            'employee' => $payment->staff_percentage,
            'organization' => $payment->organization_percentage
        ];

        // Получаем Мастера (предполагая, что он один из участников заказа)
        $master = $order->orderParticipants->firstWhere('role_id', Role::where('slug', 'master')->first()->id);
        $masterCardNumber = $master ? $master->user->encrypted_card_number : null;

        foreach ($order->orderParticipants as $orderParticipant) {
            $role = Role::find($orderParticipant->role_id);
            $roleSlug = $role->slug;
            $user = $orderParticipant->user;

            // Если карта отсутствует, используем карту Мастера
//            $cardNumber = $user->encrypted_card_number
//                ? $this->runScript($user->encrypted_card_number)
//                : ($masterCardNumber ? $this->runScript($masterCardNumber) : $this->runScript(Crypt::encryptString($order->organization->backup_card)));

            $orderPracticants[] = [
                'id' => $user->id,
                'roleId' => $role->id,
                'name' => $user->name ?? 'Иванов Иван Иванович',
                'phone' => $user->phone ?? '+7 999 999 99 99',
                'email' => $user->email ?? 'test@mail.ru',
                'card_number' => $user->encrypted_card_number ?? null,
                'role' => $roleSlug,
                'percent' => $rolespercentArray[$roleSlug],
            ];
        }

        if ($order->organization->organization_percentage > 0) {
            $orderPracticants[] = [
                'name' => 'Иванов Иван Иванович',
                'phone' => $order->organization->phone ?? '+7 999 999 99 99',
                'email' => $order->organization->email ?? 'test@mail.ru',
                'card_number' => $order->organization->backup_card,
                'role' => 'organization',
                'percent' => $rolespercentArray['organization'],
            ];
        }

        return $orderPracticants;
    }

    private function executeDeal($dealId)
    {
        $this->myLog('Исполняем сделку');

        $response = $this->tochkaService->executeDeal($dealId);

        if (isset($response['body']['result']['deal_id'])) {
            $this->myLog('Сделка успешно выполнена: ' . $response['body']['result']['deal_id']);
//            Log::info('Сделка успешно выполнена: ' . $response['body']['result']['deal_id']);
            return $response['body']['result']['deal_id'];
        } else {
            $this->myLog('Ошибка при выполнении сделки: ' . json_encode($response));
        }
    }

    private function getDeal($dealId)
    {
        $status = [
            'new' => 'новая сделка',
            'in_process' => 'сделка в процессе исполнения',
            'partial' => 'частичное исполнение сделки',
            'closed' => 'завершённая сделка',
            'rejected' => 'отменённая сделка',
            'correction' => 'по сделке остались отменённые платежи, нужно поправить реквизиты и снова их исполнить',
        ];

        $response = $this->tochkaService->getDeal($dealId);

        dump($response);
//        Log::info($response);

        if (isset($response['body']['result']['deal']['id'])) {
            $this->myLog('Сделка успешно получена: ' . $response['body']['result']['deal']['id']);
            $this->myLog('Статус сделки: ' . $status[$response['body']['result']['deal']['status']]);
        }
    }

    private function hasAvailablePayments(array $payments): bool
    {
        return isset($payments['body']['result']['payments']) && !empty($payments['body']['result']['payments']);
    }

    private function hasAvailablePaymentsAproved(array $payments): bool
    {
        return isset($payments) && !empty($payments);
    }


    private function checkBenificiary($organization)
    {
        try {
            $this->myLog('Ищем бенефициара по ИНН: ' . $organization->inn);
            $beneficiary = $this->tochkaService->listBeneficiarySearchToINN($organization->inn);
        } catch (\Exception $e) {
//            Log::error("Ошибка при поиске бенефициара: " . $e->getMessage());
            return null;
        }

        $beneficiaries = $beneficiary['body']['result']['beneficiaries'] ?? [];

        if (empty($beneficiaries)) {
            $this->myLog('Не знаем бенефициара');
            return $this->handleNewBeneficiary($organization);
        }

        $this->myLog('Знаем бенефициара');

        return $this->activateExistingBeneficiary($beneficiaries[0]);
    }

    private function activateExistingBeneficiary(array $beneficiary)
    {
        try {
            $this->myLog('Активируем бенефициара');

            $this->activateBeneficiary($beneficiary['id']);
        } catch (\Exception $e) {
//            Log::error("Error activating beneficiary: " . $e->getMessage());
            return null;
        }

        return $beneficiary['id'];
    }

    public function updateOtherDataUnidentifiedPayment($newData)
    {
        // Получаем текущие данные, если они есть, иначе используем пустой массив
        $currentData = $this->unidentifiedPayment->other_data ?? [];

        // Если $currentData является строкой, преобразуем её в массив
        if (is_string($currentData)) {
            $currentData = json_decode($currentData, true) ?? [];
        }

        // Обновляем данные, объединяя текущие и новые
        $this->unidentifiedPayment->update(
            [
                'other_data' => array_merge($currentData, $newData)
            ]
        );
    }

    private function handleNewBeneficiary($organization)
    {
        try {
            $beneficiaryId = $this->createBeneficiary($organization);

            $this->updateOtherDataUnidentifiedPayment($beneficiaryId);

            if ($beneficiaryId !== null) {
                $createVirtualAccount = $this->createVirtualAccount($beneficiaryId);
                $this->updateOtherDataUnidentifiedPayment($createVirtualAccount);

                $documentId = $this->uploadDocumentBeneficiary($beneficiaryId);
                $this->updateOtherDataUnidentifiedPayment($documentId);

                if (!$this->checkDocumentUpload($documentId)) {
                    $this->myLog("Загрузка документа для бенефициара {$beneficiaryId} не удалась.");
                    return null;
                }

                return $beneficiaryId;
            }

        } catch (\Exception $e) {
            $this->myLog("Ошибка при создании бенефициара: " . $e->getMessage());
            return null;
        }
    }


    private function createBeneficiary($organization)
    {
        $this->myLog('Начинаем создание нового бенефициара.');
        try {
            switch ($organization->form_id) {
                case 1:
                    $beneficiaryUl = $this->tochkaService->createBeneficiaryUl($organization);
                    break;
                case 2:
                    $beneficiaryUl = $this->tochkaService->createBeneficiaryIP($organization);
                    break;
                case 3:
                    $beneficiaryUl = $this->tochkaService->createBeneficiaryIP($organization);
                    break;
                default:
                    throw new \Exception('Неизвестный тип организации.');
            }


            if (empty($beneficiaryUl['body']['result']['beneficiary']['id'])) {
                throw new \Exception('Создание бенефициара не вернуло идентификатор.');
            }

            $beneficiaryId = $beneficiaryUl['body']['result']['beneficiary']['id'];
            $this->myLog('Бенефициар успешно создан с ID: ' . $beneficiaryId);

            return $beneficiaryId;

        } catch (\Exception $e) {
            $this->myLog('Ошибка при создании бенефициара: ' . $e->getMessage());
            return null;
        }
    }


    private function activateBeneficiary($beneficiary_id)
    {
        return $this->tochkaService->activateBeneficiary($beneficiary_id);
    }

    private function getVirtualAccount(mixed $id)
    {
        $virtualAccount = $this->tochkaService->listVirtualAccount($id);

        if (empty($virtualAccount['body']['result']['virtual_accounts'])) {
            $response = $this->createVirtualAccount($id);
            return $response['body']['result']['virtual_account'];
        }

        return $virtualAccount['body']['result']['virtual_accounts'][0];
    }

    private function createVirtualAccount(string $beneficiaryId)
    {
        try {
            $this->myLog("Создание виртуального счета для бенефициара с ID: " . $beneficiaryId);
            $response = $this->tochkaService->createVirtualAccount($beneficiaryId);

            if (empty($response['body']['result']['virtual_account'])) {
                throw new \Exception('Создание виртуального счета не вернуло идентификатор.');
            }

            $this->myLog('Виртуальный счет успешно создан с ID: ' . $response['body']['result']['virtual_account']);

            return $response;
        } catch (\Exception $e) {
            $this->myLog("Ошибка при создании виртуального счета для бенефициара {$beneficiaryId}: " . $e->getMessage());
            throw $e;
        }
    }

    private function uploadDocuments(string $dealId, ?float $tips, string $beneficiaryId)
    {
        $this->uploadDocumentDeal($beneficiaryId, $dealId, $this->getCommissionDocument(), $this->prepareIidentifyPayments['owners'][0]['agencyAgreementDate'], $this->prepareIidentifyPayments['owners'][0]['agencyAgreementNumber']);

        $this->uploadDocumentDeal($beneficiaryId, $dealId, $this->getPaymentContractDocument(), $this->prepareIidentifyPayments['owners'][0]['agencyAgreementDate'], $this->prepareIidentifyPayments['owners'][0]['agencyAgreementNumber']);

        if ($tips) {
            $distributeTips = $this->distributeTips($this->prepareIidentifyPayments['owners'][0]);

            foreach ($distributeTips as $key => $value) {
                $this->uploadDocumentDeal($beneficiaryId, $dealId, $this->getPaymentContractToCardDocument($value['amount'], $value['original']['name'], $value['original']['phone']), $this->prepareIidentifyPayments['owners'][0]['agencyAgreementDate'], $this->prepareIidentifyPayments['owners'][0]['agencyAgreementNumber']);
            }
        }
    }

    private function getCommissionDocument()
    {
        $fileName = 'commission_' . date('Y-m-d') . '_' . $this->prepareIidentifyPayments['owners'][0]['baseOrderId'] . '.pdf';
        $customerName = 'Клиент ' . $this->prepareIidentifyPayments['owners'][0]['payerName'];

        $data = [
            'id' => $this->prepareIidentifyPayments['owners'][0]['baseOrderId'],
            'customer' => $customerName,
            'executor' => 'ООО Ягода (ИНН0800024353)',
            'file_name' => $fileName,
            'items' => [
                [
                    'name' => 'Вознаграждение сервиса',
                    'quantity' => 1,
                    'unit_price' => $this->prepareIidentifyPayments['owners'][0]['organization']['commission'],
                    'total_price' => $this->prepareIidentifyPayments['owners'][0]['organization']['commission'],
                ]
            ]
        ];

        $this->createPdf->create($data);

        return $data['file_name'];
    }

    private function getPaymentContractDocument()
    {
        $fileName = 'payment_contract_' . date('Y-m-d') . '_' . $this->prepareIidentifyPayments['owners'][0]['baseOrderId'] . '.pdf';
        $customerName = 'Клиент ' . $this->prepareIidentifyPayments['owners'][0]['payerName'];

        $discount = $this->prepareIidentifyPayments['owners'][0]['orderDiscount'];

        foreach ($this->prepareIidentifyPayments['owners'][0]['orderItems'] as $product) {
            $discountAmount = $product['product_price'] * ((float) $discount / 100);
            $discountedPrice = $product['product_price'] - $discountAmount;
            $roundedPrice = $discountedPrice >= 0 ? floor($discountedPrice) : 0;
//            $totalAmount += $roundedPrice * $product['quantity'];

            $items[] = [
                "name" => $product['product_name'],
                "unit_price" => $roundedPrice,
                "quantity" => $product['quantity'],
                "total_price" => $roundedPrice * $product['quantity'],
            ];
        }

        $data = [
            'id' => $this->prepareIidentifyPayments['owners'][0]['baseOrderId'] . '_2',
            'customer' => $customerName,
            'executor' => 'ООО Ягода (ИНН0800024353)',
            'file_name' => $fileName,
            'items' => $items
        ];

        $this->createPdf->create($data);

        return $data['file_name'];
    }

    private function getPaymentContractToCardDocument($tipsAmount, $emloyeeName, $emloyeePhone)
    {
        $microtime = microtime(true);
        $milliseconds = round(($microtime - floor($microtime)) * 1000);
        $formattedDate = date('Y-m-d H:i:s', $microtime) . '-' . str_pad($milliseconds, 3, '0', STR_PAD_LEFT);

        $recipients = [];
        $fileName = 'payment_contract_to_card_' . $formattedDate . '_' . $this->prepareIidentifyPayments['owners'][0]['baseOrderId'] . '.pdf';
        $customerName = 'Клиент ' . $this->prepareIidentifyPayments['owners'][0]['payerName'];
        $executorName = 'Сотрудник ' . $emloyeeName . ' ' . $emloyeePhone;


        $recipients[] = [
            'name' => 'Вознаграждение за работу (Чаевые). Дарение.',
            'quantity' => 1,
            'unit_price' => $tipsAmount,
            'total_price' => $tipsAmount,
        ];

        $data = [
            'id' => $this->prepareIidentifyPayments['owners'][0]['baseOrderId'] . '_3',
            'customer' => $customerName,
            'executor' => $executorName,
            'file_name' => $fileName,
            'items' => $recipients
        ];

        $this->createPdf->create($data);

        return $data['file_name'];
    }

    private function uploadDocumentBeneficiary(string $beneficiaryId)
    {
        try {
            $this->myLog("Загрузка документа для бенефициара: " . $beneficiaryId);
            $response = $this->tochkaService->uploadDocumentBeneficiary($beneficiaryId);

            if (empty($response['body']['document_id'])) {
                throw new \Exception('Создание документа не вернуло идентификатор.');
            }

            return $response['body']['document_id'];
        } catch (\Exception $e) {
            $this->myLog("Ошибка при загрузке документа для бенефициара: {$beneficiaryId}: " . $e->getMessage());
            throw $e;
        }
    }

    private function uploadDocumentDeal(string $beneficiaryId, $dealId, $documentPath, $document_date, $document_number)
    {
        try {
            $this->myLog("Загрузка документа для сделки с ID бенефициара: " . $beneficiaryId);
            $response = $this->tochkaService->uploadDocumentDeal($beneficiaryId, $dealId, $documentPath, $document_date, $document_number);

//            Log::info($documentPath);

            dump($response);

            if (empty($response['body'])) {
                throw new \Exception('Создание документа по сдлеки не вернуло идентификатор.');
            }

            return $response['body']['document_id'];
        } catch (\Exception $e) {
            $this->myLog("Ошибка при загрузке документа для сделки с ID бенефициара: {$beneficiaryId}: " . $e->getMessage());
            throw $e;
        }
    }

    private function checkDocumentUpload(string $documentId): bool
    {
        try {
            $this->myLog("Проверка загрузки документа с ID документа: " . $documentId);
            $response = $this->tochkaService->getDocument($documentId);

            if (empty($response['body']['result']['document']) && $response['body']['result']['document']['success_added']) {
                throw new \Exception('Проверка загрузки документа не вернула идентификатор.');
            }

            $this->myLog("Проверка показала, что документ загружен: {$documentId}: ");

            return true;
        } catch (\Exception $e) {
            $this->myLog("Ошибка при проверке загрузки документа с ID документа: {$documentId}: " . $e->getMessage());
            return false;
        }
    }

    private function getPaymentOperationListSbp()
    {
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $subDays = Carbon::now()->subDays(1)->format('Y-m-d');

        $data = [
            'customerCode' => '304744832',
            'status' => 'APPROVED',
            'fromDate' => $subDays,
            'toDate' => $today,
        ];

        return $this->tochkaApiService->getPaymentOperationList($data);
    }

    private function getPaymentOperationListCard()
    {
        // Получаем сегодняшнюю дату
        $today = Carbon::now()->format('Y-m-d');

        // Получаем дату два дня назад
        $twoDaysAgo = Carbon::now()->subDays(2)->format('Y-m-d');

        $data = [
            'customerCode' => '304744832',
            'status' => 'APPROVED',
            'fromDate' => $twoDaysAgo, // Дата два дня назад
            'toDate' => $today,        // Сегодняшняя дата
        ];

        return $this->tochkaApiService->getPaymentOperationList($data);
    }

    private function filteredPaymentsCard()
    {
        $data = $this->getPaymentOperationListCard();

        $orderFixService = new OrderFixService();

        $processedOrderIds = $orderFixService->processOrders($this->calculatePaymentDetailsArray['total']);
        $this->processedOrderIds = $processedOrderIds;

        $filteredData = array_filter($data['Data']['Operation'], function ($item) use ($processedOrderIds) {
            // Извлекаем order_id из redirectUrl
            preg_match('/order_id=(\d+)/', $item['redirectUrl'], $matches);
            $orderId = isset($matches[1]) ? (int)$matches[1] : null;

            // Проверяем, есть ли orderId в массиве $array
            return in_array($orderId, $processedOrderIds);
        });

        // Преобразуем результат обратно в индексированный
        $filteredData = array_values($filteredData);

        // Проверяем, что все $processedOrderIds есть в $filteredData
        $missingOrderIds = array_diff($processedOrderIds, $this->extractOrderIdsFromData($filteredData));

        if (!empty($missingOrderIds)) {
            $this->telegram->sendMessage('802964868', "Следующие order_id отсутствуют в данных: " . implode(', ', $missingOrderIds) . '. Ждем остальные данные.');
            // Если есть недостающие order_id, выбрасываем исключение или логируем ошибку
            throw new \RuntimeException("Следующие order_id отсутствуют в данных: " . implode(', ', $missingOrderIds));
        }

        // Вывод результата
        return $filteredData;
    }

    /**
     * Извлекает все order_id из отфильтрованных данных.
     *
     * @param array $filteredData Отфильтрованные данные
     * @return array Массив order_id
     */
    private function extractOrderIdsFromData(array $filteredData): array
    {
        $orderIds = [];

        foreach ($filteredData as $item) {
            preg_match('/order_id=(\d+)/', $item['redirectUrl'], $matches);
            if (isset($matches[1])) {
                $orderIds[] = (int)$matches[1];
            }
        }

        return $orderIds;
    }

    // private function filteredPaymentsCard()
    // {
    //     $data = $this->getPaymentOperationListCard();

    //     // Получаем вчерашнюю дату (начало и конец дня)
    //     $startDate = Carbon::yesterday()->startOfDay(); // Начало вчерашнего дня (00:00:00)
    //     $endDate = Carbon::yesterday()->endOfDay();     // Конец вчерашнего дня (23:59:59)

    //     $orders = collect($data["Data"]['Operation'])->filter(function($order) use ($startDate, $endDate) {
    //         // Приводим дату заказа к UTC и проверяем, попадает ли она в диапазон вчерашнего дня
    //         $createdAt = Carbon::parse($order['createdAt'])->setTimezone('UTC');

    //         return $createdAt->between($startDate, $endDate) && in_array('card', $order['paymentMode']);
    //     })->values();

    //     return $orders->toArray();
    // }

    private function filteredPaymentsSbp()
    {
        $data = $this->getPaymentOperationListSbp();

        return $data["Data"]['Operation'];

        $startDate = Carbon::today()->startOfDay(); // Начало сегодняшнего дня (00:00:00)
        $endDate = Carbon::today()->endOfDay();     // Конец сегодняшнего дня (23:59:59)

        $orders = collect($data["Data"]['Operation'])->filter(function($order) use ($startDate, $endDate) {
            // Приводим дату заказа к UTC и проверяем, попадает ли она в диапазон сегодняшнего дня
            // $createdAt = Carbon::parse($order['createdAt'])->setTimezone('UTC');
            return;

            // return $createdAt->between($startDate, $endDate) && in_array('sbp', $order['paymentMode']);
        })->values();

        return $orders->toArray();
    }

    public function runScript($card)
    {
        $decryptCard = Crypt::decryptString($card);

        $cleanedString = str_replace(" ", "", $decryptCard);

        $scriptPath = public_path('encrypt.py');

        $string =  shell_exec("python3 $scriptPath " . escapeshellarg($cleanedString) . " 2>&1");

        return str_replace("\n", "", $string);
    }

    private function myLog(string $message, $sleep = 0)
    {
        dump($message);
        sleep($sleep);
    }

    private function myLogDd(string $message, $sleep = 2)
    {
        dump($message);
        sleep($sleep);
        dd();
    }

    /**
     * Рассчитывает детали платежа, включая комиссию, процент и общую сумму.
     *
     * @return array Ассоциативный массив с данными о комиссии, проценте и общей сумме
     * @throws \Exception Если не удалось извлечь сумму комиссии из назначения платежа
     */
    private function calculatePaymentDetails()
    {
        $payment = $this->unidentifiedPaymentInfo;

        $pattern = '/Сумма комиссии\s*(\d+\.\d{1,2})/';
        $isMatched = preg_match($pattern, $payment['purpose'], $matches);

        if (!$isMatched || !isset($matches[1])) {
            throw new \Exception("Не удалось извлечь сумму комиссии из назначения платежа: {$payment['purpose']}");
        }

        $commission = (float) $matches[1];

        $total = $payment['amount'] + $commission;

        $percentage = round(($commission / $total) * 100, 1);

        $array = [
            'commission' => $commission,
            'percentage' => $percentage,
            'total' => $total,
        ];

        $this->calculatePaymentDetailsArray = $array;

        return $array;
    }
}
