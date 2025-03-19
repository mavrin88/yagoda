<?php

namespace App\Services\TochkaService;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TipsDistributor
{
    private $currentNumber = 10;
    private $totalTips;
    private $orderPracticants;
    private $masterCardNumber;
    private $organizationCardNumber;

    public function __construct($prepareIidentifyPayment)
    {
        $this->totalTips = $prepareIidentifyPayment['organization']['tips'];
        $this->orderPracticants = $prepareIidentifyPayment['organization']['orderPracticants'][0];
        $this->initializeCardNumbers();
    }

    private function initializeCardNumbers()
    {
        foreach ($this->orderPracticants as $practicant) {
            if ($practicant['role'] === 'master') {
                $this->masterCardNumber = $practicant['card_number'];
            }
            if ($practicant['role'] === 'organization') {
                $this->organizationCardNumber = $practicant['card_number'];
            }
        }
    }

    public function distributeTips()
    {
        $tipsDistribution = [];

        $roles = ['master', 'admin', 'employee', 'organization'];

        foreach ($roles as $role) {
            $participants = $this->filterParticipantsByRole($role);
            if (empty($participants)) {
                continue; // Пропускаем роли без участников
            }
            $tipsDistribution = array_merge($tipsDistribution, $this->calculateTipsForParticipants($participants, $role));
        }

        return $this->adjustTipsDistribution($tipsDistribution);
    }

    private function filterParticipantsByRole($role)
    {
        return array_filter($this->orderPracticants, function($practicant) use ($role) {
            return $practicant['role'] === $role;
        });
    }

    private function calculateTipsForParticipants($participants, $role)
    {
        $tipsForRole = [];
        $numberOfParticipants = count($participants);

        foreach ($participants as $participant) {
            $tip = $this->calculateTip($participant, $numberOfParticipants);
            $cardNumber = $this->getCardNumberForParticipant($participant);

            $tipsForRole[] = [
                "number" => $this->currentNumber,
                "type" => "payment_contract_to_card",
                "amount" => round($tip, 2),
                "card_number_crypto_base64" => $cardNumber,
                "id" => $participant['id'] ?? null,
                "roleId" => $participant['roleId'] ?? null,
                "role" => $participant['role'] ?? null,
                "original" => $participant
            ];

            $this->currentNumber++;
        }

        return $tipsForRole;
    }

    private function calculateTip($participant, $numberOfParticipants)
    {
        return $this->totalTips * ($participant['percent'] / 100) / $numberOfParticipants;
    }

    private function getCardNumberForParticipant($participant)
    {
        $cardNumber = $participant['card_number'] ?? null;

        if (empty($cardNumber)) {
            Log::info('Номер карты участника пуст. Используется masterCardNumber');
            $cardNumber = $this->masterCardNumber;
        }

        if (empty($cardNumber)) {
            Log::info('Номер карты все еще пуст. Используется organizationCardNumber');
            $cardNumber = $this->organizationCardNumber;
        }

        return !empty($cardNumber) ? $this->runScript($cardNumber) : null;
    }

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

    private function checkTipsDistribution(array $payments): array
    {
        // Сохраняем первый ключ исходного массива
        $firstElementKey = key($payments);
        $reservPayments = $payments;

        // Переменная для хранения суммы элементов с amount < 1
        $totalAmountToAdd = 0;

        // Проходим по массиву и убираем ненужные элементы
        foreach ($payments as $key => $item) {
            if ($item['type'] === 'payment_contract_to_card' && $item['amount'] < 1) {
                // Увеличиваем сумму для добавления
                $totalAmountToAdd += $item['amount'];
                // Удаляем элемент
                unset($payments[$key]);
            }
        }

        // Если есть сумма для добавления и массив не пустой, добавляем её к первому элементу
        if ($totalAmountToAdd > 0 && !empty($payments)) {
            reset($payments); // Убедимся, что указатель на первом элементе
            $firstElementKey = key($payments);
            $payments[$firstElementKey]['amount'] += $totalAmountToAdd;
        }

        // Если массив пустой, добавляем первый элемент из исходного массива с новой суммой
        if (empty($payments)) {
            $modifiedElement = $reservPayments[$firstElementKey] ?? null;

            if ($modifiedElement) {
                $modifiedElement['amount'] = $totalAmountToAdd;
                $payments[] = $modifiedElement;
                $payments = array_values($payments); // Переиндексируем массив
            }
        }

        return $payments;
    }


    public function runScript($card)
    {
        $decryptCard = Crypt::decryptString($card);

        $cleanedString = str_replace(" ", "", $decryptCard);

        $scriptPath = public_path('encrypt.py');

        $string =  shell_exec("python3 $scriptPath " . escapeshellarg($cleanedString) . " 2>&1");

        return str_replace("\n", "", $string);
    }
}
