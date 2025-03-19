<?php

namespace App\Services\TochkaPaymentProcessor;

use App\Models\Bill;
use App\Models\Role;
use App\Models\Setting;
use App\Models\UnidentifiedPayment;
use App\Models\OrderStatistics;
use App\Modules\YagodaTips\Models\OrderStatistics as OrderStatisticsTips;
use App\Models\Order;
use App\Services\TochkaPaymentProcessor\YagodaTipsPayments\TipsPaymentProcessor;
use App\Services\TochkaService;
use App\Services\TochkaService\CommissionCalculator;
use App\Services\TelegramService;
use App\Services\TochkaApiService;
use app\Services\TochkaService\OrderFixService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentProcessor
{
    private $tochkaService;
    private $commissionCalculator;
    private $telegram;
    private $tochkaApiService;
    private $beneficiaryService;
    private $sbp_payment_fee_percent;
    private $unidentifiedPayment;
    private $unidentifiedPaymentInfo = [];
    public $paymentsAprovedList = []; // Сделан публичным для передачи в DealService
    public $prepareIidentifyPayments = []; // Сделан публичным для передачи в DealService
    private $processedOrderIds = [];
    private $calculatePaymentDetailsArray = [];
    public $processPaymentTest = false;

    public function __construct(
        TochkaService $tochkaService,
        CommissionCalculator $commissionCalculator,
        TelegramService $telegram,
        TochkaApiService $tochkaApiService,
        BeneficiaryService $beneficiaryService
    ) {
        $this->tochkaService = $tochkaService;
        $this->commissionCalculator = $commissionCalculator;
        $this->telegram = $telegram;
        $this->tochkaApiService = $tochkaApiService;
        $this->beneficiaryService = $beneficiaryService;
        $this->calculatePaymentDetailsArray = [
            "commission" => 0,
            "percentage" => 0,
            "total" => 0
        ];
    }

    public function processPaymentsDetailsTest($paymentId, $paymentType)
    {
        $this->processPaymentTest = true;

        try {
            $unidentifiedPayment = UnidentifiedPayment::find($paymentId);

            if (!$unidentifiedPayment) {
                throw new \Exception("Unidentified payment not found.");
            }

            $this->unidentifiedPayment = $unidentifiedPayment;
            $this->unidentifiedPaymentInfo = $unidentifiedPayment->details;

            switch ($paymentType) {
                case 'sbp':
                    $this->processIdentificationSbp();
                    break;

                case 'card':
                    $this->processIdentificationCard();
                    break;

                default:
                    throw new \Exception("Unknown payment type.");
            }

        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    public function processForUnidentifiedPayments()
    {
        dump('Проверка наличия неидентифицированных денег на н/с');

        try {
            $payments = $this->tochkaService->listPayments();
        } catch (\Exception $e) {
            Log::error('Ошибка при получении платежей: ' . $e->getMessage());
            return;
        }

        if ($this->hasAvailablePayments($payments)) {
            $payments = $payments['body']['result']['payments'] ?? [];
            dump('Есть платежи в ответе: ' . count($payments) . ' шт.');
            $this->processPaymentsDetails($payments);
        } else {
            dump('Нет доступных платежей');
        }
    }

    private function processPaymentsDetails(array $payments): void
    {
        foreach ($payments as $payment) {
            try {
                $paymentInfo = $this->getPaymentInfo($payment);
                $this->unidentifiedPaymentInfo = $paymentInfo;

                $this->unidentifiedPayment = UnidentifiedPayment::firstOrCreate(
                    ['payment_id' => $payment],
                    ['type' => $paymentInfo['type'] ?? 'unknown', 'details' => $paymentInfo]
                );

                if (isset($paymentInfo['purpose']) && str_contains($paymentInfo['purpose'], 'Возврат средств')) {
                    $this->unidentifiedPayment->update(['type' => 'return']);
                    continue;
                }

                $this->processPaymentByType($paymentInfo);
            } catch (\Exception $e) {
                Log::error("Ошибка обработки платежа: " . $e->getMessage());
                continue;
            }
        }
    }

    /**
     * Обрабатывает платеж в зависимости от его типа и источника статистики.
     *
     * @param array $paymentInfo Информация о платеже
     * @return void
     */
    private function processPaymentByType(array $paymentInfo): void
    {
        $orderStatistic = $this->findOrderStatisticByPurpose($paymentInfo['purpose'] ?? '');

        if ($orderStatistic instanceof OrderStatisticsTips) {
            $tipsProcessor = new TipsPaymentProcessor();
            $tipsProcessor = $tipsProcessor->process($paymentInfo, $orderStatistic);
            $this->prepareIidentifyPayments = $tipsProcessor;
            return;
        }

        switch ($paymentInfo['type'] ?? 'unknown') {
            case 'incoming_sbp':
                $this->telegram->sendMessage('802964868', 'Внимание! Новый платеж неразобранный (SBP).');
                $this->processIdentificationSbp();
                break;
            case 'incoming':
                $this->processIdentificationCard();
                break;
            default:
                Log::error("Неизвестный тип оплаты: " . ($paymentInfo['type'] ?? 'не указан'));
                break;
        }
    }

    /**
     * Находит статистику заказа по qrcId, извлечённому из назначения платежа.
     *
     * @param string $purpose Назначение платежа
     * @return array Массив с данными статистики и типом модели
     * @throws \Exception Если запись не найдена ни в OrderStatistics, ни в OrderStatisticsTips
     */
    private function findOrderStatisticByPurpose($purpose)
    {
        $qrcId = $this->extractQrCode($purpose);

        $orderStatistic = OrderStatistics::where('qrcId', $qrcId)->first();

        if ($orderStatistic) {
            return $orderStatistic;
        }

        $orderStatisticTips = OrderStatisticsTips::where('qrcId', $qrcId)->first();

        if ($orderStatisticTips) {
            return $orderStatisticTips;
        }

        throw new \Exception('Не удалось найти заказ в базе по qrcId: ' . $qrcId);
    }

    private function processIdentificationSbp()
    {
        $purpose = $this->unidentifiedPaymentInfo['purpose'];
        $qrcId = $this->extractQrCode($purpose);

        $orderStatistic = OrderStatistics::where('qrcId', $qrcId)->first();

        if (!$orderStatistic) {
            dd('Не удалось найти заказ в базе по qrcId: ' . $qrcId);
            return;
        }

        $operations = $this->processPaymentsApprovedSbp();
        $sbpPayments = array_filter($operations, fn($operation) => $operation['purpose'] === $orderStatistic->uuid);
        $this->paymentsAprovedList = array_values($sbpPayments);

        $this->updateOtherDataUnidentifiedPayment($this->paymentsAprovedList);
        $this->distributionMoneyAmongVirtualAccounts();
    }

    private function processIdentificationCard()
    {
        $this->calculatePaymentDetails();
        $this->paymentsAprovedList = $this->filteredPaymentsCard();

        $this->updateOtherDataUnidentifiedPayment($this->paymentsAprovedList);

        if (empty($this->paymentsAprovedList)) {
            dump('Нет одобренных платежей для обработки.');
            return;
        }

        $this->distributionMoneyAmongVirtualAccounts();
    }

    private function processPaymentsApprovedSbp()
    {
        dump('Получаем список оплаченных заказов (SBP)');

        try {
            $paymentsAproved = $this->filteredPaymentsSbp();
        } catch (\Exception $e) {
            Log::error('Ошибка при получении оплаченных заказов (SBP): ' . $e->getMessage());
            return [];
        }

        if ($this->hasAvailablePaymentsApproved($paymentsAproved)) {
            $this->paymentsAprovedList = $paymentsAproved ?? [];
            dump('Есть оплаченные заказы в ответе: ' . count($this->paymentsAprovedList) . ' шт.');
            dump('Сумма оплаченных заказов: ' . array_sum(array_column($this->paymentsAprovedList, 'amount')));
        } else {
            dump('Нет оплаченных заказов (SBP).');
        }

        return $this->paymentsAprovedList;
    }

    private function distributionMoneyAmongVirtualAccounts()
    {
        $this->prepareIidentifyPayments = [
            "payment_id" => $this->unidentifiedPaymentInfo['id'],
            "amount" => $this->unidentifiedPaymentInfo['amount'],
            "amount_to_credit" => $this->unidentifiedPaymentInfo['amount'],
            'owners' => []
        ];

        foreach ($this->paymentsAprovedList as $payment) {
            $virtualAccountData = $this->getVirtualAccountDataForPayment($payment);
            if ($virtualAccountData) {
                $this->prepareIidentifyPayments['owners'][] = $virtualAccountData;
            }
        }

        $preparedPayments = $this->prepareIdentifyPayments($this->prepareIidentifyPayments);

        if (!$this->processPaymentTest) {
            $this->identifyPayment($preparedPayments);
        }
    }

    private function getVirtualAccountDataForPayment($payment)
    {
        $orderStatistic = OrderStatistics::where('uuid', $payment['purpose'])->first();
        if (!$orderStatistic) {
            Log::error('Не удалось получить статистику для оплаченного заказа: ' . $payment['purpose']);
            return null;
        }

        $order = $orderStatistic->order;
        $beneficiaryId = $this->beneficiaryService->checkBeneficiary($order->organization);
        $virtualAccount = $this->beneficiaryService->getVirtualAccount($beneficiaryId);

        return [
            "virtual_account" => $virtualAccount,
            "amount" => (float)$payment['amount'],
            "organization" => $this->orderPracticants($orderStatistic),
            "beneficiaryId" => $beneficiaryId,
            "paymentMode" => $payment['paymentMode'][0] ?? 'unknown',
            "processPaymentTest" => $this->processPaymentTest,
            "baseOrderId" => $order->id,
            "orderItems" => $order->orderItems->toArray(),
            "orderDiscount" => $order->discount ?? 0,
            "organizationId" => $order->organization->id,
            "organizationEmail" => $order->organization->email,
            "customerOrganizaionName" => $order->organization->name,
            "customerInn" => $order->organization->inn,
            "agencyAgreementDate" => $order->organization->agency_agreement_date,
            "agencyAgreementNumber" => $order->organization->agency_agreement_number,
            "unidentifiedPayment" => $this->unidentifiedPayment
        ];
    }

    private function prepareIdentifyPayments($identifyPayments)
    {
        $data = ["payment_id" => $identifyPayments['payment_id']];
        foreach ($identifyPayments['owners'] as $identifyPayment) {
            $amountToCredit = $this->commissionCalculator->calculateCommission(
                $identifyPayment['amount'],
                $identifyPayment['paymentMode'],
                $this->calculatePaymentDetailsArray['percentage']
            );
            $data['owners'][] = [
                'virtual_account' => $identifyPayment['virtual_account'],
                'amount' => $amountToCredit['amount_to_credit'],
            ];
        }
        return $this->checkAmount($data);
    }

    private function checkAmount($data)
    {
        $targetAmount = $this->prepareIidentifyPayments['amount'];
        $totalAmount = array_sum(array_column($data['owners'], 'amount'));

        $difference = round($targetAmount - $totalAmount, 2);
        if ($difference != 0) {
            $index = $difference > 0 ?
                array_search(min(array_column($data['owners'], 'amount')), array_column($data['owners'], 'amount')) :
                array_search(max(array_column($data['owners'], 'amount')), array_column($data['owners'], 'amount'));
            $data['owners'][$index]['amount'] += $difference > 0 ? $difference : -$difference;
        }
        return $data;
    }

    private function identifyPayment(array $identifyPayment)
    {
        $this->unidentifiedPayment->update(['unidentified_payments_prepare_data' => $identifyPayment]);

        try {
            dump('Зачисляем деньги на виртуальный счет бенефициара');
            $response = $this->tochkaService->identificationPayment($identifyPayment);
            $this->unidentifiedPayment->update([
                'status' => 'identification_payment_success',
                'identification_payment' => $response
            ]);
            OrderStatistics::whereIn('order_id', $this->processedOrderIds)->update(['processed_at' => Carbon::now()]);
            return $response;
        } catch (\Exception $e) {
            $this->unidentifiedPayment->update([
                'status' => 'identification_payment_error',
                'identification_payment' => $e->getMessage()
            ]);
            Log::error('Ошибка при зачислении денег: ' . $e->getMessage());
            return [];
        }
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

    private function extractQrCode($inputString) {
        preg_match('/[A-Z0-9]{8,40}/', $inputString, $matches);
        return $matches[0] ?? null;
    }

    private function getPaymentInfo(string $payment): array {
        try {
            dump('Запрос информации о платеже: ' . $payment);
            $response = $this->tochkaService->getPayment($payment);
            return $response['body']['result']['payment'] ?? [];
        } catch (\Exception $e) {
            Log::error('Ошибка при получении информации о платеже ' . $payment . ': ' . $e->getMessage());
            return [];
        }
    }

    private function hasAvailablePayments(array $payments): bool {
        return !empty($payments['body']['result']['payments']);
    }

    private function hasAvailablePaymentsApproved(array $payments): bool {
        return !empty($payments);
    }

    private function updateOtherDataUnidentifiedPayment($newData)
    {
        $currentData = is_string($this->unidentifiedPayment->other_data) ?
            json_decode($this->unidentifiedPayment->other_data, true) ?? [] :
            $this->unidentifiedPayment->other_data ?? [];

        $this->unidentifiedPayment->update(['other_data' => array_merge($currentData, $newData)]);
    }

    private function filteredPaymentsCard()
    {
        $data = $this->getPaymentOperationListCard();

        $orderFixService = new OrderFixService();
        $processedOrderIds = $orderFixService->processOrders($this->calculatePaymentDetailsArray['total']);
        $this->processedOrderIds = $processedOrderIds;

        $filteredData = array_filter($data['Data']['Operation'], function ($item) use ($processedOrderIds) {
            preg_match('/order_id=(\d+)/', $item['redirectUrl'], $matches);
            $orderId = $matches[1] ?? null;
            return in_array($orderId, $processedOrderIds);
        });

        $filteredData = array_values($filteredData);

        $missingOrderIds = array_diff($processedOrderIds, $this->extractOrderIdsFromData($filteredData));
        if (!empty($missingOrderIds)) {
            $this->telegram->sendMessage('802964868', "Следующие order_id отсутствуют в данных: " . implode(', ', $missingOrderIds) . '. Ждем остальные данные.');
            throw new \RuntimeException("Следующие order_id отсутствуют в данных: " . implode(', ', $missingOrderIds));
        }

        return $filteredData;
    }

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

    private function filteredPaymentsSbp()
    {
        $data = $this->getPaymentOperationListSbp();

        return $data["Data"]['Operation'];
    }

    private function getPaymentOperationListCard()
    {
        $today = Carbon::now()->format('Y-m-d');
        $twoDaysAgo = Carbon::now()->subDays(2)->format('Y-m-d');

        $data = [
            'customerCode' => '304744832',
            'status' => 'APPROVED',
            'fromDate' => $twoDaysAgo,
            'toDate' => $today,
        ];

        return $this->tochkaApiService->getPaymentOperationList($data);
    }

    private function getPaymentOperationListSbp()
    {
        $today = Carbon::now()->format('Y-m-d');
        $subDays = Carbon::now()->subDays(1)->format('Y-m-d');

        $data = [
            'customerCode' => '304744832',
            'status' => 'APPROVED',
            'fromDate' => $subDays,
            'toDate' => $today,
        ];

        return $this->tochkaApiService->getPaymentOperationList($data);
    }

    private function orderPracticants($payment)
    {
//        if ($payment->order_id == 1971) {
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

        // dd($dealData);

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

    private function orderParticipants($orderStatistic)
    {
        $order = Order::find($orderStatistic->order_id);
        if (!$order) {
            Log::error('Заказ не найден для order_id: ' . $orderStatistic->order_id);
            return [];
        }

        return [
            'organizationId' => $order->organization->id,
            'commission' => $this->calculatePaymentDetailsArray['commission'],
            'tips' => 0, // Чаевые пока не реализованы, можно добавить позже
            'organizartion' => [
                [
                    'sum' => $this->calculatePaymentDetailsArray['total'],
                    'account' => $order->organization->account_number ?? 'unknown',
                    'bank_code' => $order->organization->bank_code ?? 'unknown',
                    'name' => $order->organization->name,
                    'inn' => $order->organization->inn,
                    'kpp' => $order->organization->kpp ?? null,
                ]
            ],
            'customerOrganizaionName' => $order->organization->name,
            'customerInn' => $order->organization->inn,
            'baseOrderId' => $order->id,
            'orderItems' => $order->items->map(fn($item) => [
                'product_name' => $item->name,
                'product_price' => $item->price,
                'quantity' => $item->quantity,
            ])->toArray(),
            'orderDiscount' => $order->discount ?? 0,
            'agencyAgreementDate' => $order->agreement_date ?? Carbon::now()->format('Y-m-d'),
            'agencyAgreementNumber' => $order->agreement_number ?? 'AG-' . $order->id,
        ];
    }
}
