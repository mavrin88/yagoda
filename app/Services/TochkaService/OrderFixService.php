<?php

namespace app\Services\TochkaService;
use App\Models\OrderStatistics;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderFixService
{
    // Метод для получения вчерашних заказов
    public function getYesterdayOrders()
    {
        return OrderStatistics::where('status', 'ok')
            ->where('order_id', '>=', 1406)
            ->whereNull('processed_at')
            ->where('type_pay', 'card')
            ->get();
    }

    // Основной метод для обработки заказов
    public function processOrders($morningAmount)
    {
        $orders = $this->getYesterdayOrders();
        $accumulatedAmount = 0;
        $lastOrderAmount = 0;
        $processedOrderIds = [];

        // Вычисляем допустимый нисходящий допуск
        $totalOrders = $orders->count();
        $allowedDiscrepancy = ($totalOrders / 3) * 0.05;


        // Допустимые границы
        $allowedMax = $morningAmount + $allowedDiscrepancy;
        $allowedMin = $morningAmount - $allowedDiscrepancy;

        foreach ($orders as $order) {
            $lastOrderAmount = $order->full_amount;
            $accumulatedAmount += $lastOrderAmount;
            $processedOrderIds[] = $order->order_id;
            $processedOrderData[] = ['order_id' => $order->order_id, 'lastOrderAmount' => $lastOrderAmount];
            // Если накопленная сумма превышает допустимый максимум
            if ($accumulatedAmount > $allowedMax) {
                $accumulatedAmountMinusDiscrepancy = $accumulatedAmount - $allowedDiscrepancy;
                // Проверяем, можно ли остановиться
                if ($accumulatedAmountMinusDiscrepancy <= $allowedMax) {
                    // Останавливаемся и корректируем копейки
                    $processedOrderData[] = ['totalSumm' => $this->adjustCents($accumulatedAmount)];
                    return $processedOrderIds;
                    // return $this->adjustCents($accumulatedAmount);
                    // break;
                } else {
                    // Пробуем убрать последний заказ
                    $accumulatedAmountMinusLastOrder = $accumulatedAmount - $lastOrderAmount;

                    // Проверяем, попадает ли сумма в допустимый минимум
                    if ($accumulatedAmountMinusLastOrder >= $allowedMin) {
                        // Останавливаемся и корректируем копейки
                        array_splice($processedOrderIds, -1);
                        array_splice($processedOrderData, -1);
                        $processedOrderData[] = ['totalSumm' => $this->adjustCents($accumulatedAmountMinusLastOrder)];
                        return $processedOrderIds;
                    } else {
                        // Если сумма меньше допустимого минимума, бьем тревогу
                        return $this->raiseAlarm();
                    }
                }
            }
        }

        $processedOrderData[] = ['totalSumm' => $this->adjustCents($accumulatedAmount)];
        // dd($processedOrderData);
        return $processedOrderIds;
    }

    // Метод для корректировки копеек
    private function adjustCents($amount)
    {
        // Логика корректировки копеек
        // Например, округление до ближайшего целого
        return round($amount, 2);
    }

    // Метод для вызова тревоги
    private function raiseAlarm()
    {
        // Логика обработки тревоги
        // Например, отправка уведомления или логирование
        dd('Обнаружен значительный дисбаланс в заказах!');
        Log::error('Обнаружен значительный дисбаланс в заказах!');
    }
}