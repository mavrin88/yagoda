<?php

namespace App\Services\TochkaPaymentProcessor\YagodaTipsPayments;

use App\Models\Role;
use App\Models\UnidentifiedPayment;
use App\Modules\YagodaTips\Models\OrderStatistics as OrderStatisticsTips;
use App\Services\TochkaApiService;
use App\Services\TochkaPaymentProcessor\BeneficiaryService;
use App\Services\TochkaService;
use App\Services\TochkaService\CommissionCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TipsPaymentProcessor
{
    private $tochkaApiService;
    private $tochkaService;
    private $paymentsAprovedList;
    private $unidentifiedPaymentInfo;
    private $unidentifiedPayment;
    private $orderStatistic;
    private $prepareIidentifyPayments;
    private $processPaymentTest = true;
    private $commissionCalculator;
    private $virtualAccount = '62decc01-1593-4bbe-9798-bc0156c76d7a';
    private $processedOrderIds = [];

    public function __construct()
    {
        $this->tochkaApiService = new TochkaApiService();
        $this->commissionCalculator = new CommissionCalculator();
        $this->tochkaService = new TochkaService();

        $this->calculatePaymentDetailsArray = [
            "commission" => 0,
            "percentage" => 0,
            "total" => 0
        ];
    }

    public function process(array $paymentInfo, OrderStatisticsTips $orderStatistic)
    {
        try {
            $this->unidentifiedPaymentInfo = $paymentInfo;
            $this->orderStatistic = $orderStatistic;

            $this->unidentifiedPayment = UnidentifiedPayment::firstOrCreate(
                ['payment_id' => $paymentInfo['id']],
                ['type' => $paymentInfo['type'] ?? 'unknown', 'details' => $paymentInfo]
            );

            if (isset($paymentInfo['purpose']) && str_contains($paymentInfo['purpose'], 'Возврат средств')) {
                return $this->unidentifiedPayment->update(['type' => 'return']);
            }

            $this->processPaymentByType();

            return $this->prepareIidentifyPayments;
        } catch (\Exception $e) {
            Log::error("Ошибка обработки платежа: " . $e->getMessage());
        }
    }


    private function processPaymentByType()
    {
        switch ($this->unidentifiedPaymentInfo['type'] ?? 'unknown') {
            case 'incoming_sbp':
                $this->processIdentificationSbp();
                break;
            case 'incoming':
//                $this->processIdentificationCard();
                break;
            default:
                Log::error("Неизвестный тип оплаты: " . ($this->unidentifiedPaymentInfo['type'] ?? 'не указан'));
                break;
        }
    }

    private function processIdentificationSbp()
    {
        $operations = $this->processPaymentsApprovedSbp();

        $sbpPayments = array_filter($operations, fn($operation) => $operation['purpose'] === $this->orderStatistic->uuid);
        $this->paymentsAprovedList = array_values($sbpPayments);


        $this->updateOtherDataUnidentifiedPayment($this->paymentsAprovedList);
        $this->distributionMoneyAmongVirtualAccounts();
    }

    private function updateOtherDataUnidentifiedPayment($paymentsAprovedList)
    {
        dump('Обновляем данные о платеже (SBP).');
    }

    private function distributionMoneyAmongVirtualAccounts()
    {
        $this->prepareIidentifyPayments = [
            "payment_id" => $this->unidentifiedPaymentInfo['id'],
            "orderStatisticsTips" => true,
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
                'virtual_account' => $this->virtualAccount,
                'amount' => $amountToCredit['amount_to_credit'],
            ];
        }
        return $this->checkAmount($data);
    }

    private function identifyPayment(array $identifyPayment)
    {
        $this->unidentifiedPayment->update(
            [
                'unidentified_payments_prepare_data' => $identifyPayment
            ]
        );

        try {
            dump('Зачисляем деньги на вирт. счёт бенефициара, т.е. идентифицируем их');

            $response = $this->tochkaService->identificationPayment($identifyPayment);

            $this->unidentifiedPayment->update(
                [
                    'status' => 'identification_payment_success',
                    'identification_payment' => $response
                ]
            );

            OrderStatisticsTips::whereIn('order_id', $this->processedOrderIds)->update(['processed_at' => Carbon::now()]);


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

    private function getVirtualAccountDataForPayment($payment)
    {
        $order = $this->orderStatistic->order;

        return [
            'virtual_account' => $this->virtualAccount,
            "amount" => (float)$payment['amount'],
            "organization" => $this->orderPracticants(),
            "paymentMode" => $payment['paymentMode'][0] ?? 'unknown',
            "payerName" => $this->orderStatistic->payer_name,
            "processPaymentTest" => $this->processPaymentTest,
            "baseOrderId" => $order->id,
            "groupId" => $order->group_id,
            "unidentifiedPayment" => $this->unidentifiedPayment
        ];
    }

    private function orderPracticants()
    {
        $tips_sum = $this->orderStatistic->tips_sum;
        $commission = $this->orderStatistic->service_acquiring_commission;

        $orderPracticants[] = $this->prepareDistributeTips();

        if ($this->orderStatistic->fee_consent > 0) {
            $tips_sum = $tips_sum - $this->orderStatistic->fee_consent;
            $commission = $this->orderStatistic->fee_consent;
        }

        $data = [
            'total' => floatval($this->orderStatistic->full_amount),
            'tips' => floatval($tips_sum),
            'commission' => $commission,
            'orderPracticants' => $orderPracticants
        ];

        return $data;
    }

    private function prepareDistributeTips()
    {
        $orderPracticants = [];

        $rolespercentArray = [
            'admin' => $this->orderStatistic->admin_percentage,
            'master' => $this->orderStatistic->master_percentage,
            'employee' => $this->orderStatistic->staff_percentage,
        ];

        foreach ($this->orderStatistic->order->orderParticipants as $orderParticipant) {
            $role = Role::find($orderParticipant->role_id);
            $roleSlug = $role->slug;
            $user = $orderParticipant->user;

            $orderPracticants[] = [
                'id' => $user->id,
                'roleId' => $role->id,
                'name' => !empty($user->encrypted_first_name) ? Crypt::decryptString($user->encrypted_first_name) : null,
                'phone' => $user->phone,
                'email' => $user->email,
                'card_number' => $user->encrypted_card_number ?? null,
                'role' => $roleSlug,
                'percent' => $rolespercentArray[$roleSlug],
            ];
        }

        if ($this->orderStatistic->organization_percentage > 0) {
            $orderPracticants[] = [
                'id' => null,
                'roleId' => null,
                'name' => '',
                'phone' => $this->orderStatistic->order->group->contact_phone,
                'email' => $this->orderStatistic->order->group->email,
                'card_number' => $this->orderStatistic->order->group->backup_card,
                'role' => 'organization',
                'percent' => $this->orderStatistic->organization_percentage
            ];
        }

        return $orderPracticants;
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

        if (!empty($paymentsAproved)) {
            dump('Есть оплаченные заказы в ответе: ' . count($paymentsAproved) . ' шт.');
        } else {
            dump('Нет оплаченных заказов (SBP).');
        }

        return $paymentsAproved;
    }

    private function filteredPaymentsSbp()
    {
        $data = $this->getPaymentOperationListSbp();

        return $data["Data"]['Operation'];
    }

    private function getPaymentOperationListSbp()
    {
        return [
            "Data" => [
                "Operation" => [
                    [
                        "customerCode" => "304744832",
                        "paymentType" => "sbp",
                        "paymentId" => "A50771344379321A0000010011470101",
                        "transactionId" => "58618de4-09ff-b902-a574-f8f5540522f7",
                        "createdAt" => "2025-03-18T18:44:15+05:00",
                        "paymentMode" => [
                            0 => "sbp"
                        ],
                        "redirectUrl" => "https://yagoda.team/PaySuccess?tips_order_id=20",
                        "failRedirectUrl" => "https://example.com/fail",
                        "purpose" => "tips_208bdc8b-47d4-4f05-acf2-5bc9164",
                        "amount" => 107.0,
                        "status" => "APPROVED",
                        "operationId" => "e49353e9-6833-47ad-86e5-64bbb61e7bf1",
                        "paymentLink" => "https://merch.tochka.com/order/?uuid=e49353e9-6833-47ad-86e5-64bbb61e7bf1",
                        "consumerId" => "tips_b712c979-97bf-4a14-8f22-60327c7",
                    ]
                ]
            ],
            "Links" => [
                "self" => "https://enter.tochka.com/uapi/acquiring/v1.0/payments?customerCode=304744832&status=APPROVED&fromDate=2025-03-16&toDate=2025-03-17",
                "first" => "https://enter.tochka.com/uapi/acquiring/v1.0/payments?customerCode=304744832&status=APPROVED&fromDate=2025-03-16&toDate=2025-03-17&page=1",
                "last" => "https://enter.tochka.com/uapi/acquiring/v1.0/payments?customerCode=304744832&status=APPROVED&fromDate=2025-03-16&toDate=2025-03-17&page=1",
            ],
            "Meta" => [
                "totalPages" => 1
            ]
        ];

        $today = Carbon::now()->format('Y-m-d');
        $subDays = Carbon::now()->subDays(4)->format('Y-m-d');

        $data = [
            'customerCode' => '304744832',
            'status' => 'APPROVED',
            'fromDate' => $subDays,
            'toDate' => $today,
        ];

        return $this->tochkaApiService->getPaymentOperationList($data);
    }
}
