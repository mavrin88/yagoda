<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderStatistics;
use App\Modules\YagodaTips\Models\OrderStatistics as OrderStatisticsYagodaTips;
use App\Modules\YagodaTips\Models\Order as OrderYagodaTips;
use App\Services\AtolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    private $atolService;

    public function __construct(AtolService $atolService)
    {
        $this->atolService = $atolService;
    }

    /**
     * Обработка входящего webhook-запроса
     *
     * @param Request $request Входящий HTTP-запрос
     * @return \Illuminate\Http\JsonResponse JSON-ответ с результатом обработки
     */
    public function webhook(Request $request)
    {
        $response = $this->decodeJwtWithoutSignature($request->getContent());
        $data = $response->getData(true);
        $this->processing($data);
        return response()->json(['success' => true], 200);
    }

    /**
     * Обработка данных из webhook в зависимости от типа
     *
     * @param array $data Данные из webhook
     * @return void
     */
    private function processing($data)
    {
        $webhookType = $data['webhookType'];

        switch ($webhookType) {
            case 'acquiringInternetPayment':
                $this->acquiringInternetPayment($data);
                break;
            case 'incomingPayment':
                break;
            case 'outgoingPayment':
                break;
        }
    }

    /**
     * Обработка интернет-платежа через эквайринг
     *
     * @param array $data Данные платежа
     * @return void
     */
    private function acquiringInternetPayment($data)
    {
        if (strpos($data['purpose'], 'tips_') === 0) {
            $this->handleTipsPayment($data);
        } else {
            $this->handleRegularPayment($data);
        }
    }

    /**
     * Обработка платежа с чаевыми
     *
     * @param array $data Данные платежа
     * @return void
     */
    private function handleTipsPayment($data)
    {
        $extractValueAfterColon = $this->extractValueAfterColon($data['purpose']);
        $orderStatistics = OrderStatisticsYagodaTips::where('uuid', $extractValueAfterColon)->first();

        if ($orderStatistics) {
            $order = OrderYagodaTips::find($orderStatistics->order_id);
            if ($order) {
                try {
                    DB::transaction(function () use ($order, $orderStatistics, $data) {
                        $order->update([
                            'status' => 'ok',
                            'tips' => $orderStatistics->tips_sum
                        ]);
                        $orderStatistics->update([
                            'status' => 'ok',
                            'qrcId' => $data['qrcId'] ?? null,
                            'webhook' => $data ?? null,
                            'payer_name' => $data['payerName'] ?? null,
                        ]);
                    });
                } catch (\Exception $e) {
                    \Log::error('Failed to update order status: ' . $e->getMessage());
                }

                if ($orderStatistics->service_acquiring_commission > 0) {
                    // ОТДЕЛЬНО от чаевых - чек на сумму Надбавки отправляем Клиенту на указанный им Эмэйл. Если Эмэйл не указал, то на Наш
                    $this->documentRegistrationTipsToClient($order, $orderStatistics);
                }

                if ($orderStatistics->fee_consent > 0) {
                    // из ЧАЕВЫХ - Кассовый чек на сумму Надбавки отправляем Мастеру на Эмэйл. Если у него эмэйла нет, то на наш эмэйл.
                    $this->documentRegistrationTipsToMaster($order, $orderStatistics);
                }
            }
        }
    }

    /**
     * Обработка обычного платежа (не чаевые)
     *
     * @param array $data Данные платежа
     * @return void
     */
    private function handleRegularPayment($data)
    {
        $extractValueAfterColon = $this->extractValueAfterColon($data['purpose']);
        $orderStatistics = OrderStatistics::where('uuid', $extractValueAfterColon)->first();

        if ($orderStatistics) {
            $order = Order::find($orderStatistics->order_id);
            if ($order) {
                try {
                    DB::transaction(function () use ($order, $orderStatistics, $data) {
                        $order->update([
                            'status' => 'ok',
                            'tips' => $orderStatistics->tips_sum
                        ]);
                        $orderStatistics->update([
                            'status' => 'ok',
                            'qrcId' => $data['qrcId'] ?? null,
                            'webhook' => $data ?? null,
                            'payer_name' => $data['payerName'] ?? null,
                        ]);
                    });
                } catch (\Exception $e) {
                    \Log::error('Failed to update order status: ' . $e->getMessage());
                }

                $this->pusherSendOrderStatus($order->id);
                sleep(2);
                $this->documentRegistrationAgents($order, $orderStatistics);
                sleep(2);
                $this->documentRegistrationClient($order, $orderStatistics);
                sleep(2);
                $this->documentRegistrationService($order, $orderStatistics);
            }
        }
    }

    /**
     * Декодирование JWT без проверки подписи
     *
     * @param string $jwt JWT-токен
     * @return \Illuminate\Http\JsonResponse Декодированные данные в формате JSON
     * @throws \Exception Если JWT некорректен
     */
    public function decodeJwtWithoutSignature($jwt)
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            throw new \Exception('Некорректный JWT');
        }

        $payload = $parts[1];
        $payloadData = json_decode($this->base64url_decode($payload), true);

        return response()->json($payloadData);
    }

    /**
     * Декодирование строки в формате base64url
     *
     * @param string $data Строка в формате base64url
     * @return string Декодированная строка
     */
    private function base64url_decode($data)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $data);

        switch (strlen($data) % 4) {
            case 2: $data .= '=='; break;
            case 3: $data .= '='; break;
        }

        return base64_decode($data);
    }

    /**
     * Извлечение значения после двоеточия из строки
     *
     * @param string $inputString Входная строка
     * @return string Извлеченное значение или исходная строка, если двоеточия нет
     */
    private function extractValueAfterColon($inputString)
    {
        if (strpos($inputString, ':') !== false) {
            $parts = explode(':', $inputString);
            return trim($parts[1]);
        }

        return trim($inputString);
    }

    /**
     * Проверка и выбор email для отправки чека
     *
     * @param Order $order Объект заказа
     * @param OrderStatistics $orderStatistics Статистика заказа
     * @return string Email для отправки чека
     */
    private function chechEmail($order, $orderStatistics)
    {
        $receiptEmail = $orderStatistics->email_receipt;
        $organizationEmail = $order->email;
        $organizationYagoda = 'receipt@yagoda.team';

        return $receiptEmail ?? $organizationEmail ?? $organizationYagoda;
    }

    /**
     * Регистрация документа для агентов
     *
     * @param Order $order Объект заказа
     * @param OrderStatistics $orderStatistics Статистика заказа
     * @return void
     */
    private function documentRegistrationAgents($order, $orderStatistics)
    {
        $email = $this->chechEmail($order, $orderStatistics);

        $data = [
            "orderTotal" => $order->total_amount,
            "orderItems" => $order->orderItems,
            "orderDiscount" => $order->discount ?? 0,
            "emailReceipt" => $email ?? 0,
            "organization" => $order->organization
        ];

        $this->atolService->documentRegistrationAgents($data);
    }

    /**
     * Регистрация документа для клиента
     *
     * @param Order $order Объект заказа
     * @param OrderStatistics $orderStatistics Статистика заказа
     * @return void
     */
    private function documentRegistrationClient($order, $orderStatistics)
    {
        $email = $this->chechEmail($order, $orderStatistics);

        if ($orderStatistics->service_acquiring_commission > 0) {
            $orderItems = [
                [
                    "product_name" => 'Компенсация транзакционных расходов',
                    "discounted_total" => $orderStatistics->service_acquiring_commission,
                    "quantity" => 1,
                    "measure" => 0,
                    "discounted_total" => $orderStatistics->service_acquiring_commission
                ]
            ];

            $data = [
                "orderTotal" => $orderStatistics->service_acquiring_commission,
                "orderItems" => $orderItems,
                "orderDiscount" => $order->discount ?? 0,
                "emailReceipt" => $email ?? 0,
                "organization" => $order->organization
            ];

            $this->atolService->documentRegistrationMy($data);
        }
    }

    /**
     * Регистрация документа для сервиса
     *
     * @param Order $order Объект заказа
     * @param OrderStatistics $orderStatistics Статистика заказа
     * @return void
     */
    private function documentRegistrationService($order, $orderStatistics)
    {
        $serviceAcquiringCommission = $orderStatistics->service_acquiring_commission;

        if ($orderStatistics->comp_source == 'tips') {
            $serviceAcquiringCommission = $orderStatistics->tips_sum - $orderStatistics->service_acquiring_commission;
        }

        if ($serviceAcquiringCommission) {
            $orderItems = [
                [
                    "product_name" => 'Компенсация транзакционных расходов',
                    "discounted_total" => $orderStatistics->service_acquiring_commission,
                    "quantity" => 1,
                    "measure" => 0,
                    "discounted_total" => $orderStatistics->service_acquiring_commission
                ]
            ];

            $data = [
                "orderTotal" => $orderStatistics->service_acquiring_commission,
                "orderItems" => $orderItems,
                "orderDiscount" => $order->discount ?? 0,
                "emailReceipt" => 'receipt@yagoda.team',
                "organization" => $order->organization
            ];

            $this->atolService->documentRegistrationMy($data);
        }
    }

    private function documentRegistrationTipsToClient($order, $orderStatistics)
    {
        $email = $orderStatistics->email_receipt ?? 'receipt@yagoda.team';

        $orderItems = [
            [
                "product_name" => 'Компенсация транзакционных расходов',
                "discounted_total" => $orderStatistics->service_acquiring_commission,
                "quantity" => 1,
                "measure" => 0,
                "discounted_total" => $orderStatistics->service_acquiring_commission
            ]
        ];

        $data = [
            "orderTotal" => $orderStatistics->service_acquiring_commission,
            "orderItems" => $orderItems,
            "orderDiscount" => 0,
            "emailReceipt" => $email ?? 0,
            "organization" => $order->organization
        ];

        $this->atolService->documentRegistrationMy($data);
    }

    private function documentRegistrationTipsToMaster($order, $orderStatistics)
    {
        $masters = $order->masters();
        $firstMaster = $masters->first();
        $email = $firstMaster ? $firstMaster->email : 'receipt@yagoda.team';

        $total = max($orderStatistics->tips_sum - $orderStatistics->fee_consent, 0);

        if ($total > 0) {
            $orderItems = [
                [
                    "product_name" => 'Компенсация транзакционных расходов',
                    "discounted_total" => $total,
                    "quantity" => 1,
                    "measure" => 0,
                    "discounted_total" => $total
                ]
            ];

            $data = [
                "orderTotal" => $total,
                "orderItems" => $orderItems,
                "orderDiscount" => 0,
                "emailReceipt" => $email ?? 0,
                "organization" => $order->organization
            ];

            $this->atolService->documentRegistrationMy($data);
        }
    }

    /**
     * Отправка статуса заказа через Pusher или событие Laravel
     *
     * @param int $orderId ID заказа
     * @return void
     */
    private function pusherSendOrderStatus($orderId): void {
        $status = 'ok';
        broadcast(new OrderStatusUpdated($orderId, $status));
    }
}
