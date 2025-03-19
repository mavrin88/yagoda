<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatistics;
use App\Models\Setting;

class OrderService
{
    function fixed($number, $decimals = 2, $noCommas = false)
    {
        $formattedNumber = number_format($number, $decimals, '.', $noCommas ? '' : ',');

        return $formattedNumber;
    }

    public function calculateOrderStatistics($orderId)
    {
        $orderStatistic = OrderStatistics::where('order_id', $orderId)->where('status', 'ok')->first();
        $order = Order::find($orderId);

        if (!$orderStatistic) {
            return null;
        }

        $compensationPercent = Setting::where('key', 'acquiring_fee')->first()->value; // Компенсация Yagoda за эквайринг
        $accrualForService = ($orderStatistic->total_amount + $orderStatistic->tips_sum) * ($orderStatistic->service_commission / 100); // Начисление Yagoda за сервис
        $itog = $orderStatistic->total_amount + $orderStatistic->tips_sum + $accrualForService; // Итого сумма к оплате Клиентом (С галкой)
        $bankCommissionAcquiringPercent = 0.70;
        $licenseFeeOfTheBankDotPercent = 0.30; // Лицензионное вознагр. банка Точка (%)
        $bankCommissionForTransferringTipsToPhysicist = Setting::where('key', 'commission_for_using_the_service')->first()->value; // Комиссия банка за перевод чаевых физику (%)
        $compensationForAcquiring = $orderStatistic->total_amount * $compensationPercent / 100; // Компенсация Yagoda за эквайринг
        $sendMaster = $orderStatistic->tips_sum;
        $sendOrganization = $orderStatistic->total_amount - ($orderStatistic->total_amount * $compensationPercent / 100);
        $licenseFeeOfTheBankDot = ($sendOrganization + $sendMaster) * $licenseFeeOfTheBankDotPercent / 100; // Лицензионное вознагр. банка Точка (р.)
        $theBankCommissionForAcquiring = $itog * $bankCommissionAcquiringPercent / 100;

//        dd($order->full_amount);

        $totalPrice = $order->orderItems->sum('product_price');
        $discount = $totalPrice * ($order->discount / 100);
        $finalPrice = $this->fixed($totalPrice - $discount, 2, true);


        $percentage = $finalPrice * (Setting::where('key', 'acquiring_fee')->first()->value / 100);
        $result = $finalPrice - $percentage;
        $formattedResult = $this->fixed($result, 2, true);

        $profit = $order->full_amount - $order->full_amount * 2.3 / 100;
        $profit = $this->fixed(($profit - $formattedResult - $orderStatistic->tips_sum), 2, true);

        return [
            [
                'name' => 'Сумма заказа',
                'slug' => 'order_summ',
                'value' => $order->full_amount
            ],
            [
                'name' => 'Товары\услуги',
                'slug' => 'order_items',
                'value' => $finalPrice
            ],
            [
                'name' => 'Чаевые',
                'slug' => 'tips',
                'value' => $orderStatistic->tips_sum
            ],
            [
                'name' => 'Надбавка',
                'slug' => 'add',
                'value' => $orderStatistic->service_acquiring_commission
            ],
            [
                'name' => 'Экваринг банка (2.3%)',
                'slug' => 'equating',
                'value' => $this->fixed($order->full_amount * 2.3 / 100),
                'style' => 'margin-top: 10px'
            ],
            [
                'name' => 'Нужно отправить за товар (-' . Setting::where('key', 'acquiring_fee')->first()->value . '%)',
                'slug' => 'product_send',
                'value' => $formattedResult
            ],
            [
                'name' => 'Наша прибыль (р.)',
                'slug' => 'profit',
                'value' => $profit,
                'style' => 'font-weight: bold'
            ],
        ];

//        return [
//            [
//                'name' => 'Начисление Yagoda за сервис',
//                'slug' => 'service_commission',
//                'value' => $orderStatistic->service_commission
//            ],
//            [
//                'name' => 'Компенсация Yagoda за эквайринг',
//                'slug' => 'compensation_for_acquiring',
//                'value' => $compensationForAcquiring
//            ],
//            [
//                'name' => 'Комиссия Yagoda с чаевых',
//                'slug' => 'commission_from_tips',
//                'value' => 0
//            ],
//            [
//                'name' => 'Товары/услуги',
//                'slug' => 'total_amount',
//                'value' => $orderStatistic->total_amount
//            ],
//            [
//                'name' => 'Чаевые (%)',
//                'slug' => 'tips_percent',
//                'value' => $orderStatistic->tips_percent
//            ],
//            [
//                'name' => 'Чаевые (р.)',
//                'slug' => 'tips_sum',
//                'value' => $orderStatistic->tips_sum
//            ],
//            [
//                'name' => 'Начисление Yagoda за сервис',
//                'slug' => 'commission_from_tips',
//                'value' => $accrualForService
//            ],
//            [
//                'name' => 'Компенсация Yagoda за эквайринг',
//                'slug' => 'compensation_for_acquiring',
//                'value' => $compensationForAcquiring
//            ],
//            [
//                'name' => 'Итого сумма к оплате Клиентом (С галкой)',
//                'slug' => 'total_amount_with_checkbox',
//                'value' => $orderStatistic->total_amount + $orderStatistic->tips_sum + $accrualForService
//            ],
//            [
//                'name' => 'Итого сумма к оплате Клиентом (БЕЗ галки)',
//                'slug' => 'total_amount_without_checkbox',
//                'value' => $orderStatistic->total_amount + $orderStatistic->tips_sum
//            ],
//            [
//                'name' => 'Комиссия банка за эквайринг (р.)',
//                'slug' => 'bank_acquiring_fee',
//                'value' => $theBankCommissionForAcquiring
//            ],
//            [
//                'name' => 'Комиссия за перевод организации',
//                'slug' => 'organization_transfer_fee',
//                'value' => 0
//            ],
//            [
//                'name' => 'Лицензионное вознагр. банка Точка (%)',
//                'slug' => 'bank_tochka_license_fee',
//                'value' => $licenseFeeOfTheBankDotPercent
//            ],
//            [
//                'name' => 'Лицензионное вознагр. банка Точка (р.)',
//                'slug' => 'bank_tochka_license_fee_rub',
//                'value' => $licenseFeeOfTheBankDot
//            ],
//            [
//                'name' => 'Комиссия банка за перевод чаевых физику (%)',
//                'slug' => 'bank_commission_for_transferring_tips_to_physicist',
//                'value' => $bankCommissionForTransferringTipsToPhysicist
//            ],
//            [
//                'name' => 'Комиссия банка за перевод чаевых физику 1 (р.)',
//                'slug' => 'bank_tip_transfer_fee_physical_1_rub',
//                'value' => 50 * $bankCommissionForTransferringTipsToPhysicist / 100
//            ],
//            [
//                'name' => 'Комиссия банка за перевод чаевых физику 2 (р.)',
//                'slug' => 'bank_tip_transfer_fee_physical_2_rub',
//                'value' => 50 * $bankCommissionForTransferringTipsToPhysicist / 100
//            ],
//            [
//                'name' => 'Компенсация Yagoda за эквайринг Услуги',
//                'slug' => 'yagoda_acquiring_service_compensation',
//                'value' => $orderStatistic->total_amount * $compensationPercent / 100
//            ],
//            [
//                'name' => 'Отправляем организации',
//                'slug' => 'send_organization',
//                'value' => $sendOrganization
//            ],
//            [
//                'name' => 'Комиссия Yagoda с чаевых Услуги',
//                'slug' => 'yagoda_tip_commission_service',
//                'value' => 0
//            ],
//            [
//                'name' => 'Отправляем Мастеру',
//                'slug' => 'send_master',
//                'value' => $sendMaster
//            ],
//            [
//                'name' => 'Прибыль Yagoda от операции ВБРР',
//                'slug' => 'yagoda_profit_from_vbrr_operation',
//                'value' => 0
//            ],
//            [
//                'name' => 'Прибыль Yagoda от операции Точка',
//                'slug' => 'yagoda_profit_from_tochka_operation',
//                'value' => ($accrualForService + $compensationForAcquiring) - $licenseFeeOfTheBankDot - $theBankCommissionForAcquiring
//            ],
//        ];
    }
}
