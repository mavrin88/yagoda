<?php

namespace App\Services\TochkaPaymentProcessor\YagodaTipsPayments;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TipsDistributor
{
    private $currentNumber = 10;
    private $totalTips;
    private $orderPracticants;
    private $masterParticipant;
    private $organizationParticipant;

    /**
     * Конструктор класса.
     *
     * @param array $prepareIidentifyPayment Данные о платеже, включая чаевые и участников заказа.
     */
    public function __construct($prepareIidentifyPayment)
    {
        $this->totalTips = $prepareIidentifyPayment['organization']['tips'];
        $this->orderPracticants = $prepareIidentifyPayment['organization']['orderPracticants'][0];
        $this->initializeParticipants();
    }

    /**
     * Инициализирует данные мастера и организации из списка участников.
     * Сохраняет полные данные участников с ролями 'master' и 'organization'.
     */
    private function initializeParticipants()
    {
        foreach ($this->orderPracticants as $practicant) {
            if ($practicant['role'] === 'master') {
                $this->masterParticipant = $practicant;
            }
            if ($practicant['role'] === 'organization') {
                $this->organizationParticipant = $practicant;
            }
        }
    }

    /**
     * Распределяет чаевые между участниками и возвращает итоговый массив платежей.
     *
     * @return array Массив платежей с уникальными id и суммированными amount.
     */
    public function distributeTips()
    {
        $tipsDistribution = [];
        $totalDistributedPercentage = 0;

        $roles = array_unique(array_column($this->orderPracticants, 'role'));

        foreach ($roles as $role) {
            $participants = $this->filterParticipantsByRole($role);
            if (empty($participants)) {
                continue;
            }
            $rolePercentage = (float) $participants[array_key_first($participants)]['percent'];
            if ($rolePercentage == 0) {
                continue;
            }
            $totalDistributedPercentage += $rolePercentage;
            $tipsDistribution = array_merge($tipsDistribution, $this->calculateTipsForParticipants($participants, $role, $rolePercentage));
        }

        if ($totalDistributedPercentage < 100) {
            $remainingPercentage = 100 - $totalDistributedPercentage;
            $masterParticipants = $this->filterParticipantsByRole('master');
            if (!empty($masterParticipants)) {
                $remainingTips = $this->totalTips * ($remainingPercentage / 100);
                $tipsDistribution = $this->addRemainingTipsToMaster($tipsDistribution, $remainingTips, reset($masterParticipants));
            }
        }

        $adjustedTips = $this->adjustTipsDistribution($tipsDistribution);

        return $this->removeDuplicatesById($adjustedTips);
    }

    /**
     * Фильтрует участников по указанной роли.
     *
     * @param string $role Роль для фильтрации (например, 'master', 'organization').
     * @return array Отфильтрованный массив участников.
     */
    private function filterParticipantsByRole($role)
    {
        return array_filter($this->orderPracticants, function ($practicant) use ($role) {
            return $practicant['role'] === $role;
        });
    }

    /**
     * Рассчитывает чаевые для участников определённой роли.
     *
     * @param array $participants Список участников роли.
     * @param string $role Роль участников.
     * @param float $rolePercentage Процент чаевых для роли.
     * @return array Массив платежей для участников роли.
     */
    private function calculateTipsForParticipants($participants, $role, $rolePercentage)
    {
        $tipsForRole = [];
        $numberOfParticipants = count($participants);
        $roleTotalTips = $this->totalTips * ($rolePercentage / 100);

        foreach ($participants as $participant) {
            $tip = $roleTotalTips / $numberOfParticipants;
            $cardData = $this->getCardNumberForParticipant($participant);

            // Исправление 1: Используем оригинального участника, а не данные из cardData
            $originalParticipant = $participant;

            $tipsForRole[] = [
                "number" => $this->currentNumber,
                "type" => "payment_contract_to_card",
                "amount" => round($tip, 2),
                "card_number_crypto_base64" => $cardData['card_number'],
                "id" => $originalParticipant['id'] ?? null,
                "roleId" => $originalParticipant['roleId'] ?? null,
                "role" => $originalParticipant['role'] ?? null,
                "original" => $originalParticipant
            ];

            $this->currentNumber++;
        }

        return $tipsForRole;
    }

    /**
     * Добавляет остаток чаевых мастеру, если распределено менее 100%.
     *
     * @param array $tipsDistribution Текущий массив платежей.
     * @param float $remainingTips Сумма оставшихся чаевых.
     * @param array $master Данные мастера.
     * @return array Обновлённый массив платежей.
     */
    private function addRemainingTipsToMaster($tipsDistribution, $remainingTips, $master)
    {
        foreach ($tipsDistribution as &$payment) {
            if ($payment['role'] === 'master') {
                $payment['amount'] += $remainingTips;
                $payment['amount'] = round($payment['amount'], 2);
                break;
            }
        }

        return $tipsDistribution;
    }

    /**
     * Получает номер карты для участника, подставляя мастерскую или организационную при необходимости.
     *
     * @param array $participant Данные участника.
     * @return array Ассоциативный массив с 'card_number' (зашифрованный номер) и 'participant' (данные участника).
     */
    private function getCardNumberForParticipant($participant)
    {
        $cardNumber = $participant['card_number'] ?? null;

        // Исправление 2: Подменяем только card_number, не трогаем данные участника
        if (empty($cardNumber)) {
            Log::info('Номер карты участника пуст. Используется карта мастера');
            if (!empty($this->masterParticipant['card_number'])) {
                $cardNumber = $this->masterParticipant['card_number'];
            }
        }

        if (empty($cardNumber)) {
            Log::info('Номер карты все еще пуст. Используется карта организации');
            if (!empty($this->organizationParticipant['card_number'])) {
                $cardNumber = $this->organizationParticipant['card_number'];
            }
        }

        return [
            'card_number' => !empty($cardNumber) ? $this->runScript($cardNumber) : null,
            'participant' => $participant // Всегда возвращаем исходного участника
        ];
    }

    /**
     * Корректирует итоговую сумму чаевых, чтобы она соответствовала totalTips.
     *
     * @param array $tipsDistribution Текущий массив платежей.
     * @return array Скорректированный массив платежей.
     */
    private function adjustTipsDistribution($tipsDistribution)
    {
        $totalDistributedTips = array_sum(array_column($tipsDistribution, 'amount'));

        if ($totalDistributedTips < $this->totalTips) {
            $difference = $this->totalTips - $totalDistributedTips;
            $lastIndex = count($tipsDistribution) - 1;
            $tipsDistribution[$lastIndex]['amount'] += $difference;
        } elseif ($totalDistributedTips > $this->totalTips) {
            $excess = $totalDistributedTips - $this->totalTips;
            $lastIndex = count($tipsDistribution) - 1;
            $tipsDistribution[$lastIndex]['amount'] -= $excess;
        }

        return $this->checkTipsDistribution($tipsDistribution);
    }

    /**
     * Проверяет и убирает платежи с суммой менее 1, перераспределяя их сумму.
     *
     * @param array $payments Массив платежей для проверки.
     * @return array Обновлённый массив платежей.
     */
    private function checkTipsDistribution(array $payments): array
    {
        $firstElementKey = key($payments);
        $reservPayments = $payments;
        $totalAmountToAdd = 0;

        foreach ($payments as $key => $item) {
            if ($item['type'] === 'payment_contract_to_card' && $item['amount'] < 1) {
                $totalAmountToAdd += $item['amount'];
                unset($payments[$key]);
            }
        }

        if ($totalAmountToAdd > 0 && !empty($payments)) {
            reset($payments);
            $firstElementKey = key($payments);
            $payments[$firstElementKey]['amount'] += $totalAmountToAdd;
        }

        if (empty($payments) && $firstElementKey !== null) {
            $modifiedElement = $reservPayments[$firstElementKey];
            $modifiedElement['amount'] = $totalAmountToAdd;
            $payments[] = $modifiedElement;
            $payments = array_values($payments);
        }

        return $payments;
    }

    /**
     * Удаляет дубликаты по id, суммируя значения amount.
     * Добавляет сумму организации мастеру, если у организации нет карты.
     *
     * @param array $tipsDistribution Массив платежей с возможными дублями.
     * @return array Массив без дублей с обновлёнными номерами.
     */
    private function removeDuplicatesById(array $tipsDistribution): array
    {
        if (empty($tipsDistribution)) {
            return [];
        }

        $aggregated = [];
        $organizationAmount = 0;

        foreach ($tipsDistribution as $payment) {
            $id = $payment['id'] ?? null;

            // Исправление 3: Сохраняем сумму организации для добавления мастеру
            if ($payment['role'] === 'organization' && empty($this->organizationParticipant['card_number'])) {
                $organizationAmount += $payment['amount'];
                continue; // Пропускаем организацию, если у неё нет карты
            }

            if ($id === null) {
                $aggregated[] = $payment;
                continue;
            }

            if (!isset($aggregated[$id])) {
                $aggregated[$id] = $payment;
            } else {
                $aggregated[$id]['amount'] += $payment['amount'];
                $aggregated[$id]['amount'] = round($aggregated[$id]['amount'], 2);
            }
        }

        // Исправление 4: Добавляем сумму организации мастеру
        if ($organizationAmount > 0 && !empty($this->masterParticipant)) {
            $masterId = $this->masterParticipant['id'];
            if (isset($aggregated[$masterId])) {
                $aggregated[$masterId]['amount'] += $organizationAmount;
                $aggregated[$masterId]['amount'] = round($aggregated[$masterId]['amount'], 2);
            }
        }

        $result = array_values($aggregated);

        $currentNumber = 10;
        foreach ($result as &$item) {
            $item['number'] = $currentNumber++;
        }

        return $result;
    }

    /**
     * Шифрует номер карты с помощью внешнего Python-скрипта.
     *
     * @param string $card Зашифрованный номер карты (в формате строки).
     * @return string Зашифрованный номер карты в base64.
     */
    public function runScript($card)
    {
        $decryptCard = Crypt::decryptString($card);
        $cleanedString = str_replace(" ", "", $decryptCard);
        $scriptPath = public_path('encrypt.py');
        $string = shell_exec("python3 $scriptPath " . escapeshellarg($cleanedString) . " 2>&1");
        return str_replace("\n", "", $string);
    }
}
